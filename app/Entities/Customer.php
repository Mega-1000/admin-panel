<?php

namespace App\Entities;

use Exception;
use Illuminate\Notifications\Notifiable;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class Customer.
 *
 * @package namespace App\Entities;
 */
class Customer extends Authenticatable implements Transformable
{
    use TransformableTrait, HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_from_old_db', 'login', 'password', 'nick_allegro', 'status',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function standardAddress()
    {
        $standardAddress = $this->addresses()->where('type', '=', 'STANDARD_ADDRESS')->first();

        return $standardAddress;
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public $customColumnsVisibilities = [
        "customer_adresses.firstname",
        "lastname",
        "email",
        "status",
        "created_at",
        "login",
        "nick_allegro",
        "firmname",
        "nip",
        "phone",
        "address",
        "flat_number",
        "city",
        "postal_code",
    ];

    public function findForPassport($username)
    {
        return $this->where('login', $username)->first();
    }

    public function generatePassword($pass)
    {
        $pass = preg_replace('/[^0-9]/', '', $pass);
        if (strlen($pass) < 9) {
            throw new Exception('wrong_phone');
        }
        return $pass;
    }
}
