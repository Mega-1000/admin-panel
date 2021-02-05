<?php
declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderPackageCostsUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'templateList' => 'nullable|numeric',
            'cost_for_client' => 'required|numeric',
            'cost_for_company' => 'required|numeric',
            'changePackageCostId' => 'required|numeric',
        ];
    }
}
