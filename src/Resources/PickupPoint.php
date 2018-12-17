<?php

namespace DansMaCulotte\Colissimo\Resources;

use Spatie\OpeningHours\OpeningHours;

class PickupPoint
{
    public $id;
    public $name;
    public $disabledPersonAccess;
    public $address;
    public $addressOptional;
    public $locality;
    public $city;
    public $postalCode;
    public $countryCode;
    public $partialClosed;
    public $closed;
    public $latGeoCoord;
    public $longGeoCoord;
    public $range;
    public $locationHelp;
    public $openingsDateStart;
    public $openingsDateEnd;
    public $openings;
    public $holidays;
    public $maxWeight;
    public $pointType;
    public $language;
    public $countryLabel;
    public $handlingTool;
    public $parkingArea;
    public $linkCode;
    public $distributionSort;
    public $pickupParcel;
    public $sortPlanVersion;

    public function __construct($parameters)
    {
        $this->id = $parameters->identifiant;
        $this->name = $parameters->nom;
        $this->disabledPersonAccess = $parameters->accesPersonneMobiliteReduite;
        $this->address = $parameters->adresse1;
        $this->addressOptional = $parameters->adresse2;
        $this->locality = $parameters->adresse3;
        $this->city = $parameters->localite;
        $this->postalCode = $parameters->codePostal;
        $this->countryCode = $parameters->codePays;
        $this->partialClosed = $parameters->congesPartiel;
        $this->closed = $parameters->congesTotal;
        $this->latGeoCoord = $parameters->coordGeolocalisationLatitude;
        $this->longGeoCoord = $parameters->coordGeolocalisationLongitude;
        $this->range = $parameters->distanceEnMetre;
        $this->locationHelp = $parameters->indiceDeLocalisation;
        
        $this->openingsDateStart = $parameters->periodeActiviteHoraireDeb;
        $this->openingsDateEnd = $parameters->periodeActiviteHoraireFin;

        $this->openings = OpeningHours::create(
            [
                'monday' => $this->_formatRangeTime(
                    $parameters->horairesOuvertureLundi
                ),
                'tuesday' => $this->_formatRangeTime(
                    $parameters->horairesOuvertureMardi
                ),
                'wednesday' => $this->_formatRangeTime(
                    $parameters->horairesOuvertureMercredi
                ),
                'thursday' => $this->_formatRangeTime(
                    $parameters->horairesOuvertureJeudi
                ),
                'friday' => $this->_formatRangeTime(
                    $parameters->horairesOuvertureVendredi
                ),
                'saturday' => $this->_formatRangeTime(
                    $parameters->horairesOuvertureSamedi
                ),
                'sunday' => $this->_formatRangeTime(
                    $parameters->horairesOuvertureDimanche
                ),
            ]
        );

        if (isset($parameters->listeConges)) {

            $holidays = array();
            if (is_object($parameters->listeConges)) {

                array_push(
                    $holidays,
                    array(
                        'start' => $parameters->listeConges->calendarDeDebut,
                        'end' => $parameters->listeConges->calendarDeFin,
                        'number' => $parameters->listeConges->numero,
                    )
                );  

            } else {

                foreach ($parameters->listeConges as $conges) {
                    array_push(
                        $holidays,
                        array(
                            'start' => $conges->calendarDeDebut,
                            'end' => $conges->calendarDeFin,
                            'number' => $conges->numero,
                        )
                    );  
                }

            }

            $this->holidays = $holidays;
        }

        $this->maxWeight = $parameters->poidsMaxi;
        $this->pointType = $parameters->typeDePoint;
        $this->language = $parameters->langue;
        $this->countryLabel = $parameters->libellePays;
        $this->handlingTool = $parameters->loanOfHandlingTool;
        $this->parkingArea = $parameters->parking;
        $this->linkCode = $parameters->reseau;
        $this->distributionSort = $parameters->distributionSort;
        $this->pickupParcel = $parameters->lotAcheminement;
        $this->sortPlanVersion = $parameters->versionPlanTri;
    }

    /**
     * Split Range datetime in two datetimes
     * 
     * @param string $hours Range datetime e.g. 09:45-12:30 14:00-18:30
     * 
     * @return array
     */
    private function _formatRangeTime($hours)
    {
        $partialOpenings = explode(' ', $hours);

        if (count($partialOpenings) != 2) {
            return array();
        }

        $validOpenings = array_filter(
            $partialOpenings,
            function ($partial) {
                return $partial != '00:00-00:00';
            }
        );

        if (count($validOpenings) == 2 && $validOpenings[0] == $validOpenings[1]) {
            return array(
                $validOpenings[0]
            );
        }

        return $validOpenings;
    }
}