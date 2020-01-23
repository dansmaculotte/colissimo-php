# Colissimo Web Services PHP SDK

[![Latest Version](https://img.shields.io/packagist/v/dansmaculotte/colissimo-php.svg?style=flat-square)](https://packagist.org/packages/dansmaculotte/colissimo-php)
[![Total Downloads](https://img.shields.io/packagist/dt/dansmaculotte/colissimo-php.svg?style=flat-square)](https://packagist.org/packages/dansmaculotte/colissimo-php)
[![Build Status](https://img.shields.io/travis/dansmaculotte/colissimo-php/master.svg?style=flat-square)](https://travis-ci.org/dansmaculotte/colissimo-php)
[![Quality Score](https://img.shields.io/scrutinizer/g/dansmaculotte/colissimo-php.svg?style=flat-square)](https://scrutinizer-ci.com/g/dansmaculotte/colissimo-php)
[![Code Coverage](https://img.shields.io/coveralls/github/dansmaculotte/colissimo-php.svg?style=flat-square)](https://coveralls.io/github/dansmaculotte/colissimo-php)

> This library aims to facilitate the usage of Colissimo Web Services

## Services

- [Delivery Choice](https://www.colissimo.entreprise.laposte.fr/system/files/imagescontent/docs/spec_ws_livraison.pdf)
- [Parcel Tracking](https://www.colissimo.entreprise.laposte.fr/system/files/imagescontent/docs/spec_ws_suivi.pdf)
- [Postage (ToDo)](https://www.colissimo.entreprise.laposte.fr/system/files/imagescontent/docs/spec_ws_affranchissement.pdf)

## Installation

### Requirements

- PHP 7.2
- Json Extension
- SimpleXML Extension

You can install the package via composer:

``` bash
composer require dansmaculotte/colissimo-php
```

## Usage

### Web Services Status

```php
use DansMaCulotte\Colissimo\Colissimo;

try {
    $colissimo = new Colissimo();
    $colissimo->checkWebServiceStatus();
} catch (\Exception $e) {
    print_r($e);
}
```

### Delivery Choice

#### Find pickup points

```php
use DansMaCulotte\Colissimo\DeliveryChoice;

$delivery = new DeliveryChoice(
    [
        'accountNumber' => COLISSIMO_LOGIN,
        'password' => COLISSIMO_PASSWORD,
    ]
);

$result = $delivery->findPickupPoints(
    'Caen',
    '14000',
    'FR',
    Carbon::now()->format('d/m/Y'),
    [
        'address' => '7 rue Mélingue',
    ]
);

print_r($result);
```

#### Find pickup point by ID

```php
use DansMaCulotte\Colissimo\DeliveryChoice;

$delivery = new DeliveryChoice(
    [
        'accountNumber' => COLISSIMO_LOGIN,
        'password' => COLISSIMO_PASSWORD,
    ]
);

$result = $delivery->findPickupPointByID(
    '149390',
    Carbon::now()->format('d/m/Y')
);

print_r($result);
```

### Parcel Tracking

#### Get parcel status by ID

```php
use DansMaCulotte\Colissimo\ParcelTracking;

$parcelTracking = new ParcelTracking(
    [
        'accountNumber' => COLISSIMO_LOGIN,
        'password' => COLISSIMO_PASSWORD,
    ]
);

$result = $parcelTracking->getStatusByID('111111111');

print_r($result);
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
