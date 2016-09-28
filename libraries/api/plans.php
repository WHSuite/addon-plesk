<?php
namespace Addon\Plesk\Libraries\api;
/**
 * Plesk XML API - Plan Calls
 *
 * Plan API requests such as listing available hosting plans.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2014, Turn 24 Ltd.
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class Plans
{
    function allPlans()
    {
        $xmldoc = new \DomDocument('1.0', 'UTF-8');
        $xmldoc->formatOutput = true;

        $packet = $xmldoc->createElement('packet');
        $packet->setAttribute('version', '1.6.3.0');
        $xmldoc->appendChild($packet);

        $serviceplan = $xmldoc->createElement('service-plan');
        $packet->appendChild($serviceplan);

        $get = $xmldoc->createElement('get');
        $serviceplan->appendChild($get);

        $filter = $xmldoc->createElement('filter');
        $get->appendChild($filter);

        $resellerplan = $xmldoc->createElement('reseller-plan');
        $packet->appendChild($resellerplan);

        $get_r = $xmldoc->createElement('get');
        $resellerplan->appendChild($get_r);

        $filter_r = $xmldoc->createElement('filter');
        $get_r->appendChild($filter_r);

        $id = $xmldoc->createElement('all');
        $filter_r->appendChild($id);

        return $xmldoc;
    }
}