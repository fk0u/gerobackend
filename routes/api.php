<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\BalanceController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\DashboardController;

Route::get('/health', fn () => response()->json(['status' => 'ok']));
Route::get('/ping', fn () => response()->json([
    'status' => 'ok',
    'message' => 'Gerobaks API is running',
    'timestamp' => now()->toDateTimeString(),
    'environment' => config('app.env')
]));

// Public auth endpoints
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Authenticated user info & logout
Route::middleware('auth:sanctum')->group(function () {
	Route::get('/auth/me', [AuthController::class, 'me']);
	Route::post('/auth/logout', [AuthController::class, 'logout']);
});

// Schedules (read public / write protected for mitra or admin)
Route::get('/schedules', [ScheduleController::class, 'index']);
Route::get('/schedules/{id}', [ScheduleController::class, 'show']);
Route::middleware(['auth:sanctum','role:mitra,admin'])->group(function () {
	Route::post('/schedules', [ScheduleController::class, 'store']);
	Route::patch('/schedules/{id}', [ScheduleController::class, 'update']);
});

// Tracking (write requires mitra)
Route::get('/tracking', [TrackingController::class, 'index']);
Route::get('/tracking/schedule/{scheduleId}', [TrackingController::class, 'historyBySchedule']);
Route::middleware(['auth:sanctum','role:mitra'])->post('/tracking', [TrackingController::class, 'store']);

// Services (public list, modifications restricted)
Route::get('/services', [ServiceController::class, 'index']);
Route::middleware(['auth:sanctum','role:admin'])->group(function () {
	Route::post('/services', [ServiceController::class, 'store']);
	Route::patch('/services/{id}', [ServiceController::class, 'update']);
});

// Orders (creation by end_user, assign/update by mitra)
Route::middleware(['auth:sanctum'])->group(function () {
	Route::get('/orders', [OrderController::class, 'index']);
	Route::get('/orders/{id}', [OrderController::class, 'show']);
	Route::post('/orders', [OrderController::class, 'store'])->middleware('role:end_user');
	Route::patch('/orders/{id}/assign', [OrderController::class, 'assign'])->middleware('role:mitra');
	Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus'])->middleware('role:mitra,admin');
});

// Payments (auth required)
Route::middleware(['auth:sanctum'])->group(function () {
	Route::get('/payments', [PaymentController::class, 'index']);
	Route::post('/payments', [PaymentController::class, 'store']);
	Route::patch('/payments/{id}', [PaymentController::class, 'update']);
	Route::post('/payments/{id}/mark-paid', [PaymentController::class, 'markPaid']);
});

// Ratings (public read; create must be authenticated end_user)
Route::get('/ratings', [RatingController::class, 'index']);
Route::middleware(['auth:sanctum','role:end_user'])->post('/ratings', [RatingController::class, 'store']);

// Notifications (auth required)
Route::middleware(['auth:sanctum'])->group(function () {
	Route::get('/notifications', [NotificationController::class, 'index']);
	Route::post('/notifications', [NotificationController::class, 'store'])->middleware('role:admin');
	Route::post('/notifications/mark-read', [NotificationController::class, 'markRead']);
});

// Balance (auth)
Route::middleware(['auth:sanctum'])->group(function () {
	Route::get('/balance/ledger', [BalanceController::class, 'ledger']);
	Route::get('/balance/summary', [BalanceController::class, 'summary']);
});

// Chat (auth)
Route::middleware(['auth:sanctum'])->group(function () {
	Route::get('/chats', [ChatController::class, 'index']);
	Route::post('/chats', [ChatController::class, 'store']);
});

// Dashboard summaries (auth + role scope)
Route::middleware(['auth:sanctum'])->group(function () {
	Route::get('/dashboard/mitra/{id}', [DashboardController::class, 'mitra'])->middleware('role:mitra,admin');
	Route::get('/dashboard/user/{id}', [DashboardController::class, 'user'])->middleware('role:end_user,admin');
});