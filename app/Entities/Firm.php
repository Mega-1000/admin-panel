<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Firm.
 *
 * @package namespace App\Entities;
 */
class Firm extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'short_name',
        'symbol',
        'delivery_warehouse_id',
        'email',
        'secondary_email',
        'nip',
        'account_number',
        'phone',
        'secondary_phone',
        'notices',
        'secondary_notices',
        'status',
        'created_at',
        'firm_type',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function address()
    {
        return $this->hasOne(FirmAddress::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function deliveryWarehouse()
    {
        return $this->hasOne(Warehouse::class, 'delivery_warehouse_id');
    }

    public $customColumnsVisibilities = [
        'name',
        'short_name',
        'symbol' ,
        'email' ,
        'secondary_email' ,
        'nip' ,
        'account_number' ,
        'status' ,
        'active' ,
        'pending',
        'created_at',
        'changeStatus',
        'phone',
        'secondary_phone' ,
        'notices' ,
        'secondary_notices' ,
        'firm_type' ,
        'production' ,
        'delivery' ,
        'other' ,
    ];
}
