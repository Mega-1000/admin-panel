<?php

namespace App\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class UserWork.
 *
 * @package namespace App\Entities;
 */
class UserWork extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'users_works';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'date_of_work',
        'start',
        'end'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
