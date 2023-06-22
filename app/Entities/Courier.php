<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $courier_name
 * @property string $courier_key
 * @property int    $item_number
 * @property bool   $active
*/

class Courier extends Model
{
    use HasFactory;

    protected $table = 'courier';

    protected array $casts = [
        'id' => 'integer',
        'item_number' => 'integer',
        'active' => 'boolean'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected array $fillable = [
        'courier_name',
        'courier_key',
        'item_number',
        'active'
    ];
}
