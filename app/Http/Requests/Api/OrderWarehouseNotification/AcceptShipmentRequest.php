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
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'order_id' => 'Numer oferty',
            'warehouse_id' => 'Numer magazynu',
            'realization_date_from' => 'Data realizacji od',
            'realization_date_to' => 'Data realizacji do',
            'file' => 'Plik',
            'contact_person' => 'Osoba kontaktowa',
            'contact_person_phone' => 'Numer telefonu do osoby kontaktowej',
            'driver_contact' => 'Numer do kierowcy',
            'customer_notices' => 'Uwagi klienta',
        ];
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
            'realization_date_from' => 'required|date',
            'realization_date_to' => 'required|date',
            'file' => 'nullable|min:5',
            'contact_person' => 'required|min:3',
            'contact_person_phone' => 'required|min:7|max:12|numeric',
            'driver_contact' => 'nullable|min:7|max:12|numeric',
            'customer_notices' => 'nullable',
        ];
    }
}
