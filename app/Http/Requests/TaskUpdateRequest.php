<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskUpdateRequest extends FormRequest
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
            'warehouse_id' => 'required|numeric',
            'user_id' => 'required|numeric',
            'order_id' => 'nullable',
            'warehouse_value' => 'nullable|numeric',
            'consultant_value' => 'nullable|numeric',
            'new_group' => 'nullable',
            'view_type' => 'nullable',
            'active_start' => 'nullable',
            'name' => 'nullable',
            'start' => 'nullable',
            'end' => 'nullable',
            'color' => 'nullable',
            'consultant_notice' => 'nullable',
            'warehouse_notice' => 'nullable',
            'shipment_date' => 'nullable',
            'update' => 'nullable'
        ];
    }
}
