<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class OrderAddress.
 * @property Order $order
 * @package namespace App\Entities;
 */
class OrderAddress extends Model implements Transformable
{
    use TransformableTrait;

    const TYPE_DELIVERY = 'DELIVERY_ADDRESS';
    const TYPE_INVOICE = 'INVOICE_ADDRESS';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'type',
        'firstname',
        'lastname',
        'firmname',
        'nip',
        'phone_code',
        'phone',
        'address',
        'flat_number',
        'city',
        'postal_code',
        'email',
        'country_id',
        'isAbroad'
    ];


    /**
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getPhoneFullAttribute(): string
    {
        return $this->phone_code . $this->phone;
    }

    /**
     * @return string
     */
    public function getAllegroEmailAddress(): string
    {
        $emailRaw = explode('@', $this->email, 2);
        $emailFirstPart = explode('+', $emailRaw[0], 2);

        return $emailFirstPart[0] . '@' . $emailRaw[1];
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
