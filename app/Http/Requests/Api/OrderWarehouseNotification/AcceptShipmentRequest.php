<?php

namespace App\Http\Requests\Api\OrderWarehouseNotification;

use Illuminate\Foundation\Http\FormRequest;

class AcceptShipmentRequest extends FormRequest
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
            'order_id' => 'required|numeric',
            'warehouse_id' => 'required|numeric',
            'realization_date' => 'required|date',
            'possible_delay_days' => 'required|numeric',
            'file' => 'nullable|min:5',
            'contact_person' => 'required|min:3',
            'contact_person_phone' => 'required|min:7',
            'driver_contact' => 'nullable|min:7',
            'customer_notices' => 'nullable',
        ];
    }
}
