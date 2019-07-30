<?php

namespace DansMaCulotte\Colissimo;

use SimpleXMLElement;
use DansMaCulotte\Colissimo\Resources\PickupPoint;

/**
 * Implementation of Delivery Choice Web Service
 * https://www.colissimo.entreprise.laposte.fr/system/files/imagescontent/docs/spec_ws_livraison.pdf
 */
class DeliveryChoice extends Colissimo
{
    const SERVICE_URL = 'https://ws.colissimo.fr/pointretrait-ws-cxf/PointRetraitServiceWS/2.0/';

    /**
     * Construct Method
     *
     * @param array $credentials Contains login and password for authentication
     * @param array $options Additional parameters to submit to the web services
     * @param bool $soapClient Use SOAP client instead of HTTP client
     */
    public function __construct(array $credentials, array $options = [], $soapClient = false)
    {
        parent::__construct($credentials, self::SERVICE_URL, $options, $soapClient);
    }

    /**
     * Retrieve available pickup points by selectors
     *
     * @param string $city City name
     * @param string $zipCode Zip Code
     * @param string $countryCode ISO 3166 country code
     * @param string $shippingDate Shipping date (DD/MM/YYYY)
     * @param array $options Additional parameters
     *
     * @return PickupPoint[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function findPickupPoints(
        string $city,
        string $zipCode,
        string $countryCode,
        string $shippingDate,
        array $options = []
    )
    {
        $options = array_merge(
            [
                'city' => $city,
                'zipCode' => $zipCode,
                'countryCode' => $countryCode,
                'shippingDate' => $shippingDate,
            ],
            $options
        );
    
        $response = $this->httpRequest(
            'findRDVPointRetraitAcheminement',
            $options
        );
        
        $xml = new SimpleXMLElement((string) $response->getBody());
        
        $return = $xml->xpath('//return');
        if (count($return) && $return[0]->errorCode != 0) {
            throw new \Exception(
                'Failed to request delivery points: '.$return[0]->errorMessage
            );
        }

        $pickupPoints = [];
        foreach($xml->xpath('//listePointRetraitAcheminement') as $pickupPoint) {
            array_push($pickupPoints, new PickupPoint(
                $pickupPoint->accesPersonneMobiliteReduite,
                $pickupPoint->adresse1,
                $pickupPoint->adresse2,
                $pickupPoint->adresse3,
                $pickupPoint->codePostal,
                $pickupPoint->congesPartiel,
                $pickupPoint->congesTotal,
                $pickupPoint->coordGeolocalisationLatitude,
                $pickupPoint->coordGeolocalisationLongitude,
                $pickupPoint->distanceEnMetre,
                $pickupPoint->horairesOuvertureLundi,
                $pickupPoint->horairesOuvertureMardi,
                $pickupPoint->horairesOuvertureMercredi,
                $pickupPoint->horairesOuvertureJeudi,
                $pickupPoint->horairesOuvertureVendredi,
                $pickupPoint->horairesOuvertureSamedi,
                $pickupPoint->horairesOuvertureDimanche,
                $pickupPoint->identifiant,
                $pickupPoint->indiceDeLocalisation,
                $pickupPoint->listeConges,
                $pickupPoint->localite,
                $pickupPoint->nom,
                $pickupPoint->periodeActiviteHoraireDeb,
                $pickupPoint->periodeActiviteHoraireFin,
                $pickupPoint->poidsMaxi,
                $pickupPoint->typeDePoint,
                $pickupPoint->codePays,
                $pickupPoint->langue,
                $pickupPoint->libellePays,
                $pickupPoint->loanOfHandlingTool,
                $pickupPoint->parking,
                $pickupPoint->reseau,
                $pickupPoint->distributionSort,
                $pickupPoint->lotAcheminement,
                $pickupPoint->versionPlanTri
            ));
        }

        return $pickupPoints;
    }

    /**
     * Retreive pickup point by ID
     *
     * @param int    $id           Pickup point ID
     * @param string $shippingDate Shipping date (DD/MM/YYYY)
     * @param array  $options      Additional parameters
     *
     * @return PickupPoint
     * @throws \Exception
     */
    public function findPickupPointByID(int $id, string $shippingDate, array $options = [])
    {
        $options = array_merge(
            [
                'id' => $id,
                'date' => $shippingDate,
            ],
            $options
        );

        $result = $this->soapExec(
            'findPointRetraitAcheminementByID',
            $options
        );

        $result = $result->return;

        if ($result->errorCode != 0) {
            throw new \Exception(
                'Failed to request delivery points: '.$result->errorMessage
            );
        }
        
        $pickupPoint = new PickupPoint($result->pointRetraitAcheminement);

        return $pickupPoint;
    }
}
