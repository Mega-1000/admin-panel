<?php

namespace App\Entities;

use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;


/**
 * Class Warehouse.
 *
 * @property Collection<User> $users
 * @property Firm $firm
 * @package namespace App\Entities;
 */
class Warehouse extends Model implements Transformable
{
    use TransformableTrait;

    public const OLAWA_WAREHOUSE_ID = 16;
    public $customColumnsVisibilities = [
        'symbol',
        'address',
        'warehouse_number',
        'postal_code',
        'city',
        'status',
        'active',
        'pending',
        'created_at'
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firm_id',
        'symbol',
        'status',
        'radius',
        'warehouse_email'
    ];

    /**
     * @return HasOne
     */
    public function address()
    {
        return $this->hasOne(WarehouseAddress::class);
    }

    /**
     * @return HasOne
     */
    public function property()
    {
        return $this->hasOne(WarehouseProperty::class);
    }

    /**
     * @return BelongsTo
     */
    public function firm(): BelongsTo
    {
        return $this->belongsTo(Firm::class);
    }

    /**
     * @return HasMany
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_warehouse');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'warehouse_id', 'id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function reports()
    {
        return $this->belongsToMany(Report::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
