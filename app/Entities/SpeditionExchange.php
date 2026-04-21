<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class SpeditionExchange.
 *
 * @package namespace App\Entities;
 */
class SpeditionExchange extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'chosen_spedition_offer_id',
        'hash',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chosenSpedition()
    {
        return $this->belongsTo(SpeditionExchangeOffer::class, 'chosen_spedition_offer_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function speditionOffers()
    {
        return $this->hasMany(SpeditionExchangeOffer::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(SpeditionExchangeItem::class);
    }
}
