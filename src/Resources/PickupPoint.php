<?php

namespace DansMaCulotte\Colissimo\Resources;

use Spatie\OpeningHours\OpeningHours;

class PickupPoint
{
    public $data;

    /**
     * PickupPoint constructor.
     * @param string $accesPersonneMobiliteReduite
     * @param string $adresse1
     * @param string $adresse2
     * @param string $adresse3
     * @param string $codePostal
     * @param string $congesPartiel
     * @param string $congesTotal
     * @param string $coordGeolocalisationLatitude
     * @param string $coordGeolocalisationLongitude
     * @param string $distanceEnMetre
     * @param string $horairesOuvertureLundi
     * @param string $horairesOuvertureMardi
     * @param string $horairesOuvertureMercredi
     * @param string $horairesOuvertureJeudi
     * @param string $horairesOuvertureVendredi
     * @param string $horairesOuvertureSamedi
     * @param string $horairesOuvertureDimanche
     * @param string $identifiant
     * @param string $indiceDeLocalisation
     * @param mixed $listeConges
     * @param string $localite
     * @param string $nom
     * @param string $periodeActiviteHoraireDeb
     * @param string $periodeActiviteHoraireFin
     * @param string $poidsMaxi
     * @param string $typeDePoint
     * @param string $codePays
     * @param string $langue
     * @param string $libellePays
     * @param string $loanOfHandlingTool
     * @param string $parking
     * @param string $reseau
     * @param string $distributionSort
     * @param string $lotAcheminement
     * @param string $versionPlanTri
     * @param string $calendarDeDebut
     * @param string $calendarDeFin
     * @param string $numero
     */
    public function __construct(
        string $accesPersonneMobiliteReduite,
        string $adresse1,
        string $adresse2,
        string $adresse3,
        string $codePostal,
        string $congesPartiel,
        string $congesTotal,
        string $coordGeolocalisationLatitude,
        string $coordGeolocalisationLongitude,
        string $distanceEnMetre,
        string $horairesOuvertureLundi,
        string $horairesOuvertureMardi,
        string $horairesOuvertureMercredi,
        string $horairesOuvertureJeudi,
        string $horairesOuvertureVendredi,
        string $horairesOuvertureSamedi,
        string $horairesOuvertureDimanche,
        string $identifiant,
        string $indiceDeLocalisation,
        object $listeConges,
        string $localite,
        string $nom,
        string $periodeActiviteHoraireDeb,
        string $periodeActiviteHoraireFin,
        string $poidsMaxi,
        string $typeDePoint,
        string $codePays,
        string $langue,
        string $libellePays,
        string $loanOfHandlingTool,
        string $parking,
        string $reseau,
        string $distributionSort,
        string $lotAcheminement,
        string $versionPlanTri
    ) {
        $this->data['id'] = $identifiant;
        $this->data['name'] = $nom;
        $this->data['disabledPersonAccess'] = $accesPersonneMobiliteReduite;
        $this->data['streetName'] = $adresse1;
        $this->data['premise'] = $adresse2;
        $this->data['locality'] = $adresse3;
        $this->data['city'] = $localite;
        $this->data['postalCode'] = $codePostal;
        $this->data['countryCode'] = $codePays;
        $this->data['partialClosed'] = $congesPartiel;
        $this->data['closed'] = $congesTotal;
        $this->data['latGeoCoord'] = $coordGeolocalisationLatitude;
        $this->data['longGeoCoord'] = $coordGeolocalisationLongitude;
        $this->data['range'] = $distanceEnMetre;
        $this->data['locationHelp'] = $indiceDeLocalisation;

        $this->data['openingsDateStart'] = $periodeActiviteHoraireDeb;
        $this->data['openingsDateEnd'] = $periodeActiviteHoraireFin;

        $this->data['openings'] = OpeningHours::create(
            [
                'monday' => $this->_formatRangeTime(
                    $horairesOuvertureLundi
                ),
                'tuesday' => $this->_formatRangeTime(
                    $horairesOuvertureMardi
                ),
                'wednesday' => $this->_formatRangeTime(
                    $horairesOuvertureMercredi
                ),
                'thursday' => $this->_formatRangeTime(
                    $horairesOuvertureJeudi
                ),
                'friday' => $this->_formatRangeTime(
                    $horairesOuvertureVendredi
                ),
                'saturday' => $this->_formatRangeTime(
                    $horairesOuvertureSamedi
                ),
                'sunday' => $this->_formatRangeTime(
                    $horairesOuvertureDimanche
                ),
            ]
        );

        if (isset($listeConges)) {
            $holidays = [];
            if (is_object($listeConges)) {
                array_push(
                    $holidays,
                    [
                        'start' => $listeConges->calendarDeDebut,
                        'end' => $listeConges->calendarDeFin,
                        'number' => $listeConges->numero,
                    ]
                );
            } else {
                foreach ($listeConges as $conges) {
                    array_push(
                        $holidays,
                        [
                            'start' => $conges->calendarDeDebut,
                            'end' => $conges->calendarDeFin,
                            'number' => $conges->numero,
                        ]
                    );
                }
            }

            $this->data['holidays'] = $holidays;
        }

        $this->data['maxWeight'] = $poidsMaxi;
        $this->data['pointType'] = $typeDePoint;
        $this->data['language'] = $langue;
        $this->data['countryLabel'] = $libellePays;
        $this->data['handlingTool'] = $loanOfHandlingTool;
        $this->data['parkingArea'] = $parking;
        $this->data['linkCode'] = $reseau;
        $this->data['distributionSort'] = $distributionSort;
        $this->data['pickupParcel'] = $lotAcheminement;
        $this->data['sortPlanVersion'] = $versionPlanTri;
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
            return [];
        }

        $validOpenings = array_filter(
            $partialOpenings,
            function ($partial) {
                return $partial != '00:00-00:00';
            }
        );

        if (count($validOpenings) == 2 && $validOpenings[0] == $validOpenings[1]) {
            return [
                $validOpenings[0]
            ];
        }

        return $validOpenings;
    }
}