<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderUpdateRequest extends FormRequest
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
            'packing_warehouse_cost' => 'nullable|regex:/^\d*(\.\d{2})?$/',
            'warehouse_cost' => 'nullable|regex:/^\d*(\.\d{2})?$/',
            'additional_service_cost' => 'nullable|regex:/^-?\d*(\.\d{2})?$/',
            'correction_amount' => 'nullable|regex:/^\d*(\.\d{2})?$/',
            'id' => 'required|array|min:1',
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'Musisz wybrać chociaż jeden produkt',
        ];
    }
}
