<?php

namespace App\Services\Payments\Domain\ValueObjects;

final class PaymentIntentResult
{
    public function __construct(
        public readonly bool $successful,
        public readonly ?string $clientSecret = null,
        public readonly ?string $paymentIntentId = null,
        public readonly ?string $status = null,
        public readonly ?string $message = null,
    ) {}

    public static function success(string $clientSecret, string $paymentIntentId, string $status): self
    {
        return new self(true, $clientSecret, $paymentIntentId, $status);
    }

    public static function failure(string $message): self
    {
        return new self(false, null, null, null, $message);
    }
}
