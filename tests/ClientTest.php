<?php

use DansMaCulotte\Colissimo\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testCheckWebServiceStatus()
    {
        $result = Client::checkWebServiceStatus();

        $this->assertTrue($result);
    }
}
