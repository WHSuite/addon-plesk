<?php
namespace Addon\Plesk\Libraries\api;
/**
 * Plesk XML API - Reseller Calls
 *
 * Reseller related API requests such as creating and retrieving reseller accounts.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2014, Turn 24 Ltd.
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class Resellers
{

    public function createReseller($user, $username, $password, $plan)
    {
        $xmldoc = new \DomDocument('1.0', 'UTF-8');
        $xmldoc->formatOutput = true;

        $packet = $xmldoc->createElement('packet');
        $packet->setAttribute('version', '1.6.3.0');
        $xmldoc->appendChild($packet);

        $reseller = $xmldoc->createElement('reseller');
        $packet->appendChild($reseller);

        $add = $xmldoc->createElement('add');
        $reseller->appendChild($add);

        $gen_info = $xmldoc->createElement('gen-info');
        $add->appendChild($gen_info);

        $cname = $xmldoc->createElement('cname', $user->company);
        $gen_info->appendChild($cname);

        $pname = $xmldoc->createElement('pname', $user->first_name.' '.$user->last_name);
        $gen_info->appendChild($pname);

        $login = $xmldoc->createElement('login', $username);
        $gen_info->appendChild($login);

        $passwd = $xmldoc->createElement('passwd', $password);
        $gen_info->appendChild($passwd);

        $status = $xmldoc->createElement('status', '0');
        $gen_info->appendChild($status);

        $phone = $xmldoc->createElement('phone', $user->phone);
        $gen_info->appendChild($phone);

        $email = $xmldoc->createElement('email', $user->email);
        $gen_info->appendChild($email);

        $address = $xmldoc->createElement('address', $user->address1.' '.$user->address2);
        $gen_info->appendChild($address);

        $city = $xmldoc->createElement('city', $user->city);
        $gen_info->appendChild($city);

        $state = $xmldoc->createElement('state', $user->state);
        $gen_info->appendChild($state);

        $pcode = $xmldoc->createElement('pcode', $user->postcode);
        $gen_info->appendChild($pcode);

        $plan_name = $xmldoc->createElement('plan-name', $plan);
        $add->appendChild($plan_name);

        return $xmldoc;
    }

    public function getReseller($reseller_id)
    {
        $xmldoc = new \DomDocument('1.0', 'UTF-8');
        $xmldoc->formatOutput = true;

        $packet = $xmldoc->createElement('packet');
        $packet->setAttribute('version', '1.6.3.0');
        $xmldoc->appendChild($packet);

        $reseller = $xmldoc->createElement('reseller');
        $packet->appendChild($reseller);

        $get = $xmldoc->createElement('get');
        $reseller->appendChild($get);

        $filter = $xmldoc->createElement('filter');
        $get->appendChild($filter);

        $id = $xmldoc->createElement('id', $reseller_id);
        $filter->appendChild($id);

        $dataset = $xmldoc->createElement('dataset');
        $get->appendChild($dataset);

        $gen_info = $xmldoc->createElement('gen-info');
        $dataset->appendChild($gen_info);

        $stat = $xmldoc->createElement('stat');
        $dataset->appendChild($stat);

        $permissions = $xmldoc->createElement('permissions');
        $dataset->appendChild($permissions);

        $limits = $xmldoc->createElement('limits');
        $dataset->appendChild($limits);

        $ippool = $xmldoc->createElement('ippool');
        $dataset->appendChild($ippool);

        return $xmldoc;
    }

    public function suspendReseller($reseller_id)
    {
        return $this->setResellerStatus($reseller_id, 16);
    }

    public function unsuspendReseller($reseller_id)
    {
        return $this->setResellerStatus($reseller_id, 0);
    }

    public function setResellerStatus($reseller_id, $status_id)
    {
        $xmldoc = new \DomDocument('1.0', 'UTF-8');
        $xmldoc->formatOutput = true;

        $packet = $xmldoc->createElement('packet');
        $packet->setAttribute('version', '1.6.0.1');
        $xmldoc->appendChild($packet);

        $reseller = $xmldoc->createElement('reseller');
        $packet->appendChild($reseller);

        $set = $xmldoc->createElement('set');
        $reseller->appendChild($set);

        $filter = $xmldoc->createElement('filter');
        $set->appendChild($filter);

        $id = $xmldoc->createElement('id', $reseller_id);
        $filter->appendChild($id);

        $values = $xmldoc->createElement('values');
        $set->appendChild($values);

        $gen_info = $xmldoc->createElement('gen-info');
        $values->appendChild($gen_info);

        $status = $xmldoc->createElement('status', $status_id);
        $gen_info->appendChild($status);

        return $xmldoc;
    }

    public function terminateReseller($reseller_id)
    {
        $xmldoc = new \DomDocument('1.0', 'UTF-8');
        $xmldoc->formatOutput = true;

        $packet = $xmldoc->createElement('packet');
        $packet->setAttribute('version', '1.6.3.0');
        $xmldoc->appendChild($packet);

        $reseller = $xmldoc->createElement('reseller');
        $packet->appendChild($reseller);

        $del = $xmldoc->createElement('del');
        $reseller->appendChild($del);

        $filter = $xmldoc->createElement('filter');
        $del->appendChild($filter);

        $id = $xmldoc->createElement('id', $reseller_id);
        $filter->appendChild($id);

        return $xmldoc;
    }

}