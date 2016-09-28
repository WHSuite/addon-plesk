<?php
namespace Addon\Plesk\Libraries\api;
/**
 * Plesk XML API - Service Calls
 *
 * Service related API requests such a restarting MySQL and rebooting the server.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2014, Turn 24 Ltd.
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class Services
{
    function serviceAction($action, $service)
    {
        $xmldoc = new \DomDocument('1.0', 'UTF-8');
        $xmldoc->formatOutput = true;

        $packet = $xmldoc->createElement('packet');
        $packet->setAttribute('version', '1.4.2.0');
        $xmldoc->appendChild($packet);

        $server = $xmldoc->createElement('server');
        $packet->appendChild($server);

        $srv_man = $xmldoc->createElement('srv_man');
        $server->appendChild($srv_man);

        $id = $xmldoc->createElement('id', $service);
        $srv_man->appendChild($id);

        $operation = $xmldoc->createElement('operation', $action);
        $srv_man->appendChild($operation);

        return $xmldoc;
    }

    function rebootServer()
    {
        $xmldoc = new \DomDocument('1.0', 'UTF-8');
        $xmldoc->formatOutput = true;

        $packet = $xmldoc->createElement('packet');
        $packet->setAttribute('version', '1.6.3.3');
        $xmldoc->appendChild($packet);

        $server = $xmldoc->createElement('server');
        $packet->appendChild($server);

        $reboot = $xmldoc->createElement('reboot');
        $server->appendChild($reboot);

        return $xmldoc;
    }
}