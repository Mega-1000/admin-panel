<?php

namespace App\Http\Requests\Messages;

use Illuminate\Foundation\Http\FormRequest;

class CustomerComplaintRequest extends FormRequest
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
            'firstname'            => 'required|string|max:255',
            'surname'              => 'required|string|max:255',
            'phone'                => 'required|regex:/[\+0-9]{9,12}/',
            'email'                => 'required|email',
            'reason'               => 'required|string',
            'description'          => 'required|string',
            'productValue'         => 'nullable|string',
            'damagedProductsValue' => 'nullable|string',
            'accountNumber'        => 'nullable|min:10',
            'date'                 => 'required|date|date_format:Y-m-d\TH:i',
            'image'                => 'nullable|image|mimes:jpeg,png,jpg|max:4096',
            'driverPhone'          => 'nullable|regex:/[\+0-9]{9,12}/',
            'trackingNumber'       => 'nullable|string',
        ];
    }
}
