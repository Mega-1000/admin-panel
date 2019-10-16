<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WarehouseCreateRequest extends FormRequest
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
            'symbol' => 'required',
            'status' => 'in:ACTIVE,PENDING',
            'postal_code' => 'nullable|regex:/[0-9]{2}\-[0-9]{3}/',
            'email' => 'nullable|email|min:5',
        ];
    }
}
