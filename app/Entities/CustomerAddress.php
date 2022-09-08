<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class CustomerAddress.
 *
 * @package namespace App\Entities;
 */
class CustomerAddress extends Model implements Transformable
{
    use TransformableTrait;

    const ADDRESS_TYPE_STANDARD = 'STANDARD_ADDRESS';
    const ADDRESS_TYPE_INVOICE = 'INVOICE_ADDRESS';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
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
        'email',
        'country_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

}
