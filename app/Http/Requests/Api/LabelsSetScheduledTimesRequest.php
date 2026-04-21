<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LabelsSetScheduledTimesRequest extends FormRequest
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
            'sendDates.*.date' => 'required',
            'sendDates.*.id' => 'required',
            'sendDates.*.labels_timed_after_addition_id' => 'required',
        ];
    }
}
