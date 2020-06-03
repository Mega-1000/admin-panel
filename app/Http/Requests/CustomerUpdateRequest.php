<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerUpdateRequest extends FormRequest
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
            'standard_firstname' => 'nullable|regex:/^[a-zA-Z]+$/u',
            'standard_lastname' => 'nullable|regex:/^[a-zA-Z]+$/u',
            'standard_phone' => 'nullable',
            'standard_city' => 'nullable|min:3|string',
            'standard_email' => 'nullable|email|min:5',
            'standard_postal_code' => 'nullable|min:6',
            'standard_nip' => 'nullable|regex:/[0-9]{9}/',
            'invoice_firstname' => 'nullable|regex:/^[a-zA-Z]+$/u',
            'invoice_lastname' => 'nullable|regex:/^[a-zA-Z]+$/u',
            'invoice_phone' => 'nullable',
            'invoice_city' => 'nullable|min:3|string',
            'invoice_email' => 'nullable|email|min:5',
            'inboice_postal_code' => 'nullable|min:6',
            'invoice_nip' => 'nullable|regex:/[0-9]{9}/',
            'delivery_firstname' => 'nullable|regex:/^[a-zA-Z]+$/u',
            'delivery_lastname' => 'nullable|regex:/^[a-zA-Z]+$/u',
            'delivery_phone' => 'nullable',
            'delivery_city' => 'nullable|min:3|string',
            'delivery_email' => 'nullable|email|min:5',
            'delivery_postal_code' => 'nullable|min:6',
            'delivery_nip' => 'nullable|regex:/[0-9]{9}/',
        ];
    }
}
