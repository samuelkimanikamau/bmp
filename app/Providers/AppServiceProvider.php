<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Invitee;
use App\Observers\InviteeObserver;

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
        Invitee::observe(InviteeObserver::class);
    }
}
