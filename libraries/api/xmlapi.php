<?php
namespace Addon\Plesk\Libraries\api;
/**
 * Plesk XML API
 *
 * Allows interaction with a Plesk 11.5 or above server.
 *
 * @package  WHSuite
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2014, Turn 24 Ltd.
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link http://whsuite.com
 * @since  Version 1.0
 */
class Xmlapi
{
    private $host;
    private $port = '8443';

    private $user;
    private $password;

    private $curl;

    /**
     * Loads the server details and prepares CURL to perform the Panel API request
     * @return resource
     */
    public function __construct($host = null, $user = null, $password = null)
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://{$this->host}:{$this->port}/enterprise/control/agent.php");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST,           true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "HTTP_AUTH_LOGIN: {$this->user}",
            "HTTP_AUTH_PASSWD: {$this->password}",
            "HTTP_PRETTY_PRINT: TRUE",
            "Content-Type: text/xml"
        )
      );

      $this->curl = $curl;
    }

    /**
     * Performs a Panel API request, returns raw API response text
     *
     * @return string
     * @throws ApiRequestException
     */
    public function sendRequest($packet)
    {
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $packet);
        $result = curl_exec($this->curl);

        if (curl_errno($this->curl)) {
            $errmsg  = curl_error($this->curl);
            $errcode = curl_errno($this->curl);
            curl_close($this->curl);
            throw new ApiRequestException($errmsg, $errcode);
        }

        curl_close($this->curl);
        return $result;
    }

    /**
     * Looks if API responded with correct data
     *
     * @return SimpleXMLElement
     * @throws ApiRequestException
     */
    public function parseResponse($response_string)
    {
        $xml = new \SimpleXMLElement($response_string);
        if (!is_a($xml, 'SimpleXMLElement')) {
             throw new ApiRequestException("Cannot parse server response: {$response_string}");
        }
        return $xml;
    }

    /**
     * Check data in API response
     * @return void
     * @throws ApiRequestException
     */
    public function checkResponse(\SimpleXMLElement $response)
    {
        $resultNode = $response->domain->get->result;

        // check if request was successful
        if ('error' == (string)$resultNode->status) {
            throw new ApiRequestException("The Panel API returned an error: " . (string)$resultNode->result->errtext);
        }
    }

    function domainsInfoRequest()
    {
        $xmldoc = new \DomDocument('1.0', 'UTF-8');
        $xmldoc->formatOutput = true;

        // <packet>
        $packet = $xmldoc->createElement('packet');
        $packet->setAttribute('version', '1.6.2.0');
        $xmldoc->appendChild($packet);

        // <packet/domain>
        $domain = $xmldoc->createElement('domain');
        $packet->appendChild($domain);

        // <packet/domain/get>
        $get = $xmldoc->createElement('get');
        $domain->appendChild($get);

        // <packet/domain/get/filter>
        $filter = $xmldoc->createElement('filter');
        $get->appendChild($filter);

        // <packet/domain/get/dataset>
        $dataset = $xmldoc->createElement('dataset');
        $get->appendChild($dataset);

        // dataset elements
        $dataset->appendChild($xmldoc->createElement('hosting'));
        $dataset->appendChild($xmldoc->createElement('gen_info'));

        return $xmldoc;
    }


}


/**
 * Reports error during API RPC request
 */
class ApiRequestException extends \Exception {}