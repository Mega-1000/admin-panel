<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStockPacketCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'packetsQuantity' => 'required|integer',
            'packetName' => 'required|max:255|string',
            'products' => 'required'
        ];
    }
}
