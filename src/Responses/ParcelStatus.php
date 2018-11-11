<?php

namespace DansMaCulotte\ColissimoWebServices\Responses;

class ParcelStatus
{
    public $id;
    public $code;
    public $date;
    public $status;
    public $site;
    public $city;
    public $zipCode;
    public $countryCode;

    public function __construct($parameters)
    {
        $this->id = $parameters['skybillNumber'];
        $this->code = $parameters['eventCode'];
        $this->date = $parameters['eventDate'];
        $this->status = $parameters['eventLibelle'];
        $this->site = $parameters['eventSite'];
        $this->city = $parameters['recipientCity'];
        $this->zipCode = $parameters['recipientZipCode'];
        $this->contryCode = $parameters['recipientCountryCode'];
    }
}