<?php

use PHPUnit\Framework\TestCase;
use DansMaCulotte\Colissimo\Client;

class ClientTest extends TestCase
{
    public function testCheckWebServiceStatus()
    {
        $result = Client::checkWebServiceStatus();

        $this->assertTrue($result);
    }
}