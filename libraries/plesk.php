<?php
namespace Addon\Plesk\Libraries;
use \Addon\Plesk\Libraries\api as Api;

class Plesk
{
    public $server;
    public $server_group;
    public $server_module; 

    public $hosting;
    public $cmd;

    public function initServer($server, $server_group, $server_module) {

        $this->server = $server;
        $this->server_group = $server_group;
        $this->server_module = $server_module;
    }

    public function updateRemote($purchase_id)
    {
        // Load the account and server details
        $purchase = \ProductPurchase::find($purchase_id);
        $hosting = $purchase->Hosting()->first();


        if (!empty($hosting) && $this->checkServer($hosting->server_id)) {

            if ($hosting->last_sync < (time()-3600)) {
                $this->loadAccount($hosting->id);

                $service = $this->retrieveService($hosting->id);

                if (!$service) {
                    return false;
                }

                if($service['type'] == 'reseller') {

                    // For resellers we can update the limits at the same time
                    // as we pull the usage stats.

                    $disk_usage = $service['data']->data->stat->{'disk-space'};
                    $disk_usage = round($disk_usage / (1024 * 1024), 2);

                    $hosting->diskspace_usage = preg_replace("/[^0-9,.]/", "", $disk_usage);

                    $traffic_usage = $service['data']->data->stat->{'traffic'};
                    $traffic_usage = round($traffic_usage / (1024 * 1024), 2);

                    $hosting->bandwidth_usage = preg_replace("/[^0-9,.]/", "", $traffic_usage);

                    $limits = array();
                    foreach ($service['data']->data->limits->limit as $limit) {
                        $limits[end($limit->name)] = end($limit->value);
                    }

                    // Set diskspace limit
                    $hosting->diskspace_limit = round($limits['disk_space'] / (1024 * 1024), 2);

                    // Set bandwidth limit
                    $hosting->bandwidth_limit = round($limits['max_traffic'] / (1024 * 1024), 2);


                } else {
                    $disk_usage = 0;

                    foreach((array)$service['data']->data->disk_usage->httpdocs as $key => $usage) {
                        $disk_usage = $disk_usage + $usage;
                    }

                    $disk_usage = round($disk_usage / (1024 * 1024), 2);

                    $hosting->diskspace_usage = preg_replace("/[^0-9,.]/", "", $disk_usage);

                    $this->serverConnection($hosting->server_id);

                    $sites_api = new Api\Sites();
                    $site_traffic_data = $sites_api->getTrafficByDomain($hosting->domain);
                    $traffic_response = $this->cmd->parseResponse($this->cmd->sendRequest($site_traffic_data->saveXML()));
                    if (isset($traffic_response->site->get_traffic->result->traffic)) {

                        $traffic_data = $traffic_response->site->get_traffic->result->traffic;
                        unset($traffic_data->date);
                        $total_usage = 0;

                        foreach((array)$traffic_data as $key => $usage) {
                            $total_usage = $total_usage + $usage;
                        }

                        $hosting->bandwidth_usage = round($total_usage / (1024*1024), 2);

                    } else {
                        $hosting->bandwidth_usage = 0;
                    }
                }

                $hosting->last_sync = time();
                $hosting->save();
            }
            return true;
        }
    }

    public function addAddon($product_addon_id, $addon_purchase_id, $purchase_id)
    {
        $purchase = \ProductPurchase::find($purchase_id);
        $hosting = $purchase->Hosting()->first();

        $this->loadAccount($hosting->id);

        return true;
    }

    public function updateAddon($product_addon_id, $addon_purchase_id, $purchase_id)
    {
        $purchase = \ProductPurchase::find($purchase_id);
        $hosting = $purchase->Hosting()->first();

        $this->loadAccount($hosting->id);

        return true;
    }

    public function deleteAddon($product_addon_id, $addon_purchase_id, $purchase_id)
    {
        $purchase = \ProductPurchase::find($purchase_id);
        $hosting = $purchase->Hosting()->first();

        $this->loadAccount($hosting->id);

        return true;
    }

    private function loadAccount($hosting_id)
    {
        // Load the hosting package
        $this->hosting = \Hosting::find($hosting_id);

        // Load the server
        $this->server = $this->hosting->Server()->first();

        // Load the server group
        $this->server_group = $this->server->ServerGroup()->first();
    }

    public function testConnection($server_data)
    {
        // Check the correct details have been provided.
        if (!isset($server_data['Server']['main_ip']) || $server_data['Server']['main_ip'] == '' ||
            !isset($server_data['Server']['username']) || $server_data['Server']['username'] == '' ||
            !isset($server_data['Server']['password']) || $server_data['Server']['password'] == '') {
            return false;
        }

        try {
            $api = new Api\Xmlapi($server_data['Server']['main_ip'], $server_data['Server']['username'], $server_data['Server']['password']);

            $plans_api = new Api\Plans();
            $request = $api->sendRequest($plans_api->allPlans()->saveXML());
            $response = $api->parseResponse($request);

            if (! isset($response->{'service-plan'}->get)) {
                return false;
            }

        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public function serverConnection()
    {
        $this->cmd = new Api\Xmlapi($this->server->main_ip, \App::get('security')->decrypt($this->server->username), \App::get('security')->decrypt($this->server->password));
    }

    public function productFields()
    {
        $this->serverConnection();

        $forms = \App::factory('\Whsuite\Forms\Forms');

        $form = '';

        $plans_api = new Api\Plans();
        $request = $this->cmd->sendRequest($plans_api->allPlans()->saveXML());
        $response = $this->cmd->parseResponse($request);
        $shared_packages = $response->{"service-plan"}->get->result;
        $reseller_packages = $response->{"reseller-plan"}->get->result;
        $package_list = array();

        foreach ($shared_packages as $pkg) {
            $pkg_name = (string)$pkg->name;
            $package_list['shared_'.$pkg_name] = $pkg_name.' (Shared)';
        }

        foreach ($reseller_packages as $pkg) {
            $pkg_name = (string)$pkg->name;
            $package_list['reseller_'.$pkg_name] = $pkg_name.' (Reseller)';
        }
        $form .= $forms->select('PackageMeta.plesk_plan', \App::get('translation')->get('package'), array('options' => $package_list));

        echo $form;
    }


    public function productPaid($item)
    {
        return;
    }

    public function createService($purchase, $hosting)
    {
        $product = $purchase->Product()->first();
        $product_data = $product->ProductData()->get();

        $service_fields = array();

        $security = \App::get('security');

        foreach ($product_data as $p_data) {
            $service_fields[$p_data->slug] = $p_data->value;
        }

        if (! isset($service_fields['plesk_plan']) || $service_fields['plesk_plan'] == '') {
            return false;
        }

        // Extract the plan type and plan name
        $plan = explode("_", $service_fields['plesk_plan'], 2);
        $plan_type = $plan[0];
        $plan_name = $plan[1];

        // Add user/client
        $client = $purchase->Client()->first();

        $username = $this->generateClientUsername($client->first_name, $client->last_name);
        $password = $security->decrypt($hosting->password);

        $this->serverConnection();

        if (isset($plan_type) && $plan_type == 'shared') {
            // It's a shared hosting plan.

            $users_api = new Api\Users();
            $user_data = $users_api->createUser($client, $username, $password);
            $response = $this->cmd->parseResponse($this->cmd->sendRequest($user_data->saveXML()));

            if (isset($response->customer->add->result->status) && $response->customer->add->result->status == 'ok') {
                $user_id = $response->customer->add->result->id;

                $this->serverConnection();

                $sites_api = new Api\Sites();
                $site_data = $sites_api->createSite($hosting->domain, $user_id, $username, $password, $plan_name, $this->server->main_ip);
                $response = $this->cmd->parseResponse($this->cmd->sendRequest($site_data->saveXML()));

                if (isset($response->webspace->add->result->status) && $response->webspace->add->result->status == 'ok') {

                    $hosting->username = $username;

                    $hosting->save();

                    return true;
                }
            }

        } elseif (isset($plan_type) && $plan_type == 'reseller') {
            // It's a reseller hosting plan.

            $resellers_api = new Api\Resellers();
            $reseller_data = $resellers_api->createReseller($client, $username, $password, $plan_name);
            $response = $this->cmd->parseResponse($this->cmd->sendRequest($reseller_data->saveXML()));

            if (isset($response->reseller->add->result->status) && $response->reseller->add->result->status == 'ok') {

                // We need to store the reseller ID otherwise we have absolutely
                // no way of knowing which reseller account this purchase relates
                // to.
                $field_slug = 'plesk_reseller_'.$purchase->id;
                $data_group = \DataGroup::where('slug', '=', 'plesk_purchase_data')->first();

                $data_field = \DataField::where('data_group_id', '=', $data_group->id)->where('slug', '=', $field_slug)->first();
                if ($data_field) {
                    // Looks like we've already stored a reseler ID in the past,
                    // but now need to overwrite it.
                    $data_value = \DataFieldValue::where('data_field_id', '=', $data_field->id)->get();

                    $data_value->delete();
                    $data_field->delete();
                }

                $data_field = new \DataField();
                $data_field->slug = $field_slug;
                $data_field->data_group_id = $data_group->id;
                $data_field->title = $field_slug;
                $data_field->type = 'text';
                $data_field->save();

                $data_value = new \DataFieldValue();
                $data_value->data_field_id = $data_field->id;
                $data_value->value = $security->encrypt($response->reseller->add->result->id);
                $data_value->save();

                $hosting->username = $username;
                $hosting->save();

                return true;
            }
        }

        return false;

    }

    public function renewService($hosting_id)
    {
        // Plesk accounts dont need to do anything here.
        return true;
    }

    public function terminateService($purchase, $hosting)
    {
        $this->serverConnection();

        $service = $this->retrieveService($hosting->id);

        $this->serverConnection();

        if (! $service) {
            return false;
        }

        if($service['type'] == 'reseller') {

            $resellers_api = new Api\Resellers();
            $reseller_data = $resellers_api->terminateReseller($service['data']->id);
            $response = $this->cmd->parseResponse($this->cmd->sendRequest($reseller_data->saveXML()));

            if (isset($response->reseller->del->result->status) && $response->reseller->del->result->status == 'ok') {
                return true;
            }

        } else {
            $sites_api = new Api\Sites();
            $site_data = $sites_api->terminateSite($hosting->domain);
            $response = $this->cmd->parseResponse($this->cmd->sendRequest($site_data->saveXML()));

            if (isset($response->site->del->result->status) && $response->site->del->result->status == 'ok') {
                return true;
            }
        }
        return false;
    }

    public function suspendService($purchase, $hosting)
    {
        $this->serverConnection();
        $service = $this->retrieveService($hosting->id);

        $this->serverConnection();

        if (! $service) {
            return false;
        }

        if($service['type'] == 'reseller') {

            $resellers_api = new Api\Resellers();
            $reseller_data = $resellers_api->suspendReseller($service['data']->id);
            $response = $this->cmd->parseResponse($this->cmd->sendRequest($reseller_data->saveXML()));

            if (isset($response->reseller->set->result->status) && $response->reseller->set->result->status == 'ok') {
                return true;
            }

        } else {
            $sites_api = new Api\Sites();
            $site_data = $sites_api->suspendDomain($hosting->domain);
            $response = $this->cmd->parseResponse($this->cmd->sendRequest($site_data->saveXML()));

            if (isset($response->domain->set->result->status) && $response->domain->set->result->status == 'ok') {
                return true;
            }
        }
        return false;
    }

    public function unsuspendService($purchase, $hosting)
    {
        $this->serverConnection();
        $service = $this->retrieveService($hosting->id);

        $this->serverConnection();

        if (! $service) {
            return false;
        }

        if($service['type'] == 'reseller') {

            $resellers_api = new Api\Resellers();
            $reseller_data = $resellers_api->unsuspendReseller($service['data']->id);
            $response = $this->cmd->parseResponse($this->cmd->sendRequest($reseller_data->saveXML()));

            if (isset($response->reseller->set->result->status) && $response->reseller->set->result->status == 'ok') {
                return true;
            }
            
        } else {
            $sites_api = new Api\Sites();
            $site_data = $sites_api->unsuspendDomain($hosting->domain);
            $response = $this->cmd->parseResponse($this->cmd->sendRequest($site_data->saveXML()));

            if (isset($response->domain->set->result->status) && $response->domain->set->result->status == 'ok') {
                return true;
            }
        }
        return false;
    }

    public function retrieveService($hosting_id)
    {
        $hosting = \Hosting::find($hosting_id);
        $purchase = $hosting->ProductPurchase()->first();
        $product = $purchase->Product()->first();
        $product_data = $product->ProductData()->get();

        $product_data = $product->ProductData()->get();

        $server = $hosting->Server()->first();

        $service_fields = array();

        $security = \App::get('security');

        foreach ($product_data as $p_data) {
            $service_fields[$p_data->slug] = $p_data->value;
        }

        if (!isset($service_fields['plesk_plan']) || $service_fields['plesk_plan'] == '') {
            return false;
        }

        // Extract the plan type and plan name
        $plan = explode("_", $service_fields['plesk_plan'], 2);
        $plan_type = $plan[0];
        $plan_name = $plan[1];


        $this->serverConnection();

        $return_data = array();

        if ($plan_type == 'shared') {
            $return_data['type'] = 'shared';

            $sites_api = new Api\Sites();
            $site_data = $sites_api->getSiteByDomain($hosting->domain);
            $response = $this->cmd->parseResponse($this->cmd->sendRequest($site_data->saveXML()));

            $site = $response->site->get->result;

            $return_data['data'] = $site;

            if ($site->status == 'ok') {
                return $return_data;
            } else {
                return false;
            }
        } elseif ($plan_type == 'reseller') {
            $return_data['type'] = 'reseller';

            // Because plesk's reseller packages dont actually create a domain
            // based account, we've got no way of getting out the reseller details
            // using the domain name. So we're using a custom data group to store
            // a link between the purchase id, and the reseller ID that's provided by
            // plesk. So we're first going to retrieve the reseller ID from the
            // data storage.
            $field_slug = 'plesk_reseller_'.$purchase->id;
            $data_group = \DataGroup::where('slug', '=', 'plesk_purchase_data')->first();
            $data_field = \DataField::where('data_group_id', '=', $data_group->id)->where('slug', '=', $field_slug)->first();

            if (!$data_field)  {
                // The plan was never created, so we'll stop here.
                return false;
            }

            $data_value = \DataFieldValue::where('data_field_id', '=', $data_field->id)->first();

            $reseller_id = $this->getResellerId($purchase->id);
            if (!$reseller_id) {
                return false;
            }

            $resellers_api = new Api\Resellers();
            $reseller_data = $resellers_api->getReseller($reseller_id);
            $response = $this->cmd->parseResponse($this->cmd->sendRequest($reseller_data->saveXML()));

            $reseller = $response->reseller->get->result;

            $return_data['data'] = $reseller;

            if($reseller->status == 'ok') {
                return $return_data;
            } else {
                return false;
            }
        }
    }

    public function serverDetails()
    {
        $this->serverConnection();

        $server_details = array();

        $generic_api = new Api\Generic();
        $server_info = $generic_api->serverInfo();
        $response = $this->cmd->parseResponse($this->cmd->sendRequest($server_info->saveXML()));

        $server = $response->server->get->result;

        if(! isset($server->status) || $server->status == 'error') {
            throw new \Exception ('Permission Denied');
        }
      
        $server_details['hostname'] = end($server->gen_info->server_name);

        return $server_details;
    }

    public function rebootServer($server_id)
    {
        $this->serverConnection($server_id);

        $service_api = new Api\Services();
        $restart_service = $service_api->rebootServer();
        $response = $this->cmd->parseResponse($this->cmd->sendRequest($restart_service->saveXML()));

        if (isset($response->server->reboot->result->status) && $response->server->reboot->result->status == 'ok') {
            return true;
        }
        return false;
    }

    public function restartService($server_id, $service)
    {
        $this->serverConnection($server_id);

        $service_api = new Api\Services();
        $restart_service = $service_api->serviceAction('restart', $service);
        $response = $this->cmd->parseResponse($this->cmd->sendRequest($restart_service->saveXML()));

        if (isset($response->server->srv_man->result->status) && $response->server->srv_man->result->status == 'ok') {
            return true;
        }
        return false;
    }

    private function getResellerId($purchase_id)
    {
        $security = \App::get('security');

        $field_slug = 'plesk_reseller_'.$purchase_id;
        $data_group = \DataGroup::where('slug', '=', 'plesk_purchase_data')->first();
        $data_field = \DataField::where('data_group_id', '=', $data_group->id)->where('slug', '=', $field_slug)->first();

        if (!$data_field)  {
            // The plan was never created, so we'll stop here.
            return false;
        }

        $data_value = \DataFieldValue::where('data_field_id', '=', $data_field->id)->first();

        return $security->decrypt($data_value->value);
    }

    private function setResellerId($reseller_id, $purchase_id)
    {
        $security = \App::get('security');

        $field_slug = 'plesk_reseller_'.$purchase_id;
        $data_group = \DataGroup::where('slug', '=', 'plesk_purchase_data')->first();
        $data_field = \DataField::where('data_group_id', '=', $data_group->id)->where('slug', '=', $field_slug)->first();

        if($data_field) {
            // Looks like we've already stored a reseller ID in the past,
            // but now need to overwrite it.
            $data_value = \DataFieldValue::where('data_field_id', '=', $data_field->id)->get();

            $data_value->delete();
            $data_field->delete();
        }

        $data_field = new \DataField();
        $data_field->slug = $field_slug;
        $data_field->data_group_id = $data_group->id;
        $data_field->title = $field_slug;
        $data_field->type = 'text';
        $data_field->save();

        $data_value = new \DataFieldValue();
        $data_value->data_field_id = $data_field->id;
        $data_value->value = $security->encrypt($reseller_id);
        $data_value->save();
    }

    /**
     * Format Bytes (Src: http://php.net/manual/de/function.filesize.php)
     */
    private function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    private function checkServer($server_id) {
        $server = \Server::find($server_id);

        if ($server) {
            $group = $server->ServerGroup()->first();
            $module = $group->ServerModule()->first();

            if ($module->slug == 'plesk') {
                return true;
            }
        }
        return false;
    }

    private function generateClientUsername($first_name, $last_name) {

        $first_name = preg_replace("/[^a-zA-Z0-9]+/", "", $first_name);
        $last_name = preg_replace("/[^a-zA-Z0-9]+/", "", $last_name);

        $username_string = substr($first_name, 0, 1);
        $username_string.= $last_name;

        $username_string = strtolower($username_string);

        // At this point the username is the first letter of the users firstname
        // plus thier last name. We're now going to shorten the string to 10
        // characters (if needed), then add a random number to the end. This will provide a
        // sufficiently unique username, exactly 14 characters long.
        $username_string = substr($username_string, 0, 10);

        $max_length = 14;
        $current_length = strlen($username_string);
        $digits_length = $max_length - $current_length;

        // Generate a random number.
        $number = str_pad(rand(0, pow(10, $digits_length)-1), $digits_length, '0', STR_PAD_LEFT);

        $username_string .= $number;

        return $username_string;
    }
}
