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
            'space' => 'required|string',
        ];
    }

    public function validated($key = null, $default = null): array
    {
        $data = parent::validated();
        $data['message'] = str_replace("\n", '<br />',  $data['message']);


        return array_merge($data);
    }
}
