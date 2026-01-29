<?php

namespace App\Services\Payments\Adapters\Stripe;

use App\Services\Payments\Domain\ValueObjects\PaymentIntentData;
use App\Services\Payments\Domain\ValueObjects\PaymentIntentResult;
use App\Services\Payments\Ports\PaymentIntentGateway;
use Stripe\Exception\ApiErrorException;

final class StripePaymentIntentGateway implements PaymentIntentGateway
{
    public function __construct(
        public readonly StripePaymentIntentCreator $paymentIntents,
    ) {}

    public function create(PaymentIntentData $paymentIntent): PaymentIntentResult
    {
        $payload = [
            'amount' => $paymentIntent->amount,
            'currency' => $paymentIntent->currency,
            'automatic_payment_methods' => ['enabled' => true],
            'metadata' => $paymentIntent->metadata,
        ];

        if ($paymentIntent->customerId !== null) {
            $payload['customer'] = $paymentIntent->customerId;
        }

        if ($paymentIntent->description !== null) {
            $payload['description'] = $paymentIntent->description;
        }

        try {
            $intent = $this->paymentIntents->create($payload, $paymentIntent->idempotencyKey);
        } catch (ApiErrorException $exception) {
            return PaymentIntentResult::failure($exception->getMessage());
        }

        return PaymentIntentResult::success(
            $intent['client_secret'],
            $intent['id'],
            $intent['status'],
        );
    }
}
