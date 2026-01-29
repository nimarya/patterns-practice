<?php

namespace App\Services\Payments\Application;

use App\Services\Payments\Domain\ValueObjects\Charge;
use App\Services\Payments\Domain\ValueObjects\ChargeResult;
use App\Services\Payments\Ports\PaymentGateway;

final class PaymentService
{
    public function __construct(
        public readonly PaymentGateway $gateway,
    ) {}

    public function charge(Charge $charge): ChargeResult
    {
        return $this->gateway->charge($charge);
    }
}
