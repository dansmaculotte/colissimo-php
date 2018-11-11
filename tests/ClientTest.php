<?php

use PHPUnit\Framework\TestCase;
use DansMaCulotte\ColissimoWebServices\Client;

class ClientTest extends TestCase
{
    public function testCheckWebServiceStatus()
    {
        $result = Client::checkWebServiceStatus();

        $this->assertTrue($result);
    }
}