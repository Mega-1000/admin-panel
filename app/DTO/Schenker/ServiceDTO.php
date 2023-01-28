<?php

namespace App\DTO\Schenker;

use App\DTO\BaseDTO;
use App\Enums\Schenker\SupportedService;
use App\Exceptions\SchenkerException;
use JsonSerializable;

class ServiceDTO extends BaseDTO implements JsonSerializable
{

    protected $serviceCode;

    /**
     * Parametr 1 jest wymagany tylko dla wybranych usług.
     * W przypadku usług 8 i 9 parametrem jest kwota podana w PLN w formacie ZZZZZGG (Z – złotówki, G – grosze) bez
     * znaków rozdzielających. Zarówno kwota pobrania, jak i wartość deklarowana, musi być wyrażona wyłącznie w PLN.
     * Wartości w innych walutach powinny być przeliczone na PLN.
     * Dla usług 27,28,29 w polu tym powinna znajdować się liczba sztuk (nie stron) dokumentów, które są fizycznie
     * dołączone do przesyłki.
     */
    protected $mainParameter;

    /**
     * Wymagany dla usług 27 i 28 – typ dokumentów zwrotnych. Dokumenty spięte zszywaczem są traktowane jako jeden.
     * W miarę możliwości powinny być stosowane skróty.
     * Przykłady:
     * „FZ” – (faktura VAT)
     * „WZ” – (wydanie magazynowe)
     * „WZ+FV” – (wydanie magazynowe + faktura)
     * „LP+WZ” – (list przewozowy + wydanie magazynowe)
     * „PROT.PAL” – protokół paletowy
     */
    protected $documentType;

    /**
     * Parametr wymagany dla usług 27 i 28 – nr/opis dokumentów zwrotnych. Nr faktury (FV), Nr WZ itp.
     * Przykłady:
     * FV/10/12/2011
     * WZ/0151/441/09
     * 10/2011
     */
    protected $documentNumber;

    /**
     * @param string|int|null $mainParameter
     */
    public function __construct(int $serviceCode, ?string $mainParameter, ?string $documentType, ?string $documentNumber)
    {
        $this->serviceCode = $serviceCode;
        $this->mainParameter = $mainParameter;
        $this->documentType = $documentType;
        $this->documentNumber = $documentNumber;
    }

    /**
     * @throws SchenkerException
     */
    public function jsonSerialize(): array
    {
        if (SupportedService::isSupportedService($this->serviceCode) === false) {
            throw new SchenkerException('Service "' . $this->serviceCode . '" is not supported', 500);
        }

        $serviceData = [
            'code' => $this->serviceCode,
        ];

        $this->mainParameter = $this->substrText($this->mainParameter ?? '');
        $this->documentType = $this->substrText($this->documentType ?? '');
        $this->documentNumber = $this->substrText($this->documentNumber ?? '');

        $this->optionalFields = [
            'parameter1' => 'mainParameter',
            'parameter2' => 'documentType',
            'parameter3' => 'documentNumber'
        ];

        return array_merge($serviceData, $this->getOptionalFilledFields());
    }


}
