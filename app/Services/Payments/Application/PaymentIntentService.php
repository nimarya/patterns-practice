<?php

namespace App\Services\Payments\Application;

use App\Services\Payments\Domain\ValueObjects\PaymentIntentData;
use App\Services\Payments\Domain\ValueObjects\PaymentIntentResult;
use App\Services\Payments\Ports\PaymentIntentGateway;

final class PaymentIntentService
{
    public function __construct(
        public readonly PaymentIntentGateway $gateway,
    ) {}

    public function create(PaymentIntentData $paymentIntent): PaymentIntentResult
    {
        return $this->gateway->create($paymentIntent);
    }
}
