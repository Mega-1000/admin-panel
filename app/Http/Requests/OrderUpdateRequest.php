<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderUpdateRequest extends FormRequest
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
            'packing_warehouse_cost' => 'nullable|regex:/^\d*(\.\d{2})?$/',
            'warehouse_cost' => 'nullable|regex:/^\d*(\.\d{2})?$/',
            'additional_service_cost' => 'nullable|regex:/^-?\d*(\.\d{2})?$/',
            'correction_amount' => 'nullable|regex:/^\d*(\.\d{2})?$/',
            'id' => 'required|array|min:1',
            'return_value_.*' => 'nullable|int',
            'preliminary_buying_document_number' => 'nullable|string|max:255',
            'buying_document_number' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => 'Musisz wybrać chociaż jeden produkt',
        ];
    }
}
