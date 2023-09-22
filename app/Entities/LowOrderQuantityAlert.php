<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $item_names
 * @property int $min_quantity
 * @property string $message
 * @property int $delay_time
 * @property string $title
 */
class LowOrderQuantityAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_names',
        'min_quantity',
        'message',
        'delay_time',
        'title',
    ];

    public function products(): mixed
    {
        return Product::where('low_order_quantity_alert_text', $this->item_names);
    }
}
