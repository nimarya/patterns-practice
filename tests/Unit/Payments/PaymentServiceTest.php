<?php

namespace Tests\Unit\Payments;

use App\Services\Payments\Application\PaymentService;
use App\Services\Payments\Domain\ValueObjects\Charge;
use App\Services\Payments\Domain\ValueObjects\ChargeResult;
use App\Services\Payments\Ports\PaymentGateway;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    public function test_charge_returns_success_result_from_gateway(): void
    {
        $gateway = new FakePaymentGateway(ChargeResult::success('pi_123', 'succeeded'));
        $service = new PaymentService($gateway);

        $result = $service->charge($this->makeCharge());

        $this->assertTrue($result->successful);
        $this->assertSame('pi_123', $result->transactionId);
        $this->assertSame('succeeded', $result->status);
    }

    public function test_charge_returns_failure_result_from_gateway(): void
    {
        $gateway = new FakePaymentGateway(ChargeResult::failure('requires_action', 'Payment was not completed.'));
        $service = new PaymentService($gateway);

        $result = $service->charge($this->makeCharge());

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

final class FakePaymentGateway implements PaymentGateway
{
    public function __construct(
        public readonly ChargeResult $result,
    ) {}

    public function charge(Charge $charge): ChargeResult
    {
        return $this->result;
    }
}
