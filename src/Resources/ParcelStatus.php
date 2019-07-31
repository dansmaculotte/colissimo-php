<?php

namespace DansMaCulotte\Colissimo\Resources;

class ParcelStatus
{
    public $data;

    public function __construct(
        string $skybillNumber,
        string $eventCode,
        string $eventDate,
        string $eventLibelle,
        string $eventSite,
        string $recipientCity,
        string $recipientZipCode,
        string $recipientCountryCode
    ) {
        $this->data['id'] = $skybillNumber;
        $this->data['code'] = $eventCode;
        $this->data['date'] = $eventDate;
        $this->data['status'] = $eventLibelle;
        $this->data['site'] = $eventSite;
        $this->data['city'] = $recipientCity;
        $this->data['zipCode'] = $recipientZipCode;
        $this->data['countryCode'] = $recipientCountryCode;
    }
}
