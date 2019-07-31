<?php

namespace DansMaCulotte\Colissimo\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /** @var array */
    public $credentials = [
        'accountNumber' => 'anaccount',
        'password' => 'apassword',
    ];

    protected function buildClientMock(MockHandler $mock)
    {
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);
        return $client;
    }
}
