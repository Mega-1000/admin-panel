<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FirmCreateRequest extends FormRequest
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
            'name' => 'required|min:3',
            'short_name' => 'nullable',
            'email' => 'required|email|unique:firms',
            'firm_type' => 'required|in:PRODUCTION,DELIVERY,OTHER',
            'secondary_email' => 'nullable|email',
            'phone' => 'nullable',
            'secondary_phone' => 'nullable',
            'nip' => 'nullable|regex:/[0-9]{9}/',
            'account_number' => 'nullable|min:10',
            'city' => 'nullable',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
            'postal_code' => 'nullable|regex:/[0-9]{2}\-[0-9]{3}/',
        ];
    }
}
