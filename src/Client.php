<?php

namespace DansMaCulotte\ColissimoWebServices;

use Zend\Soap\Client as SoapClient;
use GuzzleHttp\Client as HttpClient;

class Client
{
    
    const STATUS_URL = 'https://ws.colissimo.fr/supervision-wspudo/supervision.jsp';

    private $_soapClient;
    private $_soapOptions;
    private $_credentials;

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

    public static function checkWebServiceStatus()
    {
        $client = new HttpClient();
        $response = $client->request('GET', self::STATUS_URL);

        if ($response->getStatusCode() != 200) {
            throw new \Exception('Colissimo Delivery Choice Status Code Error');
        }

        if (preg_match_all('/OK/m', $response->getBody()) == false) {
            throw new \Exception('Colissimo Delivery Choice KO');
        }

        return true;
    }

    public function soapExec($method, $options)
    {
        return $this->_soapClient->$method(
            array_merge($this->_credentials, $options)
        );
    }
}