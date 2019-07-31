<?php

namespace DansMaCulotte\Colissimo\Tests;

use DansMaCulotte\Colissimo\Exceptions\Exception;
use DansMaCulotte\Colissimo\ParcelTracking;
use DansMaCulotte\Colissimo\Resources\ParcelStatus;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class ParcelTrackingTest extends TestCase
{
    public function testGetStatusByID()
    {
        $mock = new MockHandler([
            new Response(
                200,
                ['Content-Type' => 'text/xml'],
                file_get_contents(__DIR__.'/snapshots/track_success.xml')
            ),
            new Response(
                200,
                ['Content-Type' => 'text/xml'],
                file_get_contents(__DIR__.'/snapshots/track_failure.xml')
            ),
        ]);

        $httpClient = $this->buildClientMock($mock);
        
        $parcelTracking = new ParcelTracking($this->credentials);
        $parcelTracking->httpClient = $httpClient;
        
        $result = $parcelTracking->getStatusByID('8V12345678901');
        $this->assertInstanceOf(ParcelStatus::class, $result);

        $this->expectExceptionObject(Exception::requestError($parcelTracking::ERRORS[201]));
        $parcelTracking->getStatusByID('8V12345678901');
    }
}
