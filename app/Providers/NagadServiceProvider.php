<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\PaymentEngine\Payment;
use App\PaymentEngine\Refund;
use App\PaymentEngine\Key;

class NagadServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind("payment", function () {
            return new Payment();
        });
        $this->app->bind("refundPayment", function () {
            return new Refund();
        });
        $this->app->bind("generate", function () {
            return new Key();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

    }
}
