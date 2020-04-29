<?php

namespace App;

use App\Entities\ProductStockLog;
use App\Entities\Report;
use App\Entities\ReportDaily;
use App\Entities\ReportProperty;
use App\Entities\Task;
use App\Entities\UserEmail;
use App\Entities\UserWork;
use App\Entities\Warehouse;
use App\Entities\Order;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

/**
 * Class User
 * @package App
 */
class User extends \TCG\Voyager\Models\User
{
    use HasApiTokens, Notifiable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'role_id',
        'firstname',
        'lastname',
        'phone',
        'phone2',
        'email',
        'password',
        'status',
        'warehouse_id',
        'rate_hour'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs()
    {
        return $this->hasMany(ProductStockLog::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userEmailData()
    {
        return $this->hasOne(UserEmail::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userWorks()
    {
        return $this->hasMany(UserWork::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function reportProperties()
    {
        return $this->belongsToMany(ReportProperty::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reportDaily()
    {
        return $this->hasMany(ReportDaily::class);
    }

    public function chats()
    {
        return $this->belongsToMany(Chat::class, 'chat_user')->withTimestamps();
    }
    
    public function orders()
    {
        return $this->hasMany(Order::class, 'employee_id');
    }

}
