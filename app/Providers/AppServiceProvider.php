<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $length = env('DB_DEFAULT_STRING_LENGTH', 0);
        if ($length > 0) {
            Schema::defaultStringLength($length);
        }
	}

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
