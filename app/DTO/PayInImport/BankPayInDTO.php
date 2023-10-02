<?php

namespace App\DTO\PayInImport;

use App\Traits\ArrayOperations;

final class BankPayInDTO
{
    use ArrayOperations;

    public function __construct(
        public ?int              $orderId = null,
        public ?array            $data = [],
        public ?string           $message = '',
        public string|float|null $kwota = 0.0,
        public                   $operation_type = '',
        public ?string           $opis_operacji = '',
        public ?string           $tytul = '',
        public ?string           $data_ksiegowania = '',
        public array             $wholeDataArray = [],
    ) {}

    public function toArray(): array
    {
        return $this->wholeDataArray;
    }

    public function setOperationType(string $operation_type): void
    {
        $this->operation_type = $operation_type;
    }
}
