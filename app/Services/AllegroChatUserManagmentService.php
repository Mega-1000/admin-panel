<?php

namespace App\Services;

use App\Entities\Customer;
use App\User;
use Illuminate\Support\Facades\Hash;

class AllegroChatUserManagmentService
{

    public static function createOrFindUserFromAllegro(string $login): Customer
    {
        return Customer::firstOrCreate(
            ['nick_allegro' => $login],
            [
                'login' => $login . '@mega1000-from-allegro.pl',
                'password' => Hash::make('123456'),
                'status' => 'active',
            ]
        );

    }
}
