<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $addressRules = [
            'name' => 'required',
            'surname' => 'required',
            'street' => 'required',
            'house_number' => 'required',
            'flat_number' => 'nullable',
            'city' => 'required',
            'postal_code' => 'required',
            'phone' => 'required',
            'email' => 'required',
        ];

        return [
            "standardAddress" => $addressRules,
            "invoiceAddress" => $addressRules,
            "deliveryAddress" => $addressRules,
        ];
    }
}
