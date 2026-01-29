<?php

namespace App\Services\Payments\Adapters\Stripe;

use Stripe\StripeClient;

final class StripeSdkPaymentIntentCreator implements StripePaymentIntentCreator
{
    public function __construct(
        public readonly StripeClient $stripe,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @return array{id: string, status: string, client_secret: string}
     */
    public function create(array $payload, ?string $idempotencyKey = null): array
    {
        $intent = $idempotencyKey !== null
            ? $this->stripe->paymentIntents->create($payload, ['idempotency_key' => $idempotencyKey])
            : $this->stripe->paymentIntents->create($payload);

        return [
            'id' => $intent->id,
            'status' => $intent->status,
            'client_secret' => $intent->client_secret,
        ];
    }
}
