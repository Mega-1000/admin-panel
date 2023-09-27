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
    ) {}

    public function toArray(): array
    {
        return [
            'orderId' => $this->orderId,
            'data' => $this->data,
            'message' => $this->message,
            'kwota' => $this->kwota,
            'operation_type' => $this->operation_type,
            'opis_operacji' => $this->opis_operacji,
            'tytul' => $this->tytul,
            'data_ksiegowania' => $this->data_ksiegowania,
        ];
    }

    public function setOperationType(string $operation_type): void
    {
        $this->operation_type = $operation_type;
    }
}
