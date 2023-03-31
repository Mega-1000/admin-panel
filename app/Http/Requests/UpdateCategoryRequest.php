<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
            'description' => 'required|string',
            'name' => 'required|string',
            'category' => 'required|exists:categories,id',
            'save_name' => 'required|boolean',
            'save_description' => 'required|boolean',
            'save_image' => 'required|boolean',
        ];
    }
}
