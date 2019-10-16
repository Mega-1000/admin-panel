<?php

namespace App\Http\Requests\Api\Orders;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderMessageRequest extends FormRequest
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
            'title' => 'required|min:3',
            'front_order_id' => 'required',
            'message' => 'required',
            'additional_description' => 'nullable',
            'employee_id' => 'nullable',
            'status' => 'in:OPEN,CLOSED',
            'type' => 'in:GENERAL,SHIPPING,WAREHOUSE,COMPLAINT',
            'files.*.attachment' => 'required_with:files',
            'files.*.attachment_name' => 'required_with:files',
        ];
    }
}
