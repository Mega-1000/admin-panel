<?php

namespace App\Entities;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class Customer.
 *
 * @package namespace App\Entities;
 *
 * @property int $id
 * @property int $id_from_old_db
 * @property string $login
 * @property string $password
 * @property string $nick_allegro
 * @property string $status
 *
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
     * @return HasMany
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function standardAddress(): Model
    {
        /** @var ?CustomerAddress $standardAddress */
        $standardAddress = $this->addresses()->where('type', '=', CustomerAddress::ADDRESS_TYPE_STANDARD)->first();
        if($standardAddress === null) {
            return $this->addresses()->create([
                'type' => CustomerAddress::ADDRESS_TYPE_STANDARD,
            ]);
        }
        return $standardAddress;
    }

    public function deliveryAddress(): Model
    {
        /** @var CustomerAddress $deliveryAddress */
        $deliveryAddress = $this->addresses()->where('type', '=', CustomerAddress::ADDRESS_TYPE_DELIVERY)->first();
        if($deliveryAddress === null) {
            return $this->addresses()->create([
                'type' => CustomerAddress::ADDRESS_TYPE_DELIVERY,
            ]);
        }
        return $deliveryAddress;
    }

    public function invoiceAddress(): Model
    {
        /** @var CustomerAddress $invoiceAddress */
        $invoiceAddress = $this->addresses()->where('type', '=', CustomerAddress::ADDRESS_TYPE_INVOICE)->first();
        if($invoiceAddress === null) {
            return $this->addresses()->create([
                'type' => CustomerAddress::ADDRESS_TYPE_INVOICE,
            ]);
        }
        return $invoiceAddress;
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function surplusPayments(): HasMany
    {
        return $this->hasMany(UserSurplusPayment::class, 'user_id');
    }

    public array $customColumnsVisibilities = [
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

    /**
     * @throws Exception
     */
    public function generatePassword($pass): string
    {
        $pass = preg_replace('/[^0-9]/', '', $pass);
        if (strlen($pass) < 9) {
            throw new Exception('wrong_phone');
        }

        return Hash::make($pass);
    }

    public function chats():BelongsToMany
    {
        return $this->belongsToMany(Chat::class, 'chat_user')->withTimestamps();
    }

    public function getIsAllegroAttribute():bool
    {
        return str_contains($this->login, 'allegro');
    }

    public function transactions():HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Zwraca kolekcje posortowaną od najnowszych
     */
    public function getOrderedTransaction(): Collection
    {
        return $this->transactions->sortBy('id', true, true)->values();
    }
}
