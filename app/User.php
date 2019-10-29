<?php

namespace App;

use App\Entities\ProductStockLog;
use App\Entities\Report;
use App\Entities\Task;
use App\Entities\UserEmail;
use App\Entities\UserWork;
use App\Entities\Warehouse;
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
        'warehouse_id'
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

    public function userEmailData()
    {
        return $this->hasOne(UserEmail::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function userWorks()
    {
        return $this->hasMany(UserWork::class);
    }

    public function reports()
    {
        return $this->belongsToMany(Report::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
