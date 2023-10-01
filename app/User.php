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
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * Class User
 * @package App
 *
 * @property int $id
 *
 */
class User extends \TCG\Voyager\Models\User
{
    use HasApiTokens, Notifiable;

    const ROLE_SUPER_ADMIN = 1;
    const ROLE_ADMIN = 2;
    const ROLE_ACCOUNTANT = 3;
    const ROLE_CONSULTANT = 4;
    const ROLE_STOREKEEPER = 5;
    const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_SUPER_ADMIN,
        self::ROLE_ACCOUNTANT,
        self::ROLE_CONSULTANT,
        self::ROLE_STOREKEEPER
    ];
    const OLAWA_USER_ID = 37;
    const CONTACT_PHONE = 691801594;
    const ORDER_DELETE_USER = 12;
    const MAGAZYN_OLAWA_ID = 37;

    const ID = 'id';
    const NAME = 'name';
    const ROLE_ID = 'role_id';
    const FIRSTNAME = 'firstname';
    const LASTNAME = 'lastname';
    const PHONE = 'phone';
    const PHONE2 = 'phone2';
    const EMAIL = 'email';
    const PASSWORD = 'password';
    const STATUS = 'status';
    const WAREHOUSE_ID = 'warehouse_id';
    const RATE_HOUR = 'rate_hour';
    const REMEMBER_TOKEN = 'remember_token';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        self::ID,
        self::NAME,
        self::ROLE_ID,
        self::FIRSTNAME,
        self::LASTNAME,
        self::PHONE,
        self::PHONE2,
        self::EMAIL,
        self::PASSWORD,
        self::STATUS,
        self::WAREHOUSE_ID,
        self::RATE_HOUR
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        self::PASSWORD,
        self::REMEMBER_TOKEN,
    ];

    /**
     * @return HasMany
     */
    public function logs()
    {
        return $this->hasMany(ProductStockLog::class);
    }

    /**
     * @return HasOne
     */
    public function userEmailData(): HasOne
    {
        return $this->hasOne(UserEmail::class);
    }

    /**
     * @return HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * @return HasMany
     */
    public function userWorks(): HasMany
    {
        return $this->hasMany(UserWork::class);
    }

    /**
     * @return HasMany
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    /**
     * @return BelongsTo
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * @return BelongsToMany
     */
    public function reportProperties()
    {
        return $this->belongsToMany(ReportProperty::class);
    }

    /**
     * @return HasMany
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
