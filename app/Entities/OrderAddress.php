<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class OrderAddress.
 *
 * @package namespace App\Entities;
 */
class OrderAddress extends Model implements Transformable
{
    use TransformableTrait;

    const TYPE_GENERAL = 'GENERAL';
    const TYPE_STANDARD = 'STANDARD_ADDRESS';
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getPhoneFullAttribute()
    {
        return $this->phone_code . $this->phone;
    }

    /**
     * @return mixed|string
     */
    public function getAllegroEmailAddress(): string
    {
        $emailRaw = explode('@', $this->email, 2);
        $emailFirstPart = explode('+', $emailRaw[0], 2);
        return $emailFirstPart[0] . '@' . $emailRaw[1];
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
