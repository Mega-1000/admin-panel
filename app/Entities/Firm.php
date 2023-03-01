<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Firm.
 * @property string $email
 * @package namespace App\Entities;
 */
class Firm extends Model implements Transformable
{
    use TransformableTrait;

    public $customColumnsVisibilities = [
        'name',
        'short_name',
        'symbol',
        'email',
        'secondary_email',
        'nip',
        'account_number',
        'status',
        'active',
        'pending',
        'created_at',
        'changeStatus',
        'phone',
        'secondary_phone',
        'notices',
        'secondary_notices',
        'firm_type',
        'production',
        'delivery',
        'other',
    ];
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
     * @return HasOne
     */
    public function address()
    {
        return $this->hasOne(FirmAddress::class);
    }

    /**
     * @return HasMany
     */
    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    /**
     * @return HasMany
     */
    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * @return HasOne
     */
    public function deliveryWarehouse()
    {
        return $this->hasOne(Warehouse::class, 'delivery_warehouse_id');
    }

    /**
     * @return HasMany
     */
    public function firmSources()
    {
        return $this->hasMany(FirmSource::class);
    }
}
