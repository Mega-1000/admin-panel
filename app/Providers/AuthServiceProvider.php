<?php

namespace App\Providers;

use App\OrderDatatableColumns;
use App\Policies\OrderDatatableColumnsPolicy;
use App\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        OrderDatatableColumns::class => OrderDatatableColumnsPolicy::class,
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

        Passport::tokensExpireIn(now()->addDays(30));
        Passport::refreshTokensExpireIn(now()->addDays(180));

    }
}
