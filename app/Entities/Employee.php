<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Employee.
 *
 * @package namespace App\Entities;
 */
class Employee extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firm_id', 'warehouse_id', 'email', 'firstname', 'lastname', 'phone', 'job_position', 'comments', 'additional_comments', 'postal_code', 'status'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function firm()
    {
        return $this->belongsTo(Firm::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'employee_warehouse')->withTimestamps();
    }
    
    public function employeeRoles()
    {
        return $this->belongsToMany(EmployeeRole::class, 'employeerole_employee')->withTimestamps();
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(OrderMessage::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tasks()
    {
        return $this->belongsToMany(OrderTask::class);
    }

    public $customColumnsVisibilities = [
        'firstname',
        'lastname' ,
        'email' ,
        'status',
        'created_at' ,
        'change_status',
        'active' ,
        'pending',
        'phone' ,
        'job_position',
        'secretariat' ,
        'consultant' ,
        'storekeeper',
        'sales' ,
        'comments' ,
        'additional_comments' ,
        'postal_code',
        'radius'
    ];
}
