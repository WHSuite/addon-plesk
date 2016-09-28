<?php
namespace Addon\Plesk\Libraries\api;
/**
 * Plesk XML API - Site Calls
 *
 * Site related API requests such as accounts, domain info and site limits
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2014, Turn 24 Ltd.
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class Sites
{
    function allSites()
    {
        $xmldoc = new \DomDocument('1.0', 'UTF-8');
        $xmldoc->formatOutput = true;

        $packet = $xmldoc->createElement('packet');
        $packet->setAttribute('version', '1.6.0.0');
        $xmldoc->appendChild($packet);

        $site = $xmldoc->createElement('domain');
        $packet->appendChild($site);

        $get = $xmldoc->createElement('get');
        $site->appendChild($get);

        $filter = $xmldoc->createElement('filter');
        $get->appendChild($filter);

        $dataset = $xmldoc->createElement('dataset');
        $get->appendChild($dataset);

        $hosting = $xmldoc->createElement('hosting');
        $dataset->appendChild($hosting);

        $gen_info = $xmldoc->createElement('gen_info');
        $dataset->appendChild($gen_info);

        $stat = $xmldoc->createElement('stat');
        $dataset->appendChild($stat);

        $prefs = $xmldoc->createElement('prefs');
        $dataset->appendChild($prefs);

        $disk_usage = $xmldoc->createElement('disk_usage');
        $dataset->appendChild($disk_usage);

        return $xmldoc;
    }

    function getSiteByDomain($domain)
    {
        $xmldoc = new \DomDocument('1.0', 'UTF-8');
        $xmldoc->formatOutput = true;

        $packet = $xmldoc->createElement('packet');
        $packet->setAttribute('version', '1.6.3.0');
        $xmldoc->appendChild($packet);

        $site = $xmldoc->createElement('site');
        $packet->appendChild($site);

        $get = $xmldoc->createElement('get');
        $site->appendChild($get);

        $filter = $xmldoc->createElement('filter');
        $get->appendChild($filter);

        $name = $xmldoc->createElement('name', $domain);
        $filter->appendChild($name);

        $dataset = $xmldoc->createElement('dataset');
        $get->appendChild($dataset);

        $hosting = $xmldoc->createElement('hosting');
        $dataset->appendChild($hosting);

        $gen_info = $xmldoc->createElement('gen_info');
        $dataset->appendChild($gen_info);

        $stat = $xmldoc->createElement('stat');
        $dataset->appendChild($stat);

        $prefs = $xmldoc->createElement('prefs');
        $dataset->appendChild($prefs);

        $disk_usage = $xmldoc->createElement('disk_usage');
        $dataset->appendChild($disk_usage);

        return $xmldoc;
    }

    public function getTrafficByDomain($domain)
    {
        $xmldoc = new \DomDocument('1.0', 'UTF-8');
        $xmldoc->formatOutput = true;

        $packet = $xmldoc->createElement('packet');
        $packet->setAttribute('version', '1.6.3.0');
        $xmldoc->appendChild($packet);

        $site = $xmldoc->createElement('site');
        $packet->appendChild($site);

        //$get = $xmldoc->createElement('get');
        //$site->appendChild($get);

        $traffic = $xmldoc->createElement('get_traffic');
        $site->appendChild($traffic);

        $filter = $xmldoc->createElement('filter');
        $traffic->appendChild($filter);

        $name = $xmldoc->createElement('name', $domain);
        $filter->appendChild($name);

        $start_date = date('Y-m-01');

        $since_date = $xmldoc->createElement('since_date', $start_date);
        $traffic->appendChild($since_date);

        return $xmldoc;
    }

    public function suspendDomain($domain)
    {
        return $this->setDomainStatus($domain, 64);
    }

    public function unsuspendDomain($domain)
    {
        return $this->setDomainStatus($domain, 0);
    }

    public function setDomainStatus($domain, $status_id)
    {
        $xmldoc = new \DomDocument('1.0', 'UTF-8');
        $xmldoc->formatOutput = true;

        $packet = $xmldoc->createElement('packet');
        $packet->setAttribute('version', '1.6.0.1');
        $xmldoc->appendChild($packet);

        $dn = $xmldoc->createElement('domain');
        $packet->appendChild($dn);

        $set = $xmldoc->createElement('set');
        $dn->appendChild($set);

        $filter = $xmldoc->createElement('filter');
        $set->appendChild($filter);

        $name = $xmldoc->createElement('domain-name', $domain);
        $filter->appendChild($name);

        $values = $xmldoc->createElement('values');
        $set->appendChild($values);

        $gen_setup = $xmldoc->createElement('gen_setup');
        $values->appendChild($gen_setup);

        $status = $xmldoc->createElement('status', $status_id);
        $gen_setup->appendChild($status);

        return $xmldoc;
    }

    public function terminateSite($domain)
    {
        $xmldoc = new \DomDocument('1.0', 'UTF-8');
        $xmldoc->formatOutput = true;

        $packet = $xmldoc->createElement('packet');
        $packet->setAttribute('version', '1.6.3.0');
        $xmldoc->appendChild($packet);

        $site = $xmldoc->createElement('site');
        $packet->appendChild($site);

        $del = $xmldoc->createElement('del');
        $site->appendChild($del);

        $filter = $xmldoc->createElement('filter');
        $del->appendChild($filter);

        $name = $xmldoc->createElement('name', $domain);
        $filter->appendChild($name);

        return $xmldoc;
    }

    public function createSite($domain, $owner_id, $username, $password, $plan, $ip_address)
    {
        $xmldoc = new \DomDocument('1.0', 'UTF-8');
        $xmldoc->formatOutput = true;

        $packet = $xmldoc->createElement('packet');
        $packet->setAttribute('version', '1.6.3.0');
        $xmldoc->appendChild($packet);

        $webspace = $xmldoc->createElement('webspace');
        $packet->appendChild($webspace);

        $add = $xmldoc->createElement('add');
        $webspace->appendChild($add);

        $gen_setup = $xmldoc->createElement('gen_setup');
        $add->appendChild($gen_setup);

        $name = $xmldoc->createElement('name', $domain);
        $gen_setup->appendChild($name);

        $owner = $xmldoc->createElement('owner-id', $owner_id);
        $gen_setup->appendChild($owner);

        $htype = $xmldoc->createElement('htype', 'vrt_hst');
        $gen_setup->appendChild($htype);

        $ip = $xmldoc->createElement('ip_address', $ip_address);
        $gen_setup->appendChild($ip);

        $status = $xmldoc->createElement('status', '0');
        $gen_setup->appendChild($status);

        $hosting = $xmldoc->createElement('hosting');
        $add->appendChild($hosting);

        $vrt_hst = $xmldoc->createElement('vrt_hst');
        $hosting->appendChild($vrt_hst);

        $property = $xmldoc->createElement('property');
        $vrt_hst->appendChild($property);

        $name = $xmldoc->createElement('name', 'ftp_login');
        $property->appendChild($name);

        $value = $xmldoc->createElement('value', $username);
        $property->appendChild($value);

        $property = $xmldoc->createElement('property');
        $vrt_hst->appendChild($property);

        $name = $xmldoc->createElement('name', 'ftp_password');
        $property->appendChild($name);

        $value = $xmldoc->createElement('value', $password);
        $property->appendChild($value);

        $ip = $xmldoc->createElement('ip_address', $ip_address);
        $vrt_hst->appendChild($ip);

        $plan_name = $xmldoc->createElement('plan-name', $plan);
        $add->appendChild($plan_name);

        return $xmldoc;
    }
}