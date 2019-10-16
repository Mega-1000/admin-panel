<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class SpeditionExchangeOffer.
 *
 * @package namespace App\Entities;
 */
class SpeditionExchangeOffer extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'spedition_exchange_id',
        'firm_name',
        'street',
        'number',
        'postal_code',
        'city',
        'nip',
        'account_number',
        'phone_number',
        'contact_person',
        'email',
        'comments',
        'driver_first_name',
        'driver_last_name',
        'driver_phone_number',
        'driver_document_number',
        'driver_car_registration_number',
        'driver_arrival_date',
        'driver_approx_arrival_time',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function speditionExchange()
    {
        return $this->belongsTo(SpeditionExchange::class);
    }
}
