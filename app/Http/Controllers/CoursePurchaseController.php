<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\Payments\Application\PaymentIntentService;
use App\Services\Payments\Domain\ValueObjects\PaymentIntentData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CoursePurchaseController extends Controller
{
    public function show(Course $course): Response
    {
        return Inertia::render('courses/Purchase', [
            'course' => [
                'id' => $course->id,
                'name' => $course->name,
                'description' => $course->description,
                'price_cents' => $course->price_cents,
                'currency' => $course->currency,
            ],
            'stripePublicKey' => config('services.stripe.public'),
        ]);
    }

    public function intent(Request $request, Course $course, PaymentIntentService $payments): JsonResponse
    {
        if ($request->user()->can('view', $course)) {
            return response()->json([
                'message' => 'You already have access to this course.',
            ], 409);
        }

        $paymentIntent = new PaymentIntentData(
            amount: $course->price_cents,
            currency: $course->currency,
            description: 'Course purchase: '.$course->name,
            metadata: [
                'course_id' => (string) $course->id,
                'user_id' => (string) $request->user()->id,
            ],
        );

        $result = $payments->create($paymentIntent);

        if (! $result->successful) {
            return response()->json([
                'message' => $result->message ?? 'Unable to create payment intent.',
            ], 422);
        }

        return response()->json([
            'client_secret' => $result->clientSecret,
            'payment_intent_id' => $result->paymentIntentId,
        ]);
    }
}
