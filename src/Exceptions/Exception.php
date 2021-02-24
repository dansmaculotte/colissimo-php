<?php

namespace DansMaCulotte\Colissimo\Exceptions;

class Exception extends \RuntimeException
{
    public static function serviceUnavailable(): Exception
    {
        return new self("Colissimo Web Service is unavailable");
    }

    public static function requestError(string $message): Exception
    {
        return new self("Request returned error: ${message}");
    }
}