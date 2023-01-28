<?php

namespace App\DTO\Schenker;

class RecipientDTO extends AddressDTO
{

    public function jsonSerialize(): array
    {
        $localAndHouseNumber = $this->houseNumber . ($this->localNumber !== null && $this->localNumber !== '' ? '/' . $this->localNumber : '');
        $payerData = [
            'name1' => $this->substrText($this->name),
            'postCode' => $this->getOnlyNumbers($this->postCode),
            'city' => $this->substrText($this->city, 35),
            'street' => $this->substrText($this->street . ' ' . $localAndHouseNumber),
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
        $this->clientId = $this->getOnlyNumbers($this->clientId);
        $this->nip = $this->getOnlyNumbers($this->nip);

        $this->optionalFields = [
            'clientId' => 'clientId',
            'phone' => 'phone',
            'email' => 'email',
            'palletId' => 'palletId',
            'nip' => 'nip',
        ];

        return array_merge($payerData, $this->getOptionalFilledFields());
    }

}
