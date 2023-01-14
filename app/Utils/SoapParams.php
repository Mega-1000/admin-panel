<?php

namespace App\Utils;

use App\Exceptions\SoapParamsException;

class SoapParams
{

    private $params = [];

    /**
     * @throws SoapParamsException
     */
    public function setParamDTOObject(string $paramName, BaseDTO $dtoObject): self
    {
        if (!array_key_exists($paramName, $this->params)) {
            $this->params[$paramName] = json_decode(json_encode($dtoObject), true);

            return $this;
        }
        throw new SoapParamsException('There already defined param with name: ' . $paramName, 500);
    }

    /**
     * @throws SoapParamsException
     */
    public function setParam(string $paramName, $paramValue): self
    {
        if (!array_key_exists($paramName, $this->params)) {
            $this->params[$paramName] = $paramValue;

            return $this;
        }
        throw new SoapParamsException('There already defined param with name: ' . $paramName, 500);
    }

    public function getParams(): array
    {
        return $this->params;
    }

}
