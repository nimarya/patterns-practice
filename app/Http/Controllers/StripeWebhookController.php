<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CoursePurchase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $secret = config('services.stripe.webhook_secret');

        if (! is_string($secret) || $secret === '') {
            return response('Stripe webhook secret not configured.', 500);
        }

        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature', '');

        try {
            $event = Webhook::constructEvent($payload, $signature, $secret);
        } catch (\UnexpectedValueException) {
            return response('Invalid payload.', 400);
        } catch (SignatureVerificationException) {
            return response('Invalid signature.', 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $intent = $event->data->object;
            $metadata = $intent->metadata ?? [];
            $courseId = $metadata['course_id'] ?? null;
            $userId = $metadata['user_id'] ?? null;

            DB::transaction(function () use ($courseId, $userId, $intent): void {
                CoursePurchase::updateOrCreate(
                    ['payment_intent_id' => $intent->id],
                    [
                        'user_id' => $userId,
                        'course_id' => $courseId,
                        'status' => $intent->status,
                        'amount_cents' => $intent->amount,
                        'currency' => $intent->currency,
                    ],
                );

                if (! $courseId || ! $userId) {
                    return;
                }

                $course = Course::find($courseId);
                $user = User::find($userId);

                if ($course && $user) {
                    $user->courses()->syncWithoutDetaching([$course->id]);
                }
            });
        }

        return response('Webhook handled.', 200);
    }
}
