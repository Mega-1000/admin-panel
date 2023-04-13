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

    protected $fillable = [
        'courier_name', 
        'courier_key', 
        'item_number', 
        'active'
    ];
}
