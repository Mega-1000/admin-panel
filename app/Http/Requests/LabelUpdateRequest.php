<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LabelUpdateRequest extends FormRequest
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
            'order' => 'nullable',
            'color' => 'required|size:6',
            'status' => 'in:ACTIVE,PENDING',
            'label_group_id' => 'nullable|numeric',
            'icon_name' => 'nullable',
            'labels_after_addition.*' => 'nullable|numeric',
            'labels_after_removal.*' => 'nullable|numeric',
            'timed_labels' => 'nullable',
        ];
    }
}
