# Colissimo Web Sercices PHP SDK

This library aims to facilitate the usage of Colissimo Web Services

## Services

- [Delivery Choice](https://www.colissimo.entreprise.laposte.fr/system/files/imagescontent/docs/spec_ws_livraison.pdf)
- [Postage](https://www.colissimo.entreprise.laposte.fr/system/files/imagescontent/docs/spec_ws_affranchissement.pdf)
- [Parcel Tracking](https://www.colissimo.entreprise.laposte.fr/system/files/imagescontent/docs/spec_ws_suivi.pdf)

## Installation

### Requirements

- PHP 7.0
- Soap Extension

You can install the package via composer:

``` bash
composer require dansmaculotte/colissimo-php
```

## Usage

### Web Services Status

```php
use DansMaCulotte\Colissimo\Client;

try {
    Client::checkWebServiceStatus();
} catch (\Exception $e) {
    print_r($e);
}
```

### Delivery Choice

[Colissimo Documentation](https://www.colissimo.entreprise.laposte.fr/system/files/imagescontent/docs/spec_ws_livraison.pdf
)

#### Find pickup points

```php
use DansMaCulotte\Colissimo\DeliveryChoice;

$delivery = new DeliveryChoice(
    array(
        'login' => COLISSIMO_LOGIN,
        'password' => COLISSIMO_PASSWORD,
    )
);

$result = $delivery->findPickupPoints(
    'Caen',
    '14000',
    'FR',
    Carbon::now()->format('d/m/Y'),
    array(
        'address' => '7 rue Mélingue',
    )
);

print_r($result);
```

#### Find pickup point by ID

```php
use DansMaCulotte\Colissimo\DeliveryChoice;

$delivery = new DeliveryChoice(
    array(
        'login' => COLISSIMO_LOGIN,
        'password' => COLISSIMO_PASSWORD,
    )
);

$result = $delivery->findPickupPointByID(
    '149390',
    Carbon::now()->format('d/m/Y')
);

print_r($result);
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
