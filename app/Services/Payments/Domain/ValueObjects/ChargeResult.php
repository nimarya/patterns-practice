<?php

namespace App\Services\Payments\Domain\ValueObjects;

final class ChargeResult
{
    public function __construct(
        public readonly bool $successful,
        public readonly ?string $transactionId = null,
        public readonly ?string $status = null,
        public readonly ?string $message = null,
    ) {}

    public static function success(string $transactionId, string $status): self
    {
        return new self(true, $transactionId, $status);
    }

    public static function failure(?string $status, string $message): self
    {
        return new self(false, null, $status, $message);
    }
}
