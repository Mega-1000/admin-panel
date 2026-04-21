<?php

use Illuminate\Database\Seeder;

class OauthClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Laravel\Passport\Client::create([
            'user_id' => 1,
            'name' => 'sadmin',
            'secret' => 'OSkQ1AV9o4fD6Ms7pXeO0Uj1VWXEbEGJZa4QnDFT',
            'redirect' => env("APP_URL") . '/auth/callback',
            'personal_access_client' => 0,
            'password_client' => 0,
            'revoked' => 0,
        ]);

        \Laravel\Passport\Client::create([
            'user_id' => 2,
            'name' => 'admin',
            'secret' => 'Cgm7yxvRbzR8ZEFXWBt1uTt5ySnLJfB31y6MZQjg',
            'redirect' => env("APP_URL") . '/auth/callback',
            'personal_access_client' => 0,
            'password_client' => 0,
            'revoked' => 0,
        ]);
    }
}
