<?php

namespace App\DTO\Schenker;

use App\DTO\BaseDTO;

class AddressDTO extends BaseDTO implements JsonSerializable
{
    protected $clientId;
    protected $clientLocalizationNumber;
    protected $name;
    protected $postCode;
    protected $city;
    protected $street;
    protected $houseNumber;
    protected $localNumber;
    protected $phone;
    protected $nip;
    protected $contactPerson;
    protected $email;
    protected $palletId;

    public function __construct(
        string  $clientId,
        ?string $clientLocalizationNumber,
        string  $name,
        string  $postCode,
        string  $city,
        string  $street,
        string  $houseNumber,
        ?string $localNumber,
        ?string $phone,
        string  $nip,
        string  $contactPerson,
        ?string $email,
        ?string $palletId
    )
    {
        $this->clientId = $clientId;
        $this->clientLocalizationNumber = $clientLocalizationNumber;
        $this->name = $name;
        $this->postCode = $postCode;
        $this->city = $city;
        $this->street = $street;
        $this->houseNumber = $houseNumber;
        $this->localNumber = $localNumber;
        $this->phone = $phone;
        $this->nip = $nip;
        $this->contactPerson = $contactPerson;
        $this->email = $email;
        $this->palletId = $palletId;
    }


    public function jsonSerialize(): array
    {
        $localAndHouseNumber = $this->houseNumber . ($this->localNumber !== null && $this->localNumber !== '' ? '/' . $this->localNumber : '');
        $payerData = [
            'clientId' => $this->clientId,
            'name1' => $this->substrText($this->name),
            'postCode' => $this->getOnlyNumbers($this->postCode),
            'city' => $this->substrText($this->city, 35),
            'street' => $this->substrText($this->street . ' ' . $localAndHouseNumber),
            'nip' => $this->getOnlyNumbers($this->postCode),
            'contactPerson' => $this->substrText($this->contactPerson),
        ];
        if (strlen($this->name) > 60) {
            $payerData['name2'] = $this->substrText($this->name, 60, 60);
        }
        if ($this->street === '') {
            $payerData['street'] = $this->substrText($this->city . ' ' . $localAndHouseNumber);
        }
        $this->phone = $this->substrText($this->phone ?? '', 35);
        $this->email = $this->substrText($this->email ?? '');
        $this->palletId = $this->substrText($this->palletId ?? '', 7);

        $this->optionalFields = [
            'phone' => 'phone',
            'email' => 'email',
            'palletId' => 'palletId',
        ];

        return array_merge($payerData, $this->getOptionalFilledFields());
    }
}
