<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VisibilitiesCreateRequest extends FormRequest
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
        $uniq = Rule::unique('column_visibilities')->where('role_id',$this->route('role_id'))->where('module_id',$this->route('module_id'));

        return [
            'display_name' => ['required', $uniq],
            'columnName' => ['required'],
        ];
    }
}
