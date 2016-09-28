<?php

class PleskController extends ClientController
{
    public $api;

    public function onLoad()
    {
        parent::onLoad();

        $this->api = App::factory('\Addon\Plesk\Libraries\Plesk');
    }

    public function manageHosting($id)
    {
        if ($this->logged_in) {
            $purchase = ProductPurchase::find($id);

            if ($this->client->id === $purchase->client_id) {
                $hosting = $purchase->Hosting()->first();
                $server = $hosting->Server()->first();
                $server_group = $server->ServerGroup()->first();
                $server_module = $server_group->ServerModule()->first();

                $this->api->initServer($server, $server_group, $server_module);

                $account = $this->api->retrieveService($hosting->id);

                $this->view->set('service', $purchase);

                if (!$account) {
                    // Account does not exist.
                    $this->view->display('plesk::client/no-account.php');
                } else {
                    $this->view->set('account', $account['data']);

                    if ($account['type'] == 'reseller') {
                        $this->view->display('plesk::client/manage-reseller-account.php');
                    } else {
                        $this->view->display('plesk::client/manage-account.php');
                    }
                }
            }
        }
    }
}
