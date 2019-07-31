<?php

namespace DansMaCulotte\Colissimo;

use DansMaCulotte\Colissimo\Exceptions\Exception;
use DansMaCulotte\Colissimo\Resources\ParcelStatus;
use SimpleXMLElement;

/**
 * Implementation of Parcel Tracking Web Service
 * https://www.colissimo.entreprise.laposte.fr/system/files/imagescontent/docs/spec_ws_suivi.pdf
 */
class ParcelTracking extends Colissimo
{
    /** @var string */
    const SERVICE_URL = 'https://www.coliposte.fr/tracking-chargeur-cxf/TrackingServiceWS/';

    /** @var array */
    const ERRORS = [
        101 => 'Invalid parcel ID',
        103 => 'Parcel ID older than 30 days',
        104 => 'Parcel ID outside of client range IDs',
        105 => 'Unknown parcel ID',
        201 => 'Invalid account number or password',
        202 => 'Request unauthorized for this account',
        1000 => 'Internal server error',
    ];

    /**
     * Construct Method
     *
     * @param array $credentials Contains login and password for authentication
     */
    public function __construct(array $credentials)
    {
        parent::__construct($credentials, self::SERVICE_URL);
    }
    
    /**
     * Retrieve Parcel status by it's ID
     *
     * @param string $id Colissimo parcel number
     * @param array $options Additional parameters
     *
     * @return ParcelStatus
     * @throws Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getStatusByID(string $id, array $options = [])
    {
        $options = array_merge(
            [
                'skybillNumber ' => $id,
            ],
            $options
        );

        $response = $this->httpRequest(
            'track',
            $options
        );

        $xml = new SimpleXMLElement((string) $response->getBody());

        $return = $xml->xpath('//return');
        if (count($return) && $return[0]->errorCode != 0) {
            $error = $this->parseErrorCode($return[0]->errorCode, self::ERRORS);
            throw Exception::requestError($error);
        }

        $parcelStatus = new ParcelStatus(
            $return[0]->skybillNumber,
            $return[0]->eventCode,
            $return[0]->eventDate,
            $return[0]->eventLibelle,
            $return[0]->eventSite,
            $return[0]->recipientCity,
            $return[0]->recipientZipCode,
            $return[0]->recipientCountryCode
        );

        return $parcelStatus;
    }
}
