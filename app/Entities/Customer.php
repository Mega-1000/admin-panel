<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Customer.
 *
 * @package namespace App\Entities;
 */
class Customer extends Model implements Transformable
{
    use TransformableTrait;

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
}
