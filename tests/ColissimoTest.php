<?php

namespace DansMaCulotte\Colissimo\Tests;

use DansMaCulotte\Colissimo\Colissimo;
use DansMaCulotte\Colissimo\Exceptions\Exception;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class ColissimoTest extends TestCase
{
    public function testCheckWebServiceStatus()
    {
        $mock = new MockHandler([
            new Response(
                200,
                ['Content-Type' => 'text/html'],
                '[OK]'
            ),
            new Response(500),
            new Response(
                200,
                ['Content-Type' => 'text/html'],
                '[NOTOK]'
            )
        ]);
        
        $httpClient = $this->buildClientMock($mock);
        
        $colissimo = new Colissimo();
        $colissimo->httpClient = $httpClient;

        $result = $colissimo->checkWebServiceStatus();
        $this->assertTrue($result);

        $this->expectExceptionObject(Exception::serviceUnavailable());
        $colissimo->checkWebServiceStatus();

        $this->expectExceptionObject(Exception::serviceUnavailable());
        $colissimo->checkWebServiceStatus();
    }
}
