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
use Illuminate\Support\Facades\Mail;

class Mailer
{
    /** @var UserRepository */
    protected $userRepository;

    /**
     * Mailer constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public static function notification(): \Illuminate\Contracts\Mail\Mailer
    {
        return Mail::mailer(
            name: 'notifications'
        );
    }

    public function create(User $user = null): \Illuminate\Contracts\Mail\Mailer
    {
        if (empty($user)) {
            $user = Auth::user();
        }

        if (empty($user)) {
            $user = $this->userRepository->findWhere(['name' => '001'])->first();
        }

        if (env('APP_ENV') === 'development') {
            return Mail::mailer('dev');
        }

        config([
            'mail.mailers.default' => [
                'transport' => 'smtp',
                'host' => $user->userEmailData->host,
                'port' => $user->userEmailData->port,
                'username' => $user->userEmailData->username,
                'password' => $user->userEmailData->password,
                'encryption' => $user->userEmailData->encryption
            ]
        ]);

        return Mail::mailer('default');
    }
}
