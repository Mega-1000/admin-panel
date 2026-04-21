<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStockUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'status' => 'in:ACTIVE,PENDING',
            'quantity' => 'nullable|integer',
            'min_quantity' => 'nullable|integer',
            'unit' => 'nullable|string',
            'start_quantity' => 'nullable|integer',
            'number_on_a_layer' => 'nullable|integer',
            'stock_product' => 'nullable',
        ];
    }
}
