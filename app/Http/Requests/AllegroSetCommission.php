<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class AllegroSetCommission extends FormRequest
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
            'file' => 'required|file|max:20000000',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        if ($validator->fails()) {
            return redirect()->route('orders.index')->with([
                'message' => __('transport.errors.too-big-file'),
                'alert-type' => 'error'
            ]);
        }
    }
}
