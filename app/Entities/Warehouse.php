<?php

namespace App\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Warehouse.
 *
 * @package namespace App\Entities;
 */
class Warehouse extends Model implements Transformable
{
    use TransformableTrait;

    public const OLAWA_WAREHOUSE_ID = 16;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firm_id',
        'symbol',
        'status',
        'radius'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function address()
    {
        return $this->hasOne(WarehouseAddress::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function property()
    {
        return $this->hasOne(WarehouseProperty::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_warehouse');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

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

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function reports()
    {
        return $this->belongsToMany(Report::class);
    }
}
