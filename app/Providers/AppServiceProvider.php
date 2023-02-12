<?php

namespace App\Providers;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // TODO Change to configuration
        if (env('DEBUG_QUERY', false)) {
            DB::listen(function ($sql) {
                error_log($sql->sql);
            });
        }

        $forceHttpsEnvs = ['production', 'test'];

        if (in_array($this->app->environment(), $forceHttpsEnvs)) {
            URL::forceScheme('https');
        }
        // TODO Check solutions for deprecated functions
        Carbon::setWeekendDays([CarbonInterface::SUNDAY, CarbonInterface::SATURDAY]);
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
