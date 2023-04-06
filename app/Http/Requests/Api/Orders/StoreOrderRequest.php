<?php

namespace App\Http\Requests\Api\Orders;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
        return [];
      /*  return array_merge([
            'id_from_front_db' => 'required|numeric',
            'rewrite' => 'nullable',
            'customer_login' => 'required|min:3',
            'customer_notices' => 'nullable|min:3',
            'cash_on_delivery_amount' => 'nullable|numeric',
            'allegro_transaction_id' => 'nullable|numeric',
            'additional_service_cost' => 'nullable|numeric',
            'shipment_price_for_client' => 'nullable|numeric',
            'shipment_price_for_us' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'total_price' => 'nullable|numeric',
            'old_id_from_front_db' => 'nullable|numeric',
            'old_prices' => 'nullable|numeric',
            'shipping_abroad' => 'nullable|boolean',
            'order_items.*.product_symbol' => 'required_with:order_items|min:3',
            'order_items.*.price' => 'required_with:order_items|numeric',
            'order_items.*.quantity' => 'required_with:order_items|numeric',
            ],
//            $this->generateAddressValidationArray('delivery_address', 'required'),
//            $this->generateAddressValidationArray('invoice_address', 'required_with:invoice_address')
            $this->generateAddressValidationArray('delivery_address', 'nullable'),
            $this->generateAddressValidationArray('invoice_address', 'nullable')
        );*/
    }

    protected function generateAddressValidationArray($name, $required)
    {
        return [
            $name . '.firstname' => $required . '',
            $name . '.lastname' => $required . '',
            $name . '.firmname' => $required . '',
            $name . '.nip' => 'nullable',
            $name . '.phone' => $required . '',
            $name . '.email' => $required . '|email',
            $name . '.city' => $required . '',
            $name . '.address' => $required . '',
            $name . '.postal_code' => $required . '',
            $name . '.flat_number' => $required . '',
        ];
    }
}
