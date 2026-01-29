<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\CoursePurchaseController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::resources([
    'courses' => CourseController::class,
    'lessons' => LessonController::class,
]);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('courses/{course}/purchase', [CoursePurchaseController::class, 'show'])->name('courses.purchase.show');
    Route::post('courses/{course}/payment-intent', [CoursePurchaseController::class, 'intent'])->name('courses.purchase.intent');
    Route::get('my-courses', [CourseController::class, 'myCourses'])->name('courses.mine');
});

Route::middleware('permission:create course')->group(function () {
    Route::get('courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('courses', [CourseController::class, 'store'])->name('courses.store');
});

Route::post('stripe/webhook', StripeWebhookController::class)->name('stripe.webhook');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
