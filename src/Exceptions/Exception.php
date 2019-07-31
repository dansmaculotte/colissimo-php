<?php

namespace DansMaCulotte\Colissimo\Exceptions;

class Exception extends \Exception
{
    /**
     * @param string $message
     * @return Exception
     */
    public static function requestError(string $message)
    {
        return new self("Request returned error: ${message}");
    }
}
