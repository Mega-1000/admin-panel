<?php

namespace App\Http\Requests\Api\Customers;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerAddressRequest extends FormRequest
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
            'firstname' => 'nullable|string',
            'lastname' => 'nullable|string',
            'firmname' => 'nullable|string',
            'nip' => 'nullable|min:6',
            'phone' => 'required',
            'email' => 'required|email',
            'city' => 'required|string',
            'address' => 'required|string',
            'flat_number' => 'required|string',
            'postal_code' => 'required|string',
            'country_id' => 'exists:countries,id',
        ];
    }
}
