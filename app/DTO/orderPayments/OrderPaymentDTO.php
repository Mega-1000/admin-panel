<?php

namespace App\DTO\orderPayments;

use App\Entities\OrderPayment;

class OrderPaymentDTO
{
    public function __construct(
        public string $amount,
        public readonly ?string $payment_date,
        public readonly ?string $payment_type,
        public readonly ?string $status,
        public readonly ?string $token,
        public readonly ?string $operation_type,
    )
    {

    }

    /**
     * @param array $array
     *
     * @return static
     */
    public static function fromArray(array $array): self
    {
        return new self(
            $array['amount'],
            $array['payment_date'],
            $array['payment_type'],
            $array['status'],
            $array['token'],
            $array['operation_type'],
        );
    }

    /**
     * @param OrderPayment $payment
     *
     * @return static
     */
    public static function fromPayment(OrderPayment $payment, int $amount): self
    {
        return new self(
            $amount,
            $payment->payment_date,
            $payment->payment_type,
            $payment->status,
            $payment->token,
            $payment->operation_type,
        );
    }

    /**
     * Get the value of operation_type
     *
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * Get the value of operation_type
     *
     * @param string $amount
     *
     * @return self
     */
    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }
}
