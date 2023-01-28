<?php

namespace App\DTO\Schenker\Request;

use App\DTO\BaseDTO;
use JsonSerializable;

class GetTrackingRequestDTO extends BaseDTO implements JsonSerializable
{

    /**
     * „DWB” – Domestic WayBill – numer listu przewozowego
     * „PKG” – Package ID – identyfikator opakowania (SSCC)
     * „SHP” – Shipper reference - numer referencyjny nadany przez zleceniodawcę
     * „CGN” – Consignee reference – numer referencyjny nadany przez odbiorcę
     * „COR” – Client order reference – numer referencyjny zlecenia klienta
     * „FF” – Freight forwarder’s reference – numer referencyjny przesyłki wg Schenker
     */
    const REF_TYPE_DEFAULT = 'FF';

    private $referenceType;
    private $referenceNumber;

    public function __construct($referenceNumber, string $referenceType = self::REF_TYPE_DEFAULT)
    {
        $this->referenceType = $referenceType;
        $this->referenceNumber = $referenceNumber;
    }

    public function jsonSerialize(): array
    {
        return [
            'referenceType' => $this->referenceType,
            'referenceNumber' => $this->referenceNumber,
        ];
    }


}
