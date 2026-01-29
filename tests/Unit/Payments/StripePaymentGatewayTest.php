<?php

namespace Tests\Unit\Payments;

use App\Services\Payments\Adapters\Stripe\StripePaymentGateway;
use App\Services\Payments\Adapters\Stripe\StripePaymentIntentCreator;
use App\Services\Payments\Domain\ValueObjects\Charge;
use Tests\TestCase;

class StripePaymentGatewayTest extends TestCase
{
    public function test_charge_returns_success_when_intent_succeeds(): void
    {
        $gateway = new StripePaymentGateway(
            new FakeStripePaymentIntentCreator(['id' => 'pi_999', 'status' => 'succeeded', 'client_secret' => 'cs_123']),
        );

        $result = $gateway->charge($this->makeCharge());

        $this->assertTrue($result->successful);
        $this->assertSame('pi_999', $result->transactionId);
        $this->assertSame('succeeded', $result->status);
    }

    public function test_charge_returns_failure_when_intent_not_succeeded(): void
    {
        $gateway = new StripePaymentGateway(
            new FakeStripePaymentIntentCreator(['id' => 'pi_888', 'status' => 'requires_action', 'client_secret' => 'cs_456']),
        );

        $result = $gateway->charge($this->makeCharge());

        $this->assertFalse($result->successful);
        $this->assertSame('requires_action', $result->status);
        $this->assertSame('Payment was not completed.', $result->message);
    }

    private function makeCharge(): Charge
    {
        return new Charge(
            amount: 1200,
            currency: 'usd',
            paymentMethodId: 'pm_test',
        );
    }
}

final class FakeStripePaymentIntentCreator implements StripePaymentIntentCreator
{
    /**
     * @param  array{id: string, status: string, client_secret: string}  $result
     */
    public function __construct(
        public readonly array $result,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @return array{id: string, status: string, client_secret: string}
     */
    public function create(array $payload, ?string $idempotencyKey = null): array
    {
        return $this->result;
    }
}
