<?php

namespace App\Exceptions;

use App\Utils\SoapParams;
use Exception;
use SoapFault;
use Throwable;

class SoapException extends Exception
{
    public $errorToReturn = '';

    public function __construct(SoapFault $soapFault, string $message = '', int $code = 0, ?SoapParams $soapParams = null, string $wsdlFilePath = '', ?Throwable $previous = null)
    {
        if ($soapParams !== null) {
            $message .= 'Params send: ' . json_encode($soapParams->getParams()) . PHP_EOL;
        }
        if ($wsdlFilePath !== '') {
            $message .= 'WSDL file name: ' . basename($wsdlFilePath) . PHP_EOL;
        }
        
        if (($soapFault->detail ?? null) !== null) {
            $errors = json_decode(json_encode($soapFault->detail), true);
            if (array_key_exists('transportOrderFaultList', $errors)) {

                $errorToReturn = '';
                if (array_key_exists('transportOrderFaultRow', $errors['transportOrderFaultList'])) {
                    if (array_key_exists('errorCode', $errors['transportOrderFaultList']['transportOrderFaultRow'])) {
                        $error = $errors['transportOrderFaultList']['transportOrderFaultRow'];
                        $this->errorToReturn = $error['errorCode'] . ": " . $error['errorMessage'];
                    } else if (is_array($errors['transportOrderFaultList']['transportOrderFaultRow'])) {
                        foreach ($errors['transportOrderFaultList']['transportOrderFaultRow'] as $row) {
                            $errorToReturn .= $row['errorCode'] . ': ' . $row['errorMessage'] . "<br/>";
                        }
                        $this->errorToReturn = $errorToReturn;
                    }
                }
            }
        }

        parent::__construct($message, $code, $previous);
    }
}
