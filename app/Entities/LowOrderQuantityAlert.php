<?php

namespace App\Entities;

use FontLib\TrueType\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $item_names
 * @property int $min_quantity
 * @property string $message
 * @property int $delay_time
 * @property string $title
 * @property string $space
 * @property string $column_name
 * @property string $php_code
 * @property Collection $messages
 */
class LowOrderQuantityAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_names',
        'min_quantity',
        'delay_time',
        'title',
        'space',
        'php_code',
        'column_name',
    ];

    public function products(): mixed
    {
        return Product::where('low_order_quantity_alert_text', $this->item_names);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(LowOrderQuantityAlertMessage::class);
    }

    public static function findForEdition(int $id): self
    {
        $result = self::where('id', $id)->first();
        $result->message = str_replace('<br />', "\n", $result->message);

        return $result;
    }
}
