<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::macro('calculateDaysDifference', function () {
            $from_date = Carbon::parse($this->format('Y-m-d'));
            $through_date = Carbon::parse(date('Y-m-d', strtotime(now())));

            return $from_date->diffInDays($through_date);
        });
    }
}
