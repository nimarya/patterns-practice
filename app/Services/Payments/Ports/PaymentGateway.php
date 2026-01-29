<?php

namespace App\Services\Payments\Ports;

use App\Services\Payments\Domain\ValueObjects\Charge;
use App\Services\Payments\Domain\ValueObjects\ChargeResult;

interface PaymentGateway
{
    public function charge(Charge $charge): ChargeResult;
}
