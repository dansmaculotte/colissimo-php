<?php

namespace DansMaCulotte\Colissimo;

use GuzzleHttp\Client as HttpClient;

class Colissimo
{
    const STATUS_URL = 'https://ws.colissimo.fr/supervision-wspudo/supervision.jsp';

    private $_httpClient;
    private $_credentials;

    /**
     * Construct method to build soap client and options
     *
     * @param array $credentials Contains login, password and/or apiKey items
     * @param string $url Url to use for SOAP client
     */
    public function __construct($credentials, $url)
    {
        if (isset($credentials['accountNumber']) && isset($credentials['password'])) {
            $this->_credentials = [
                'accountNumber' =>  $credentials['accountNumber'],
                'password' => $credentials['password'],
            ];
        }

        if (isset($credentials['apikey'])) {
            $this->_credentials['apikey'] = $credentials['apikey'];
        }

        if (isset($credentials['codTiersPourPartenaire'])) {
            $this->_credentials['codTiersPourPartenaire'] = $credentials['codTiersPourPartenaire'];
        }

        $this->_httpClient = new HttpClient([
            'base_uri' => $url,
        ]);
    }

    /**
     * Check Web Services Endpoint and verify if response contains OK string
     *
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException|\Exception
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
     * Proxy method to automatically inject credentials
     *
     * @param string $method
     * @param array $params
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function httpRequest(string $method, array $params)
    {
        return $this->_httpClient->request('GET', $method, [
            'query' => array_merge(
                $this->_credentials,
                $params
            )
        ]);
    }

    /**
     * @param int $code
     * @param array $errors
     * @return string|null
     */
    protected function parseErrorCode(int $code, array $errors)
    {
        if (isset($errors[$code])) {
            return $errors[$code];
        }

        return null;
    }
}
