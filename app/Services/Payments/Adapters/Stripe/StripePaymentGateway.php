<?php

namespace App\Services\Payments\Adapters\Stripe;

use App\Services\Payments\Domain\ValueObjects\Charge;
use App\Services\Payments\Domain\ValueObjects\ChargeResult;
use App\Services\Payments\Ports\PaymentGateway;
use Stripe\Exception\ApiErrorException;

final class StripePaymentGateway implements PaymentGateway
{
    public function __construct(
        public readonly StripePaymentIntentCreator $paymentIntents,
    ) {}

    public function charge(Charge $charge): ChargeResult
    {
        $payload = [
            'amount' => $charge->amount,
            'currency' => $charge->currency,
            'payment_method' => $charge->paymentMethodId,
            'confirm' => true,
        ];

        if ($charge->customerId !== null) {
            $payload['customer'] = $charge->customerId;
        }

        if ($charge->description !== null) {
            $payload['description'] = $charge->description;
        }

        if ($charge->metadata !== []) {
            $payload['metadata'] = $charge->metadata;
        }

        try {
            $intent = $this->paymentIntents->create($payload, $charge->idempotencyKey);
        } catch (ApiErrorException $exception) {
            return ChargeResult::failure(null, $exception->getMessage());
        }

        if ($intent['status'] === 'succeeded') {
            return ChargeResult::success($intent['id'], $intent['status']);
        }

        return ChargeResult::failure($intent['status'], 'Payment was not completed.');
    }
}
