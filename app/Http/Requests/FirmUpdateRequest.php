<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FirmUpdateRequest extends FormRequest
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
            'name' => 'nullable|min:3',
            'short_name' => 'nullable',
            'email' => 'nullable|email',
            'status' => 'in:ACTIVE,PENDING',
            'firm_type' => 'required|in:PRODUCTION,DELIVERY,OTHER',
            'nip' => 'nullable|regex:/[0-9]{9}/',
            'account_number' => 'nullable|min:10',
            'city' => 'nullable',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
            'postal_code' => 'nullable|regex:/[0-9]{2}\-[0-9]{3}/',
            'secondary_email' => 'nullable|email',
            'complaint_email' => 'nullable|email',
            'phone' => 'nullable',
            'secondary_phone' => 'nullable',
        ];
    }
}
