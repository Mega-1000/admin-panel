<?php

namespace App\Http\Requests\Api\Orders;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderDeliveryAndInvoiceAddressesRequest extends FormRequest
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
            'customer_shipment_date_from' => 'required',
            'customer_shipment_date_to' => 'required',
            'customer_delivery_date_from' => 'required',
            'customer_delivery_date_to' => 'required',
            'delivery_description' => 'nullable',
            'remember_delivery_address' => 'required',
            'remember_invoice_address' => 'required',
            'DELIVERY_ADDRESS.firstname' => 'required',
            'DELIVERY_ADDRESS.lastname' => 'required',
            'DELIVERY_ADDRESS.phone' => 'required',
            'DELIVERY_ADDRESS.email' => 'nullable',
            'DELIVERY_ADDRESS.city' => 'required',
            'DELIVERY_ADDRESS.address' => 'required',
            'DELIVERY_ADDRESS.postal_code' => 'required',
            'DELIVERY_ADDRESS.flat_number' => 'required',
            'INVOICE_ADDRESS.firstname' => 'nullable',
            'INVOICE_ADDRESS.lastname' => 'nullable',
            'INVOICE_ADDRESS.firmname' => 'nullable',
            'INVOICE_ADDRESS.nip' => 'nullable',
            'INVOICE_ADDRESS.phone' => 'nullable',
            'INVOICE_ADDRESS.email' => 'nullable',
            'INVOICE_ADDRESS.city' => 'nullable',
            'INVOICE_ADDRESS.address' => 'nullable',
            'INVOICE_ADDRESS.postal_code' => 'nullable',
            'INVOICE_ADDRESS.flat_number' => 'nullable',
        ];
    }

//    public function getDates()
//    {
//        return [
//            'customer_shipment_date_from' => $this->request->get('shipment_date_from'),
//            'customer_shipment_date_to' => $this->request->get('shipment_date_to'),
//            'customer_delivery_date_from' => $this->request->get('delivery_date_from'),
//            'customer_delivery_date_to' => $this->request->get('delivery_date_to'),
//        ];
//    }
}
