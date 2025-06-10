<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

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
        // Set timezone for the application
        $timezone = config('app.timezone');
        date_default_timezone_set($timezone);

        // For consistency across all date operations
        if (function_exists('ini_set')) {
            ini_set('date.timezone', $timezone);
        }

        // Set Carbon timezone and locale
        Carbon::setLocale('id'); // Indonesian locale
        // Carbon will use the application timezone set by date_default_timezone_set() above
    }
}
