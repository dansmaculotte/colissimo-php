<?php

namespace DansMaCulotte\Colissimo\Exceptions;

class Exception extends \Exception
{
    /**
     * @return Exception
     */
    public static function serviceUnavailable()
    {
        return new self("Colissimo Web Service is unavailable");
    }
    
    /**
     * @param string $message
     * @return Exception
     */
    public static function requestError(string $message)
    {
        return new self("Request returned error: ${message}");
    }
}
