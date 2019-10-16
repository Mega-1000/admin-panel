<?php

namespace App\Http\Requests\Api\Customers;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
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
        return array_merge([
            'login' => 'required|min:3',
            'password' => 'nullable|min:5',
            ],
//            $this->generateAddressValidationArray('delivery_address', 'required_with:delivery_address'),
//            $this->generateAddressValidationArray('invoice_address', 'required_with:invoice_address'),
//            $this->generateAddressValidationArray('standard_address', 'required')
            $this->generateAddressValidationArray('delivery_address', 'nullable'),
            $this->generateAddressValidationArray('invoice_address', 'nullable'),
            $this->generateAddressValidationArray('standard_address', 'nullable')
        );
    }

    protected function generateAddressValidationArray($name, $required)
    {
        return [
            $name . '.firstname' => $required . '',
            $name . '.lastname' => $required . '',
            $name . '.firmname' => $required . '',
            $name . '.nip' => 'nullable|min:6',
            $name . '.phone' => $required . '',
            $name . '.email' => $required . '|email',
            $name . '.city' => $required . '',
            $name . '.address' => $required . '',
            $name . '.postal_code' => $required . '',
        ];
    }
}
