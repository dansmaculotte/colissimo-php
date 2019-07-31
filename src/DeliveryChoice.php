<?php

namespace DansMaCulotte\Colissimo;

use DansMaCulotte\Colissimo\Exceptions\Exception;
use DansMaCulotte\Colissimo\Resources\PickupPoint;
use SimpleXMLElement;

/**
 * Implementation of Delivery Choice Web Service
 * https://www.colissimo.entreprise.laposte.fr/system/files/imagescontent/docs/spec_ws_livraison.pdf
 */
class DeliveryChoice extends Colissimo
{
    /** @var string */
    const SERVICE_URL = 'https://ws.colissimo.fr/pointretrait-ws-cxf/PointRetraitServiceWS/2.0/';

    /** @var array */
    const ERRORS = [
        101 => 'Missing account number',
        102 => 'Missing password',
        104 => 'Missing postal code',
        105 => 'Missing city',
        106 => 'Missing estimated shipping date',
        107 => 'Missing pickup point ID',
        117 => 'Missing country code',
        120 => 'Weight value is not an integer',
        121 => 'Weight value is not between 1 and 99999',
        122 => 'Date format does not match DD/MM/YYYY',
        123 => 'Relay filter is not a bool',
        124 => 'Invalid pickup point ID',
        125 => 'Invalid postal code (not between 01XXX, 95XXX or 980XX)',
        127 => 'Invalid RequestId',
        129 => 'Invalid address',
        143 => 'Postal code does not match XXXX',
        201 => 'Invalid account number or password',
        144 => 'Invalid postal code',
        145 => 'Missing postal code',
        146 => 'Country not valid for Colissimo Europe',
        202 => 'Request unauthorized for this account',
        203 => 'International option not available for this country',
        300 => 'No pickup points found with rules applied',
        301 => 'No pickup points found',
        1000 => 'Internal server error',
    ];
    
    /**
     * Construct Method
     *
     * @param array $credentials Contains accountNumber and password for authentication
     */
    public function __construct(array $credentials)
    {
        parent::__construct($credentials, self::SERVICE_URL);
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
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function findPickupPoints(
        string $city,
        string $zipCode,
        string $countryCode,
        string $shippingDate,
        array $options = []
    ) {
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
            $error = $this->parseErrorCode($return[0]->errorCode, self::ERRORS);
            throw Exception::requestError($error);
        }

        $pickupPoints = [];
        foreach ($xml->xpath('//listePointRetraitAcheminement') as $pickupPoint) {
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
     * @param int $id Pickup point ID
     * @param string $shippingDate Shipping date (DD/MM/YYYY)
     * @param array $options Additional parameters
     *
     * @return PickupPoint
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Exception
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

        $response = $this->httpRequest(
            'findPointRetraitAcheminementByID',
            $options
        );

        $xml = new SimpleXMLElement((string) $response->getBody());

        $return = $xml->xpath('//return');
        if (count($return) && $return[0]->errorCode != 0) {
            $error = $this->parseErrorCode($return[0]->errorCode, self::ERRORS);
            throw Exception::requestError($error);
        }
        
        $rawPickupPoint = $xml->xpath('//pointRetraitAcheminement');
        if (count($rawPickupPoint) && $rawPickupPoint[0]) {
            $pickupPoint = new PickupPoint(
                $rawPickupPoint[0]->accesPersonneMobiliteReduite,
                $rawPickupPoint[0]->adresse1,
                $rawPickupPoint[0]->adresse2,
                $rawPickupPoint[0]->adresse3,
                $rawPickupPoint[0]->codePostal,
                $rawPickupPoint[0]->congesPartiel,
                $rawPickupPoint[0]->congesTotal,
                $rawPickupPoint[0]->coordGeolocalisationLatitude,
                $rawPickupPoint[0]->coordGeolocalisationLongitude,
                $rawPickupPoint[0]->distanceEnMetre,
                $rawPickupPoint[0]->horairesOuvertureLundi,
                $rawPickupPoint[0]->horairesOuvertureMardi,
                $rawPickupPoint[0]->horairesOuvertureMercredi,
                $rawPickupPoint[0]->horairesOuvertureJeudi,
                $rawPickupPoint[0]->horairesOuvertureVendredi,
                $rawPickupPoint[0]->horairesOuvertureSamedi,
                $rawPickupPoint[0]->horairesOuvertureDimanche,
                $rawPickupPoint[0]->identifiant,
                $rawPickupPoint[0]->indiceDeLocalisation,
                $rawPickupPoint[0]->listeConges,
                $rawPickupPoint[0]->localite,
                $rawPickupPoint[0]->nom,
                $rawPickupPoint[0]->periodeActiviteHoraireDeb,
                $rawPickupPoint[0]->periodeActiviteHoraireFin,
                $rawPickupPoint[0]->poidsMaxi,
                $rawPickupPoint[0]->typeDePoint,
                $rawPickupPoint[0]->codePays,
                $rawPickupPoint[0]->langue,
                $rawPickupPoint[0]->libellePays,
                $rawPickupPoint[0]->loanOfHandlingTool,
                $rawPickupPoint[0]->parking,
                $rawPickupPoint[0]->reseau,
                $rawPickupPoint[0]->distributionSort,
                $rawPickupPoint[0]->lotAcheminement,
                $rawPickupPoint[0]->versionPlanTri
            );
        }

        return $pickupPoint;
    }
}
