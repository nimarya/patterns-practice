<?php

namespace Tests\Feature\Payments;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_grants_course_access_on_payment_success(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();

        config(['services.stripe.webhook_secret' => 'whsec_test']);

        $payload = json_encode([
            'id' => 'evt_123',
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'id' => 'pi_123',
                    'status' => 'succeeded',
                    'amount' => 1500,
                    'currency' => 'usd',
                    'metadata' => [
                        'course_id' => (string) $course->id,
                        'user_id' => (string) $user->id,
                    ],
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $signature = $this->makeStripeSignature($payload, 'whsec_test');

        $response = $this->call(
            'POST',
            route('stripe.webhook'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_STRIPE_SIGNATURE' => $signature,
            ],
            $payload,
        );

        $response->assertOk();
        $this->assertDatabaseHas('course_user', [
            'course_id' => $course->id,
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('course_purchases', [
            'payment_intent_id' => 'pi_123',
            'status' => 'succeeded',
            'amount_cents' => 1500,
            'currency' => 'usd',
            'course_id' => $course->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_webhook_is_idempotent_for_same_payment_intent(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();

        config(['services.stripe.webhook_secret' => 'whsec_test']);

        $payload = json_encode([
            'id' => 'evt_123',
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'id' => 'pi_123',
                    'status' => 'succeeded',
                    'amount' => 1500,
                    'currency' => 'usd',
                    'metadata' => [
                        'course_id' => (string) $course->id,
                        'user_id' => (string) $user->id,
                    ],
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $signature = $this->makeStripeSignature($payload, 'whsec_test');

        $this->call(
            'POST',
            route('stripe.webhook'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_STRIPE_SIGNATURE' => $signature,
            ],
            $payload,
        );

        $this->call(
            'POST',
            route('stripe.webhook'),
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_STRIPE_SIGNATURE' => $signature,
            ],
            $payload,
        );

        $this->assertDatabaseCount('course_purchases', 1);
    }

    private function makeStripeSignature(string $payload, string $secret): string
    {
        $timestamp = time();
        $signedPayload = $timestamp.'.'.$payload;
        $signature = hash_hmac('sha256', $signedPayload, $secret);

        return 't='.$timestamp.',v1='.$signature;
    }
}
