<?php

namespace App\DTO\ProductStocks;

use App\Http\Requests\CreateMultipleAdminOrdersRequest;

readonly class CreateMultipleOrdersDTO
{
    public function __construct(
        public string $clientEmail,
        public array $products
    )
    {
    }

    public static function fromRequest(CreateMultipleAdminOrdersRequest $request): self
    {
        return new self(
            clientEmail: $request->get('clientEmail'),
            products: $request->get('products')
        );
    }
}
