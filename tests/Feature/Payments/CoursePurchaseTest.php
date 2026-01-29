<?php

namespace Tests\Feature\Payments;

use App\Models\Course;
use App\Models\User;
use App\Services\Payments\Domain\ValueObjects\PaymentIntentData;
use App\Services\Payments\Domain\ValueObjects\PaymentIntentResult;
use App\Services\Payments\Ports\PaymentIntentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoursePurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_intent_is_created_for_course_purchase(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['price_cents' => 1500, 'currency' => 'usd']);

        $this->app->instance(PaymentIntentGateway::class, new FakePaymentIntentGateway(
            PaymentIntentResult::success('cs_test', 'pi_123', 'requires_payment_method'),
        ));

        $response = $this->actingAs($user)->post(route('courses.purchase.intent', $course));

        $response->assertOk();
        $response->assertJson([
            'client_secret' => 'cs_test',
            'payment_intent_id' => 'pi_123',
        ]);

        $this->assertDatabaseMissing('course_user', [
            'course_id' => $course->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_payment_intent_is_blocked_when_user_already_has_access(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();

        $user->courses()->attach($course->id);

        $response = $this->actingAs($user)->post(route('courses.purchase.intent', $course));

        $response->assertStatus(409);
        $response->assertJson([
            'message' => 'You already have access to this course.',
        ]);
    }

    public function test_payment_intent_failure_returns_error(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();

        $this->app->instance(PaymentIntentGateway::class, new FakePaymentIntentGateway(
            PaymentIntentResult::failure('Unable to create payment intent.'),
        ));

        $response = $this->actingAs($user)->post(route('courses.purchase.intent', $course));

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Unable to create payment intent.',
        ]);
    }
}

final class FakePaymentIntentGateway implements PaymentIntentGateway
{
    public function __construct(
        public readonly PaymentIntentResult $result,
    ) {}

    public function create(PaymentIntentData $paymentIntent): PaymentIntentResult
    {
        return $this->result;
    }
}
