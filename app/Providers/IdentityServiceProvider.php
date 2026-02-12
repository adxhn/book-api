<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class IdentityServiceProvider extends ServiceProvider
{
    public const THROTTLE_KEY = 'throttle:auth-attempts';
    public const THROTTLE_LIMIT = 5;

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        RateLimiter::for(static::THROTTLE_KEY, function (Request $request) {
            return Limit::perMinute(static::THROTTLE_LIMIT)->by($request->ip());
        });
    }
}
