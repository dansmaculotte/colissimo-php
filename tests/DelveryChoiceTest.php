<?php

use PHPUnit\Framework\TestCase;
use DansMaCulotte\Colissimo\DeliveryChoice;

require 'Credentials.php';

class DeliveryChoiceTest extends TestCase
{
    public function testDeliveryChoice()
    {
        $delivery = new DeliveryChoice(
            array(
                'login' => COLISSIMO_LOGIN,
                'password' => COLISSIMO_PASSWORD,
            )
        );

        $result = $delivery->findPickupPoints(
            'Perdreauville',
            '78484',
            'FR',
            '26/11/2018',
            array(
                'address' => '1 Impasse Frere Jacques La belle Cote',
                'optionInter' => 0,
            )
        );

        print_r($result);

        // $this->assertTrue($result);
    }
}