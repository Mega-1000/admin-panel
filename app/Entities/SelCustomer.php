<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class SelCustomer extends Model
{
    protected $table = 'sel_cs__customer';

    public function email()
    {
        return $this->hasOne('App\Entities\SelCustomerEmail', 'ce_CustomerId', 'id');
    }
    public function phone()
    {
        return $this->hasOne('App\Entities\SelCustomerPhone', 'cp_CustomerId', 'id');
    }
}
