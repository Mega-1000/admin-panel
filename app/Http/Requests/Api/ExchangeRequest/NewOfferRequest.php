<?php

namespace App\Http\Requests\Api\ExchangeRequest;

use Illuminate\Foundation\Http\FormRequest;

class NewOfferRequest extends FormRequest
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
            'firm_name' => 'required',
            'street' => 'required',
            'number' => 'required',
            'postal_code' => 'required',
            'city' => 'required',
            'nip' => 'required',
            'account_number' => 'required',
            'phone_number' => 'required',
            'contact_person' => 'required',
            'email' => 'required',
            'comments' => 'nullable',
            'driver_first_name' => 'required',
            'driver_last_name' => 'required',
            'driver_phone_number' => 'required',
            'driver_document_number' => 'required',
            'driver_car_registration_number' => 'required',
            'driver_arrival_date' => 'required',
            'driver_approx_arrival_time' => 'required',
        ];
    }
}
