<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStockPositionCreate extends FormRequest
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
            'lane' => 'nullable',
            'bookstand' => 'nullable',
            'shelf' => 'nullable',
            'position' => 'nullable',
            'position_quantity' => 'required|integer'
        ];
    }
}
