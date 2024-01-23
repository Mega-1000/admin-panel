<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $order_id
 * @property float $value
 * @property string $invoice_number
 * @property string $issue_date
 * @property string $type
 */
class OrderInvoiceValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'value',
        'invoice_number',
        'issue_date',
        'type',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
