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
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|min:3',
            'short_name' => 'nullable',
            'email' => 'required|email|unique:firms',
            'firm_type' => 'required|in:PRODUCTION,DELIVERY,OTHER',
            'secondary_email' => 'required|email',
            'complaint_email' => 'required|email',
            'phone' => 'required',
            'secondary_phone' => 'required',
            'nip' => 'required|regex:/[0-9]{9}/',
            'account_number' => 'required|min:10',
            'city' => 'required',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
            'postal_code' => 'required|regex:/[0-9]{2}\-[0-9]{3}/',
        ];
    }
}
