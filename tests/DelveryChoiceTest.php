<?php

namespace DansMaCulotte\Colissimo\Tests;

use Carbon\Carbon;
use DansMaCulotte\Colissimo\DeliveryChoice;
use DansMaCulotte\Colissimo\Resources\PickupPoint;

class DeliveryChoiceTest extends TestCase
{
    public function testDeliveryChoice()
    {
        $delivery = new DeliveryChoice($this->credentials);
        
        $result = $delivery->findPickupPoints(
            'Caen',
            '14000',
            'FR',
            Carbon::now()->format('d/m/Y'),
            [
                'address' => '7 rue Mélingue'
            ]
        );
        
        print_r($result);
    }

    public function testDeliveryChoiceByID()
    {
        $delivery = new DeliveryChoice($this->credentials);

        $result = $delivery->findPickupPointByID(
            '149390',
            Carbon::now()->format('d/m/Y')
        );

        $this->assertInstanceOf(PickupPoint::class, $result);
    }
}
