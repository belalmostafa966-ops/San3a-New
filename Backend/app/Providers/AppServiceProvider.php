<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\JobCompletedEvent;
use App\Listeners\UpdateVerificationTierAfterJob;

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
        Event::listen(
            JobCompletedEvent::class,
            UpdateVerificationTierAfterJob::class,
        );
    }
}