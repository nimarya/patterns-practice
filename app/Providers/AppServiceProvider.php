<?php

namespace App\Providers;

use App\Services\Payments\Adapters\Stripe\StripePaymentGateway;
use App\Services\Payments\Adapters\Stripe\StripePaymentIntentCreator;
use App\Services\Payments\Adapters\Stripe\StripePaymentIntentGateway;
use App\Services\Payments\Adapters\Stripe\StripeSdkPaymentIntentCreator;
use App\Services\Payments\Ports\PaymentGateway;
use App\Services\Payments\Ports\PaymentIntentGateway;
use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(StripeClient::class, function (): StripeClient {
            $secret = config('services.stripe.secret');

            if (! is_string($secret) || $secret === '') {
                throw new \InvalidArgumentException('Stripe secret key is not configured.');
            }

            return new StripeClient($secret);
        });

        $this->app->bind(StripePaymentIntentCreator::class, StripeSdkPaymentIntentCreator::class);
        $this->app->bind(PaymentIntentGateway::class, StripePaymentIntentGateway::class);
        $this->app->bind(PaymentGateway::class, StripePaymentGateway::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
