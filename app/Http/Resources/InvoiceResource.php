<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'name' => $this->getFileName(),
            'url' => $this->getUrl(),
        ];
    }
}
