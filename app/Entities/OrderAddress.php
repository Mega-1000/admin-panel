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
        'phone',
        'address',
        'flat_number',
        'city',
        'postal_code',
        'email'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}
