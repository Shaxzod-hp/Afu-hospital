<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        // Per-email|IP login throttle — supplements the per-IP throttle:6,1 on
        // the route. This key locks out a specific credential across all IPs
        // (brute-forcing the same account from rotating IPs) and is independent
        // of the IP-keyed limiter, so both must pass.
        RateLimiter::for('login', function (Request $request) {
            return [
                // 5 attempts per minute keyed on the submitted login identifier + IP.
                // Blocks credential stuffing from a single IP.
                Limit::perMinute(5)->by(
                    $request->input('username', $request->input('email', 'unknown'))
                    . '|' . $request->ip()
                ),
                // 10 attempts per minute keyed on IP only.
                // Provides a wider IP-level ceiling independent of identifier.
                Limit::perMinute(10)->by($request->ip()),
            ];
        });
    }
}
