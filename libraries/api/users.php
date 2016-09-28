<?php
namespace Addon\Plesk\Libraries\api;
/**
 * Plesk XML API - User Calls
 *
 * User related API requests such as creating and retrieving user/client info.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2014, Turn 24 Ltd.
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class Users
{

    public function createUser($user, $username, $password)
    {
        $xmldoc = new \DomDocument('1.0', 'UTF-8');
        $xmldoc->formatOutput = true;

        $packet = $xmldoc->createElement('packet');
        $packet->setAttribute('version', '1.6.3.0');
        $xmldoc->appendChild($packet);

        $customer = $xmldoc->createElement('customer');
        $packet->appendChild($customer);

        $add = $xmldoc->createElement('add');
        $customer->appendChild($add);

        $gen_info = $xmldoc->createElement('gen_info');
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

        return $xmldoc;
    }

}