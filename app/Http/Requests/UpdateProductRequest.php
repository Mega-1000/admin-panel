<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'save_name' => 'required',
            'save_image' => 'required',
            'image' => 'nullable',
            'description' => 'nullable|string',
            'youtube' => 'nullable',
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $validated = parent::validated();
        $validated['save_name'] = $validated['save_name'] === 'true';
        $validated['save_image'] = $validated['save_image'] === 'true';
        if($validated['youtube']){
            $validated['youtube'] = array_filter(array_map('array_filter', json_decode($validated['youtube'], true)));
        }
        
        return $validated;
    }
}
