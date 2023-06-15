<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateChatAuctionOfferRequest extends FormRequest
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
            'commercial_price_net' => 'required|numeric',
            'basic_price_net' => 'required|numeric',
            'calculated_price_net' => 'required|numeric',
            'aggregate_price_net' => 'required|numeric',
            'commercial_price_gross' => 'required|numeric',
            'basic_price_gross' => 'required|numeric',
            'calculated_price_gross' => 'required|numeric',
            'aggregate_price_gross' => 'required|numeric',
            'order_item_id' => 'required|integer',
            'send_notification' => 'nullable',
        ];
    }
}
