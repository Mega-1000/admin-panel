<?php

namespace App\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class CustomerAddress.
 *
 * @package namespace App\Entities;
 *
 * @property int $customer_id
 * @property string $type
 * @property string $firstname
 * @property string $lastname
 * @property string $firmname
 * @property string $nip
 * @property string $phone
 * @property string $address
 * @property string $flat_number
 * @property string $city
 * @property string $postal_code
 * @property string $email
 * @property int $country_id
 *
 * @property Customer $customer
 *
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class CustomerAddress extends Model implements Transformable
{
    use TransformableTrait;

    const ADDRESS_TYPE_STANDARD = 'STANDARD_ADDRESS';
    const ADDRESS_TYPE_INVOICE = 'INVOICE_ADDRESS';
    const ADDRESS_TYPE_DELIVERY = 'DELIVERY_ADDRESS';
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
