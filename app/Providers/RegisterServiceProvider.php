<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\NagadPaymentService;
use App\Services\Interfaces\NagadPaymentServiceInterface;

class RegisterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(NagadPaymentServiceInterface::class, NagadPaymentService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}