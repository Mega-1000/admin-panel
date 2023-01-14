<?php

namespace App\Exceptions;

use App\Utils\SoapParams;
use Exception;
use Throwable;

class SapException extends Exception
{
    public function __construct(string $message = '', int $code = 0, ?SoapParams $soapParams = null, string $wsdlFilePath = '', ?Throwable $previous = null)
    {
        if ($soapParams !== null) {
            $message .= 'Params send: ' . json_encode($soapParams->getParams()) . PHP_EOL;
        }
        if ($wsdlFilePath !== '') {
            $message .= 'WSDL file name: ' . basename($wsdlFilePath) . PHP_EOL;
        }

        parent::__construct($message, $code, $previous);
    }
}
