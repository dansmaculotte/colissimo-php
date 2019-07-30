<?php

namespace DansMaCulotte\Colissimo;

use DansMaCulotte\Colissimo\Resources\ParcelStatus;

/**
 * Implementation of Parcel Tracking Web Service
 * https://www.colissimo.entreprise.laposte.fr/system/files/imagescontent/docs/spec_ws_suivi.pdf
 */
class ParcelTracking extends Colissimo
{
    const SERVICE_URL = 'https://www.coliposte.fr/tracking-chargeur-cxf/TrackingServiceWS?wsdl';

    /**
     * Construct Method
     *
     * @param array $credentials Contains login and password for authentication
     * @param array $options Additional parameters to submit to the web services
     * @param bool $soapClient Use SOAP client instead of HTTP client
     */
    public function __construct(array $credentials, array $options = [], $soapClient = false)
    {
        parent::__construct($credentials, self::SERVICE_URL, $options);
    }

    /**
     * Retrieve Parcel status by it's ID
     *
     * @param string $id      Colissimo parcel number
     * @param array  $options Additional parameters
     *
     * @return ParcelStatus
     * @throws \Exception
     */
    public function getStatusByID(string $id, array $options = [])
    {
        $options = array_merge(
            [
                'skybillNumber ' => $id,
            ],
            $options
        );

        $result = $this->soapExec(
            'track',
            $options
        );

        $result = $result->return;

        if ($result->errorCode != 0) {
            throw new \Exception(
                'Failed to get status: '.$result->errorMessage
            );
        }

        $parcelStatus = new ParcelStatus($result);

        return $parcelStatus;
    }
}
