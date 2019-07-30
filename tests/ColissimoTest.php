<?php

use DansMaCulotte\Colissimo\Colissimo;
use PHPUnit\Framework\TestCase;

class ColissimoTest extends TestCase
{
    public function testCheckWebServiceStatus()
    {
        $result = Colissimo::checkWebServiceStatus();

        $this->assertTrue($result);
    }
}
