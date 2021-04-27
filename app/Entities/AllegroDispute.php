<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class AllegroDispute extends Model
{

    protected $fillable = [
        'dispute_id',
        'hash',
        'status',
        'subject',
        'buyer_id',
        'buyer_login',
        'form_id',
        'order_date',
    ];

    // @TODO refactor sello import to include form_id @ orders table
    // add db indexes
    public function getOrderAttribute(): string
    {
        $transaction = SelTransaction::where('tr_CheckoutFormId', '=', $this->form_id)->first();
        if ($transaction) {
            $order = Order::where('sello_id', '=', $transaction->id)->first();
        } else {
            $order = null;
        }
        if ($order) {
            return "<a href='/admin/orders/{$order->id}/edit'>" . $order->id . "</a>";
        } else {
            return "b/d";
        }
    }

}
