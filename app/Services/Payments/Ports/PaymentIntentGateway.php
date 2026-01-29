<?php

namespace App\Services\Payments\Ports;

use App\Services\Payments\Domain\ValueObjects\PaymentIntentData;
use App\Services\Payments\Domain\ValueObjects\PaymentIntentResult;

interface PaymentIntentGateway
{
    public function create(PaymentIntentData $paymentIntent): PaymentIntentResult;
}
