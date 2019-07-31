<?php

namespace DansMaCulotte\Colissimo\Tests;

use Carbon\Carbon;
use DansMaCulotte\Colissimo\DeliveryChoice;
use DansMaCulotte\Colissimo\Exceptions\Exception;
use DansMaCulotte\Colissimo\Resources\PickupPoint;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class DeliveryChoiceTest extends TestCase
{
    public function testDeliveryChoiceFind()
    {
        $mock = new MockHandler([
            new Response(
                200,
                ['Content-Type' => 'text/xml'],
                file_get_contents(__DIR__.'/snapshots/findRDVPointRetraitAcheminement_success.xml')
            ),
            new Response(
                200,
                ['Content-Type' => 'text/xml'],
                file_get_contents(__DIR__.'/snapshots/findRDVPointRetraitAcheminement_failure.xml')
            )
        ]);

        $httpClient = $this->buildClientMock($mock);
        
        $delivery = new DeliveryChoice($this->credentials);
        $delivery->httpClient = $httpClient;
        
        $result = $delivery->findPickupPoints(
            'Caen',
            '14000',
            'FR',
            Carbon::now()->format('d/m/Y'),
            [
                'address' => '7 rue Mélingue'
            ]
        );
        
        $this->assertCount(20, $result);
        $this->assertContainsOnlyInstancesOf(PickupPoint::class, $result);

        $this->expectExceptionObject(Exception::requestError(DeliveryChoice::ERRORS[201]));
        $result = $delivery->findPickupPoints(
            'Caen',
            '14000',
            'FR',
            Carbon::now()->format('d/m/Y'),
            [
                'address' => '7 rue Mélingue'
            ]
        );
    }

    public function testDeliveryChoiceByID()
    {
        $mock = new MockHandler([
            new Response(
                200,
                ['Content-Type' => 'text/xml'],
                file_get_contents(__DIR__.'/snapshots/findPointRetraitAcheminementByID_success.xml')
            ),
            new Response(
                200,
                ['Content-Type' => 'text/xml'],
                file_get_contents(__DIR__.'/snapshots/findPointRetraitAcheminementByID_failure.xml')
            ),
        ]);

        $httpClient = $this->buildClientMock($mock);
        
        $delivery = new DeliveryChoice($this->credentials);
        $delivery->httpClient = $httpClient;

        $result = $delivery->findPickupPointByID(
            '149390',
            Carbon::now()->format('d/m/Y')
        );

        $this->assertInstanceOf(PickupPoint::class, $result);

        $this->expectExceptionObject(Exception::requestError(DeliveryChoice::ERRORS[201]));
        $delivery->findPickupPointByID(
            '149390',
            Carbon::now()->format('d/m/Y')
        );
    }
}
