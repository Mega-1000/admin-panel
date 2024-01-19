<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecalculateLabelsInOrdersBasedOnPeriodRequest extends FormRequest
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
            'time-from' => 'required|date',
            'time-to' => 'required|date',
            'calculate-only-with-39' => 'nullable',
        ];
    }
}
