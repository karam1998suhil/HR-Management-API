<?php

namespace App\Providers;

use App\Events\SalaryChanged;
use App\Listeners\SendSalaryChangedNotifications;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register salary change event → listener
        Event::listen(
            SalaryChanged::class,
            SendSalaryChangedNotifications::class,
        );
    }
}