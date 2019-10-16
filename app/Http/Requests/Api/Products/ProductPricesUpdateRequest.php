<?php

namespace App\Http\Requests\Api\Products;

use Illuminate\Foundation\Http\FormRequest;

class ProductPricesUpdateRequest extends FormRequest
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
            '*.date_of_price_change' => 'required|date|after:today',
            '*.date_of_the_new_prices' => 'required|date',
            '*.value_of_price_change_data_first' => 'required',
            '*.value_of_price_change_data_second' => 'required',
        ];
    }

}
