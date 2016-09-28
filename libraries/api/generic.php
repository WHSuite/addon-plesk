<?php
namespace Addon\Plesk\Libraries\api;
/**
 * Plesk XML API - Generic Calls
 *
 * Generic API requests such as basic server details.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2014, Turn 24 Ltd.
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class Generic
{
    function serverInfo()
    {
        $xmldoc = new \DomDocument('1.0', 'UTF-8');
        $xmldoc->formatOutput = true;

        $packet = $xmldoc->createElement('packet');
        $packet->setAttribute('version', '1.6.2.0');
        $xmldoc->appendChild($packet);

        $server = $xmldoc->createElement('server');
        $packet->appendChild($server);

        $get = $xmldoc->createElement('get');
        $server->appendChild($get);

        $gen_info = $xmldoc->createElement('gen_info');
        $get->appendChild($gen_info);

        $stat = $xmldoc->createElement('stat');
        $get->appendChild($stat);

        $prefs = $xmldoc->createElement('prefs');
        $get->appendChild($prefs);

        return $xmldoc;
    }
}