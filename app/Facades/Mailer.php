<?php
/**
 * Author: Sebastian Rogala
 * Mail: sebrogala@gmail.com
 * Created: 06.02.2019
 */

namespace App\Facades;

use App\Repositories\UserRepository;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class Mailer
{
    public static function notification(): \Illuminate\Contracts\Mail\Mailer
    {
        return Mail::mailer(
            name: 'notifications'
        );
    }

    public static function create(User $user = null): \Illuminate\Contracts\Mail\Mailer
    {
        $user = User::whereHas('userEmailData')->first();

        if (config('app.env') === 'development') {
            return Mail::mailer('dev');
        }


        return Mail::mailer(name: 'default');
    }
}
