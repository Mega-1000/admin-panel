<?php declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransportPaymentsImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|max:20000000',
        ];
    }

    public function messages(): array
    {
        return [
            'max' => __('transport.errors.too-big-file'),
            'mimes' => __('transport.errors.mimes'),
        ];
    }
}
