<?php declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MasterPaymentCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => 'required',
            'notices' => 'max:255',
            'promise_date' => 'date',
            'created_at' => 'required|date',
            'payment-type' => 'required',
            'customer_id' => 'required'
        ];
    }
}
