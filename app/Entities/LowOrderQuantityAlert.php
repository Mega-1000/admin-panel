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
 * @property string $space
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
        'space',
    ];

    public function products(): mixed
    {
        return Product::where('low_order_quantity_alert_text', $this->item_names);
    }

    public static function findForEdition(int $id): self
    {
        $result = self::where('id', $id)->first();
        $result->message = str_replace('<br />', "\n", $result->message);

        return $result;
    }
}
