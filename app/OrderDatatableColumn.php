<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @class OrderDatatableColumns
 * Class OrderDatatableColumns is guarded by policy \App\Policies\OrderDatatableColumnsPolicy
 *
 * @property int $order
 * @property bool $hidden
 * @property string $size
 * @property int $user_id
 * @property User $user
 */
class OrderDatatableColumn extends Model
{
    use HasFactory;

    public $fillable = [
        'order',
        'hidden',
        'size',
        'user_id',
        'label',
        'filter',
        'resetFilters',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
