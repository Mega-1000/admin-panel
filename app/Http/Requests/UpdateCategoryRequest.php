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
            'description' => 'nullable|string',
            'name' => 'required|string',
            'category' => 'required|exists:categories,id',
            'save_name' => 'required|boolean',
            'save_description' => 'required|boolean',
            'save_image' => 'required|boolean',
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated($key, $default);

        // Check if 'description' key exists before accessing it
        if (isset($validated['description'])) {
            $validated['description'] = nl2br($validated['description']);
        }

        return $validated;
    }

}
