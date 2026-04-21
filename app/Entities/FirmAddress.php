<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class FirmAddress.
 *
 * @package namespace App\Entities;
 */
class FirmAddress extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firm_id', 'city', 'latitude', 'longitude', 'flat_number', 'address', 'address2', 'postal_code'
    ];

    /**
     * @return BelongsTo
     */
    public function firm(): BelongsTo
    {
        return $this->belongsTo(Firm::class);
    }
}
