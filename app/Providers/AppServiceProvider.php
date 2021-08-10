<?php

namespace App\Providers;

use App\Entities\TaskTime;
use App\Observers\Entities\TaskTimeObserver;
use Carbon\Carbon;
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
        if (env('DEBUG_QUERY', false)) {
            \DB::listen(function($sql) {
                error_log($sql->sql);
            });
        }

        $forceHttpsEnvs = ['production', 'test'];

        if (in_array($this->app->environment(), $forceHttpsEnvs)) {
            \URL::forceScheme('https');
        }
        Carbon::setWeekendDays([Carbon::SUNDAY, Carbon::SATURDAY]);
//        TaskTime::observe(TaskTimeObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Schema::defaultStringLength(191);
    }
}
