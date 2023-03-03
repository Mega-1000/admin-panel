<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailSettingsCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|in:NEW,PRODUCED,PICKED_UP,PROVIDED',
            'time' => 'required|numeric',
            'title' => 'required',
            'content' => 'required'
        ];
    }
}
