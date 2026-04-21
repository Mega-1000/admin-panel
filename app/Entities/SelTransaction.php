<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SelTransaction extends Model
{
    protected $table = 'sel_tr__transaction';

    public function customer()
    {
        return $this->hasOne('App\Entities\SelCustomer','id', 'tr_CustomerId');
    }

    public function note()
    {
        return $this->hasOne('App\Entities\SelNote','ne_TransId', 'id');
    }

    public function transactionItem()
    {
        return $this->hasOne('App\Entities\SelTransactionItem','tt_TransId', 'id');
    }

    public function deliveryAddress()
    {
        return $this->hasOne('App\Entities\SelAddress','adr_PostBuy_TransId', 'id')
            ->where('adr_Type', 2)
            ->orderBy('id', 'dsc');
    }
    public function deliveryAddressBefore()
    {
        return $this->hasOne('App\Entities\SelAddress','adr_TransId', 'id')
            ->where('adr_Type', 2)
            ->orderBy('id', 'dsc');
    }

    public function invoiceAddress()
    {
        return $this->hasOne('App\Entities\SelAddress','adr_PostBuy_TransId', 'id')
            ->where('adr_Type', 3)
            ->orderBy('id', 'dsc');
    }

    public function defaultAdress()
    {
        return $this->hasOne('App\Entities\SelAddress','adr_PostBuy_TransId', 'id')
            ->where('adr_Type', 1)
            ->orderBy('id', 'dsc');
    }

    public function defaultAdressBefore()
    {
        return $this->hasOne('App\Entities\SelAddress','adr_TransId', 'id')
            ->where('adr_Type', 1)
            ->orderBy('id', 'dsc');
    }

    public function invoiceAddressBefore()
    {
        return $this->hasOne('App\Entities\SelAddress','adr_TransId', 'id')
            ->where('adr_Type', 3)
            ->orderBy('id', 'dsc');
    }

    public function order()
    {
        return $this->hasOne(Order::class, 'sello_id', 'id');
    }
	
	public function allegroOrder(): BelongsTo
	{
		return $this->belongsTo(AllegroOrder::class, 'tr_CheckoutFormId', 'order_id');
	}
}
