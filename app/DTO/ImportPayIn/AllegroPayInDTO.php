<?php 

namespace App\DTO\ImportPayIn;

final class AllegroPayInDTO {
    public function __construct(
        public readonly string $date,
        public readonly string $operation,
        public readonly string $allegroIdentifier,
        public readonly string $operator,
        public readonly string $buyer,
        public string $amount,
        public readonly string $balance,
        public readonly string $offer,
        public readonly string $deliveryCost,
        public readonly string $accountedDate = '',
    ) {}

    public static $headers = [
        'data',
        'data_zaksiegowania',
        'identyfikator',
        'operacja',
        'operator',
        'kupujacy',
        'oferta',
        'dostawa',
        'kwota',
        'saldo',
    ];

    public function toArray(): array {
        return array_combine(self::$headers, [
            $this->date,
            $this->accountedDate,
            $this->allegroIdentifier,
            $this->operation,
            $this->operator,
            $this->buyer,
            $this->offer,
            $this->deliveryCost,
            $this->amount,
            $this->balance,
        ]);
    }
}
