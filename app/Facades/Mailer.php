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
use Illuminate\Support\Facades\Log;

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

    public function create(User $user = null)
    {
        if (empty($user)) {
            $user = Auth::user();
        }

        if (empty($user)) {
            $user = $this->userRepository->findWhere(['name' => '001'])->first();
        }

        $transport = (new \Swift_SmtpTransport($user->userEmailData->host, $user->userEmailData->port, $user->userEmailData->encryption))
            ->setEncryption($user->userEmailData->encryption)
            ->setUsername($user->userEmailData->username)
            ->setPassword($user->userEmailData->password);

        $mailer = app(\Illuminate\Mail\Mailer::class);
        $mailer->setSwiftMailer(new \Swift_Mailer($transport));
        $mailer->alwaysFrom($user->userEmailData->username);

        return $mailer;
    }
}
