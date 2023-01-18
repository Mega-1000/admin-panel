<?php

namespace App\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property string $symbol
 * @property int $id
 * @property string $shipping_provider
 * @property array $additional_informations
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ContainerType extends Model
{

    protected $casts = [
        'additional_informations' => 'json',
    ];

}
