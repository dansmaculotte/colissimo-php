<?php

namespace DansMaCulotte\Colissimo;

use DansMaCulotte\Colissimo\Exceptions\Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;

class Colissimo
{
    const STATUS_URL = 'https://ws.colissimo.fr/supervision-wspudo/supervision.jsp';

    public $httpClient;
    protected $credentials;

    /**
     * Construct method to build soap client and options
     *
     * @param array $credentials Contains login, password and/or apiKey items
     * @param string $url Url to use for SOAP client
     * @param array $options Guzzle Client options
     */
    public function __construct(array $credentials = [], $url = null, array $options = [])
    {
        if (isset($credentials['accountNumber']) && isset($credentials['password'])) {
            $this->credentials = [
                'accountNumber' =>  $credentials['accountNumber'],
                'password' => $credentials['password'],
            ];
        }

        if (isset($credentials['apikey'])) {
            $this->credentials['apikey'] = $credentials['apikey'];
        }

        if (isset($credentials['codTiersPourPartenaire'])) {
            $this->credentials['codTiersPourPartenaire'] = $credentials['codTiersPourPartenaire'];
        }

        $this->httpClient = new HttpClient(array_merge([
            'base_uri' => $url,
        ], $options));
    }

    /**
     * Check Web Services Endpoint and verify if response contains OK string
     *
     * @return bool
     * @throws Exception
     */
    public function checkWebServiceStatus()
    {
        try {
            $response = $this->httpClient->request('GET', self::STATUS_URL);

            $isOk = preg_match_all('/OK/m', (string) $response->getBody());
            if ($isOk == false) {
                throw Exception::serviceUnavailable();
            }
        } catch (GuzzleException $e) {
            throw Exception::serviceUnavailable();
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
        return $this->httpClient->request('GET', $method, [
            'query' => array_merge(
                $this->credentials,
                $params
            )
        ]);
    }

    /**
     * Parse service errors and throw if code match
     *
     * @param int $code
     * @param array $errors
     * @throws Exception
     */
    protected function parseErrorCodeAndThrow(int $code, array $errors)
    {
        $message = null;
        
        if (isset($errors[$code])) {
            $message = $errors[$code];
        }
        
        if ($message) {
            throw Exception::requestError($message);
        }
        
        return ;
    }
}
