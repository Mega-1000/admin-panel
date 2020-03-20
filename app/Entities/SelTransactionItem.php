<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class SelTransactionItem extends Model
{
    protected $table = 'sel_tr_item';

    public function itemExist()
    {
        return isset($this->tt_ItemId);
    }
    public function item()
    {
        return $this->hasOne('App\Entities\SelItem','id', 'tt_ItemId');
    }
}
