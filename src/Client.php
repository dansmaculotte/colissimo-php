<?php

namespace DansMaCulotte\Colissimo;

use Zend\Soap\Client as SoapClient;
use GuzzleHttp\Client as HttpClient;

class Client
{
    
    const STATUS_URL = 'https://ws.colissimo.fr/supervision-wspudo/supervision.jsp';

    private $_soapClient;
    private $_soapOptions;
    private $_credentials;

    /**
     * Construct method to build soap client and options
     * 
     * @param array  $credentials Contains login and password items
     * @param string $url         Url to use for SOAP client
     * @param array  $options     Options to use for SOAP client
     */
    public function __construct($credentials, $url, $options)
    {
        if (isset($credentials['login']) == false) {
            throw new \Exception(
                'You must provide a login to authenticate with Colissimo Web Services'
            );
        }

        if (isset($credentials['password']) == false) {
            throw new \Exception('You must provide a password to authenticate with Colissimo Web Services');
        }

        $this->_soapOptions = array(
            'soap_version' => SOAP_1_1,
        );

        $this->_credentials = array(
            'accountNumber' =>  $credentials['login'],
            'password' => $credentials['password'],
        );

        $this->_soapClient = new SoapClient(
            $url,
            array_merge($this->_soapOptions, $options)
        );
    }

    /**
     * Check Web Services Endpoint and verify if response contains OK string
     * 
     * @return bool
     */
    public static function checkWebServiceStatus()
    {
        $client = new HttpClient();
        $response = $client->request('GET', self::STATUS_URL);

        if ($response->getStatusCode() != 200) {
            throw new \Exception('Colissimo Web Services Status Code Error');
        }

        if (preg_match_all('/OK/m', $response->getBody()) == false) {
            throw new \Exception('Colissimo Web Services KO');
        }

        return true;
    }

    /**
     * Proxy method to automaticaly inject credentials and options
     * 
     * @param array $method Method to with SOAP web services
     * @param array $params Parameters to send with method
     * 
     * @return Object
     */
    public function soapExec($method, $params)
    {
        return $this->_soapClient->$method(
            array_merge($this->_credentials, $params)
        );
    }
}