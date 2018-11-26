<?php

namespace DansMaCulotte\ColissimoWebServices;

use DansMaCulotte\ColissimoWebServices\Client;
use DansMaCulotte\ColissimoWebServices\Resources\ParcelStatus;

/**
 * Implementation of Parcel Tracking Web Service
 * https://www.colissimo.entreprise.laposte.fr/system/files/imagescontent/docs/spec_ws_suivi.pdf
 */
class ParcelTracking extends Client
{
    const SERVICE_URL = 'https://www.coliposte.fr/tracking-chargeur-cxf/TrackingServiceWS?wsdl';

    public function __construct($credentials, $options = array())
    {
        parent::__construct($credentials, self::SERVICE_URL, $options);
    }

    /**
     * Retrieve Parcel status by it's ID
     * 
     * @param string $id Colissimo parcel number
     * 
     * @return ParcelStatus
     */
    public function getStatusByID($id)
    {
        $options = array_merge(
            array(
                'skybillNumber ' => $id,
            ),
            $options
        );

        $result = $this->soapExec(
            'track',
            $options
        );

        $result = $result->return;

        if ($result->errorCode != 0) {
            throw new \Exception(
                'Failed to generate label: '.$result->errorMessage
            );
        }

        $parcelStatus = new ParcelStatus($result);

        return $parcelStatus;
    }
}