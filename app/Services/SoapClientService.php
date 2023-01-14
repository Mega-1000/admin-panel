<?php

namespace App\Services;

use App\Exceptions\SapException;
use App\Utils\SoapParams;
use SoapClient;
use SoapFault;

class SoapClientService
{
    private $client = null;
    private $wsdlFile;
    private $user;
    private $password;

    public final function __construct()
    {
        $this->user = config('integrations.schenker.user_name');
        $this->password = config('integrations.schenker.user_password');
    }

    /**
     * @throws SapException
     */
    public static final function sendRequest(string $wsdlFile, string $action, SoapParams $soapParams): array
    {
        try {
            $soapClient = new self();
            $soapClient->setWsdl($wsdlFile);

            return $soapClient->makeRequest($action, $soapParams);
        } catch (SoapFault $exception) {
            throw new SapException('Soap client error: ' . $exception->getMessage() . PHP_EOL, $exception->getCode(), $soapParams, $wsdlFile, $exception->getPrevious());
        }
    }

    /**
     * @throws SoapFault
     */
    public final function setWsdl(string $wsdlFileFullPath): void
    {
        $needRecreateSoapClient = $this->wsdlFile != $wsdlFileFullPath;
        $this->wsdlFile = $wsdlFileFullPath;
        $this->getSoapClient($needRecreateSoapClient);
    }

    /**
     * @throws SoapFault
     */
    private function getSoapClient(bool $forceRecreate = false): SoapClient
    {
        if ($this->client === null || $forceRecreate === true) {
            return $this->client = new SoapClient($this->wsdlFile, [
                'cache_wsdl' => WSDL_CACHE_NONE,
                'login' => $this->user,
                'password' => $this->password,
                'features' => SOAP_SINGLE_ELEMENT_ARRAYS,
            ]);
        }

        return $this->client;
    }

    /**
     * @throws SoapFault
     */
    public final function makeRequest(string $action, SoapParams $params): array
    {
        $client = $this->getSoapClient();

        $paramsData = $params->getParams();
        $response = $client->$action($paramsData);

        return json_decode(json_encode($response ?? []), true);
    }
}
