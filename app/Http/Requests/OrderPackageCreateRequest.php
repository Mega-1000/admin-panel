<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderPackageCreateRequest extends FormRequest
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
            'size_a' => 'nullable|numeric',
            'size_b' => 'nullable|numeric',
            'size_c' => 'nullable|numeric',
            'shipment_date' => 'required|date|date_format:"Y-m-d"',
            'delivery_date' => 'nullable|date|date_format:"Y-m-d"',
            'delivery_courier_name' => 'required|in:DPD,INPOST,APACZKA,JAS,POCZTEX,GIELDA,ODBIOR_OSOBISTY,ALLEGRO-INPOST',
            'service_courier_name' => 'nullable|in:DPD,INPOST,APACZKA,JAS,POCZTEX,GIELDA,ODBIOR_OSOBISTY,PACZKOMAT,ALLEGRO-INPOST',
            'weight' => 'required|regex:/^\d*(\.\d{1,2})?$/',
            'quantity' => 'nullable|numeric',
            'container_type' => 'nullable',
            'notices' => 'nullable',
            'shape' => 'nullable',
            'sending_number' => 'nullable',
            'letter_number' => 'nullable',
            'cash_on_delivery' => 'nullable',
            'status' => 'required|in:NEW',
            'cost_for_client' => 'nullable|regex:/^\d*(\.\d{2})?$/',
            'cost_for_company' => 'nullable|regex:/^\d*(\.\d{2})?$/',
            'real_cost_for_company' => 'nullable|regex:/^\d*(\.\d{2})?$/',
            'chosen_data_template' => 'nullable',
            'content' => 'required',
            'toCheck' => 'nullable',
        ];
    }
}
