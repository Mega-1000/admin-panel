<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CurierUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'courier_name' => 'required',
            'courier_key' => 'required',
            'item_number' => 'required|numeric',
        ];
    }
}
