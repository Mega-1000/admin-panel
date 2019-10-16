<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerCreateRequest extends FormRequest
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
            'login' => 'required|unique:customers|min:3',
            'password' => 'required|min:5',
            'status' => 'in:ACTIVE,PENDING',
            'standard_firstname' => 'nullable|regex:/^[a-zA-Z]+$/u',
            'standard_lastname' => 'nullable|regex:/^[a-zA-Z]+$/u',
            'standard_phone' => 'nullable',
            'standard_city' => 'nullable|regex:/^[a-zA-Z]+$/u',
            'standard_email' => 'nullable|email|min:5',
            'standard_postal_code' => 'nullable|regex:/[0-9]{2}\-[0-9]{3}/',
            'standard_nip' => 'nullable|regex:/[0-9]{9}/',
            'invoice_firstname' => 'nullable|regex:/^[a-zA-Z]+$/u',
            'invoice_lastname' => 'nullable|regex:/^[a-zA-Z]+$/u',
            'invoice_phone' => 'nullable',
            'invoice_city' => 'nullable|regex:/^[a-zA-Z]+$/u',
            'invoice_email' => 'nullable|email|min:5',
            'inboice_postal_code' => 'nullable|regex:/[0-9]{2}\-[0-9]{3}/',
            'invoice_nip' => 'nullable|regex:/[0-9]{9}/',
            'delivery_firstname' => 'nullable|regex:/^[a-zA-Z]+$/u',
            'delivery_lastname' => 'nullable|regex:/^[a-zA-Z]+$/u',
            'delivery_phone' => 'nullable',
            'delivery_city' => 'nullable|regex:/^[a-zA-Z]+$/u',
            'delivery_email' => 'nullable|email|min:5',
            'delivery_postal_code' => 'nullable|regex:/[0-9]{2}\-[0-9]{3}/',
            'delivery_nip' => 'nullable|regex:/[0-9]{9}/',
        ];
    }
}
