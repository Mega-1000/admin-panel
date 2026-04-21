<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $preliminary_buying_document_number
 * @property string $buying_document_number
 * @property float $gross_value
 * @property string $invoice_date
 * @property int $order_id
 * @property Order $order
 */
class OrderInvoiceDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'preliminary_buying_document_number',
        'buying_document_number',
        'gross_value',
        'invoice_date',
        'order_id',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
