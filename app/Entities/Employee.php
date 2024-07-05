<?php

namespace App\Entities;

use App\Helpers\ChatHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'firm_id', 'warehouse_id', 'email', 'firstname', 'lastname', 'phone', 'job_position', 'comments', 'additional_comments', 'postal_code', 'status', 'zip_code_2', 'zip_code_3', 'zip_code_4', 'zip_code_5'
    ];

    public array $customColumnsVisibilities = [
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

    /**
     * @return BelongsTo
     */
    public function firm(): BelongsTo
    {
        return $this->belongsTo(Firm::class);
    }

    /**
     * @return BelongsToMany
     */
    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class, 'employee_warehouse')->withTimestamps();
    }

    public function employeeRoles(): BelongsToMany
    {
        return $this->belongsToMany(EmployeeRole::class, 'employeerole_employee')->withTimestamps();
    }
    /**
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(OrderMessage::class);
    }

    /**
     * @return BelongsToMany
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(OrderTask::class);
    }


    public function chats(): BelongsToMany
    {
        return $this->belongsToMany(Chat::class, 'chat_user')->withTimestamps();
    }

}
