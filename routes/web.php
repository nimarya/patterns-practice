<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
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

Route::middleware('permission:courses.create')->group(function () {
    Route::get('courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('courses', [CourseController::class, 'store'])->name('courses.store');
});

Route::post('stripe/webhook', StripeWebhookController::class)->name('stripe.webhook');

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::middleware('permission:settings.users')->group(function () {
            Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
            Route::post('users', [AdminUserController::class, 'store'])->name('users.store');
            Route::put('users/{user}', [AdminUserController::class, 'update'])->name('users.update');
            Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
        });

        Route::middleware('permission:settings.permissions')->group(function () {
            Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
            Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
            Route::put('permissions/{permission}', [PermissionController::class, 'updatePermission'])->name('permissions.update');
            Route::delete('permissions/{permission}', [PermissionController::class, 'destroyPermission'])->name('permissions.destroy');
            Route::post('roles', [PermissionController::class, 'storeRole'])->name('roles.store');
            Route::put('roles/{role}/permissions', [PermissionController::class, 'updateRolePermissions'])->name('roles.permissions.update');
            Route::delete('roles/{role}', [PermissionController::class, 'destroyRole'])->name('roles.destroy');
        });
    });

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
