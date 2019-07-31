<?php

namespace DansMaCulotte\Colissimo\Tests;

use DansMaCulotte\Colissimo\Colissimo;

class ColissimoTest extends TestCase
{
    public function testCheckWebServiceStatus()
    {
        $result = Colissimo::checkWebServiceStatus();

        $this->assertTrue($result);
    }
}
