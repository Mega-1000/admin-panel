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
        if (empty($user)) {
            $user = Auth::user();
        }
        $userRepository = app(UserRepository::class);
        if (empty($user)) {
            $user = $userRepository->findWhere(['name' => '001'])->first();
        }

        if (config('app.env') === 'development') {
            return Mail::mailer('dev');
        }

        Config::set([
            'mail.mailers.default' => [
                'transport' => 'smtp',
                'host' => $user->userEmailData->host,
                'port' => $user->userEmailData->port,
                'username' => $user->userEmailData->username,
                'password' => $user->userEmailData->password,
                'encryption' => $user->userEmailData->encryption,
                'from' => ['address' => $user->userEMailData->username, 'name' => $user->userEMailData->username],
            ]
        ]);

        return Mail::mailer(name: 'default');
    }
}
