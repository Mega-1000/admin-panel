<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class SubiektInvoices extends Model
{
    public $table ="gt_invoices";
    protected $visible = ['id', 'gt_invoice_number'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
