<?php

namespace App\Services\Payments\Adapters\Stripe;

interface StripePaymentIntentCreator
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array{id: string, status: string, client_secret: string}
     */
    public function create(array $payload, ?string $idempotencyKey = null): array;
}
