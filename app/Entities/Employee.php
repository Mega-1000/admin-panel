<?php

namespace App\Entities;

use App\Entities\interfaces\iChatNickname;
use App\Helpers\ChatHelper;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Employee.
 *
 * @package namespace App\Entities;
 */
class Employee extends Model implements Transformable, iChatNickname
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


    public function chats()
    {
        return $this->belongsToMany(Chat::class, 'chat_user')->withTimestamps();
    }

    public function getUserNicknameForChat($userType)
    {
        return '<th class="alert-info alert">' . ChatHelper::formatChatUser($this, $userType) . '</th>';
    }

}
