<?php

namespace App\Services\Payments\Domain\ValueObjects;

final class Charge
{
    /**
     * @param  array<string, string>  $metadata
     */
    public function __construct(
        public readonly int $amount,
        public readonly string $currency,
        public readonly string $paymentMethodId,
        public readonly ?string $customerId = null,
        public readonly ?string $description = null,
        public readonly array $metadata = [],
        public readonly ?string $idempotencyKey = null,
    ) {
        if ($this->amount <= 0) {
            throw new \InvalidArgumentException('Amount must be greater than zero.');
        }

        if (strlen($this->currency) !== 3) {
            throw new \InvalidArgumentException('Currency must be a 3-letter ISO code.');
        }
    }
}
