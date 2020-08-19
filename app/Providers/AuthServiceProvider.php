<?php

namespace App\Providers;

use App\Http\Middleware\Cors;
use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Gate::define('create-bonus', function ($user) {
            return in_array($user->role_id, [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN]);
        });

        Route::group(['middleware' => Cors::class], function () {
            Passport::routes();
        });
        Passport::tokensExpireIn(now()->addDays(30));
        Passport::refreshTokensExpireIn(now()->addDays(180));

    }
}
