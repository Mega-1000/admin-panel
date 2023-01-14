<?php

namespace App\DTO\Schenker;

use App\DTO\BaseDTO;
use JsonSerializable;

class SenderDTO extends BaseDTO implements JsonSerializable
{

    private $clientId;
    private $clientLn;
    private $name;
    private $postCode;
    private $city;
    private $street;
    private $phone;
    private $nip;
    private $contactPerson;
    private $email;
    private $paletteId;

    /**
     * @param string $clientId - required, client ID from Schenker System
     * @param ?String $clientLn - optional - international localization number
     * @param string $name - required - sender name, separated to the name1 and name2, max 120 chars
     * @param string $postCode - required - formatted to format 00731
     * @param string $city - required - max chars 35
     * @param string $street - required - max chars 60
     * @param ?String $phone - optional - max chars 35
     * @param string $nip - required - only numbers
     * @param string $contactPerson - required - max chars 60
     * @param ?string $email - optional - max chars 60
     * @param ?string $paletteId - optional
     */
    public function __construct(
        string  $clientId,
        ?string $clientLn,
        string  $name,
        string  $postCode,
        string  $city,
        string  $street,
        ?string $phone,
        string  $nip,
        string  $contactPerson,
        ?string $email,
        ?string $paletteId
    )
    {
        $this->clientId = $clientId;
        $this->clientLn = $clientLn;
        $this->name = $name;
        $this->postCode = $postCode;
        $this->city = $city;
        $this->street = $street;
        $this->phone = $phone;
        $this->nip = $nip;
        $this->contactPerson = $contactPerson;
        $this->email = $email;
        $this->paletteId = $paletteId;
        $this->optionalFields = [
            'clientLn' => 'clientLn',
            'phone' => 'phone',
            'email' => 'email',
            'paletteId' => 'paletteId',
        ];
    }


    public function jsonSerialize(): array
    {
        $senderData = [
            'clientId' => $this->clientId,
            'name1' => substr($this->name, 0, 60),
            'name2' => substr($this->name, 60, 60),
            'postCode' => preg_replace("/[^\d]+/", '', $this->postCode),
            'city' => substr($this->city, 0, 35),
            'street' => substr($this->street, 0, 60),
            'nip' => preg_replace('/[^\d]+/', '', $this->nip),
            'contactPerson' => substr($this->contactPerson, 0, 60),
        ];
        $optionalFields = $this->getOptionalFilledFields();
        if (count($optionalFields) > 0) {
            $senderData = array_merge($senderData, $optionalFields);
        }

        return $senderData;
    }
}
