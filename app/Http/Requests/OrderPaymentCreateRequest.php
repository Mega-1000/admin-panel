<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderPaymentCreateRequest extends FormRequest
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
            'amount' => 'nullable|regex:/^\d*([\.,]{1}\d{1,2})?$/',
            'external_payment_id' => 'nullable|string',
            'payer' => 'nullable|string',
            'operation_date' => 'nullable|date',
            'tracking_number' => 'nullable|string',
            'operation_id' => 'nullable|string',
            'declared_sum' => 'nullable|regex:/^\d*([\.,]{1}\d{1,2})?$/',
            'posting_date' => 'nullable|date',
            'operation_type' => 'nullable|string',
            'comments' => 'nullable|string'
        ];
    }
}
