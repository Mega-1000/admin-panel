<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
class OrderDatatableColumns extends Model
{
    use HasFactory;

    public $fillable = [
        'order',
        'hidden',
        'size',
        'user_id',
        'label',
    ];
}
