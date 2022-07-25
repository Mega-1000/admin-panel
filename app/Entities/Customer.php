<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Collection;
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

    protected $hidden = ['password', 'remember_token'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function standardAddress()
    {
        $standardAddress = $this->addresses()->where('type', '=', CustomerAddress::ADDRESS_TYPE_STANDARD)->first();

        return $standardAddress;
    }

    public function invoiceAddress()
    {
        $invoiceAddress = $this->addresses()->where('type', '=', CustomerAddress::ADDRESS_TYPE_INVOICE)->first();

        return $invoiceAddress;
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function surplusPayments()
    {
        return $this->hasMany(UserSurplusPayment::class, 'user_id');
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
            throw new \Exception('wrong_phone');
        }
        return \Hash::make($pass);
    }

    public function chats()
    {
        return $this->belongsToMany(Chat::class, 'chat_user')->withTimestamps();
    }

    public function getIsAllegroAttribute()
    {
        return strpos($this->login, 'allegro') !== false;
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Zwraca kolekcje posortowaną od najnowszych
     *
     * @return Collection
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    public function getOrderedTransaction()
    {
        return $this->transactions->sortBy('id', true, true)->values();
    }
}
