<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

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
}
