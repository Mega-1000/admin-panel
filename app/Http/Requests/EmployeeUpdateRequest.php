<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeUpdateRequest extends FormRequest
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
            'email' => 'required|email|min:5',
            'firstname' => 'nullable|regex:/^[a-zA-Z]+$/u',
            'lastname' => 'nullable|regex:/^[a-zA-Z]+$/u',
            'phone' => 'nullable|regex:/[0-9]{9}/',
            'job_position' => 'in:SECRETARIAT,CONSULTANT,STOREKEEPER,SALES',
            'radius' => 'nullable|numeric',
            'status' => 'in:ACTIVE,PENDING',
        ];
    }
}
