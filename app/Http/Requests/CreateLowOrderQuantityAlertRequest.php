<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateLowOrderQuantityAlertRequest extends FormRequest
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
            'item_names' => 'required',
            'min_quantity' => 'required|numeric',
            'message' => 'required',
            'delay_time' => 'required|numeric',
            'title' => 'required|string',
        ];
    }
}
