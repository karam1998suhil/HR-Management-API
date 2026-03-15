<?php

namespace App\Providers;

use App\Events\SalaryChanged;
use App\Listeners\SendSalaryChangedNotifications;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // salary change event listener
        Event::listen(
            SalaryChanged::class,
            SendSalaryChangedNotifications::class,
        );

        // 10 requests per minute per user or IP
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(10)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function () {
                    return response()->json([
                        'message' => 'Too many requests. Please slow down.',
                    ], 429);
                });
        });
    }
}