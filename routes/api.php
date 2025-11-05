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
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\SubscriptionPlanController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\ReportController;

/**
 * @OA\Get(
 *     path="/api/health",
 *     summary="Health check endpoint",
 *     tags={"Health"},
 *     @OA\Response(
 *         response=200,
 *         description="Service is healthy",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="ok")
 *         )
 *     )
 * )
 */
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

// Public settings
Route::get('/settings', [SettingsController::class, 'index']);
Route::get('/settings/api-config', [SettingsController::class, 'apiConfig']);

// Authenticated user info & logout
Route::middleware('auth:sanctum')->group(function () {
	Route::get('/auth/me', [AuthController::class, 'me']);
	Route::post('/auth/logout', [AuthController::class, 'logout']);
	
	// User profile management
	Route::post('/user/update-profile', [UserController::class, 'updateProfile']);
	Route::post('/user/change-password', [UserController::class, 'changePassword']);
	Route::post('/user/upload-profile-image', [UserController::class, 'uploadProfileImage']);
});

// Schedules (read public / write protected for mitra or admin)
Route::get('/schedules', [ScheduleController::class, 'index']);
Route::get('/schedules/{id}', [ScheduleController::class, 'show']);
Route::middleware(['auth:sanctum','role:mitra,admin'])->group(function () {
	Route::post('/schedules', [ScheduleController::class, 'store']);
	Route::put('/schedules/{id}', [ScheduleController::class, 'update']);
	Route::patch('/schedules/{id}', [ScheduleController::class, 'update']);
	Route::delete('/schedules/{id}', [ScheduleController::class, 'destroy']);
	Route::post('/schedules/{id}/complete', [ScheduleController::class, 'complete']);
	Route::post('/schedules/{id}/cancel', [ScheduleController::class, 'cancel']);
});

// Mobile-friendly schedule endpoints (end_user can create schedules)
Route::middleware(['auth:sanctum','role:end_user'])->group(function () {
	Route::post('/schedules/mobile', [ScheduleController::class, 'storeMobileFormat']);
});

// Tracking (write requires mitra)
Route::get('/tracking', [TrackingController::class, 'index']);
Route::get('/tracking/{id}', [TrackingController::class, 'show']);
Route::get('/tracking/schedule/{scheduleId}', [TrackingController::class, 'historyBySchedule']);
Route::middleware(['auth:sanctum','role:mitra,admin'])->group(function () {
	Route::post('/tracking', [TrackingController::class, 'store']);
	Route::put('/tracking/{id}', [TrackingController::class, 'update']);
	Route::patch('/tracking/{id}', [TrackingController::class, 'update']);
	Route::delete('/tracking/{id}', [TrackingController::class, 'destroy'])->middleware('role:admin');
});

// Services (public list, modifications restricted)
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{id}', [ServiceController::class, 'show']);
Route::middleware(['auth:sanctum','role:admin'])->group(function () {
	Route::post('/services', [ServiceController::class, 'store']);
	Route::put('/services/{id}', [ServiceController::class, 'update']);
	Route::patch('/services/{id}', [ServiceController::class, 'update']);
	Route::delete('/services/{id}', [ServiceController::class, 'destroy']);
});

// Orders (creation by end_user, assign/update by mitra)
Route::middleware(['auth:sanctum'])->group(function () {
	Route::get('/orders', [OrderController::class, 'index']);
	Route::get('/orders/{id}', [OrderController::class, 'show']);
	Route::post('/orders', [OrderController::class, 'store'])->middleware('role:end_user');
	Route::put('/orders/{id}', [OrderController::class, 'update'])->middleware('role:end_user,mitra,admin');
	Route::patch('/orders/{id}', [OrderController::class, 'update'])->middleware('role:end_user,mitra,admin');
	Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->middleware('role:end_user,admin');
	Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->middleware('role:end_user');
	Route::patch('/orders/{id}/assign', [OrderController::class, 'assign'])->middleware('role:mitra');
	Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus'])->middleware('role:mitra,admin');
});

// Payments (auth required)
Route::middleware(['auth:sanctum'])->group(function () {
	Route::get('/payments', [PaymentController::class, 'index']);
	Route::get('/payments/{id}', [PaymentController::class, 'show']);
	Route::post('/payments', [PaymentController::class, 'store']);
	Route::put('/payments/{id}', [PaymentController::class, 'update']);
	Route::patch('/payments/{id}', [PaymentController::class, 'update']);
	Route::delete('/payments/{id}', [PaymentController::class, 'destroy'])->middleware('role:admin');
	Route::post('/payments/{id}/mark-paid', [PaymentController::class, 'markPaid']);
});

// Ratings (public read; create must be authenticated end_user)
Route::get('/ratings', [RatingController::class, 'index']);
Route::get('/ratings/{id}', [RatingController::class, 'show']);
Route::middleware(['auth:sanctum','role:end_user'])->group(function () {
	Route::post('/ratings', [RatingController::class, 'store']);
	Route::put('/ratings/{id}', [RatingController::class, 'update']);
	Route::patch('/ratings/{id}', [RatingController::class, 'update']);
	Route::delete('/ratings/{id}', [RatingController::class, 'destroy']);
});

// Notifications (auth required)
Route::middleware(['auth:sanctum'])->group(function () {
	Route::get('/notifications', [NotificationController::class, 'index']);
	Route::get('/notifications/{id}', [NotificationController::class, 'show']);
	Route::post('/notifications', [NotificationController::class, 'store'])->middleware('role:admin');
	Route::put('/notifications/{id}', [NotificationController::class, 'update']);
	Route::patch('/notifications/{id}', [NotificationController::class, 'update']);
	Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
	Route::post('/notifications/mark-read', [NotificationController::class, 'markRead']);
});

// Balance (auth)
Route::middleware(['auth:sanctum'])->group(function () {
	Route::get('/balance/ledger', [BalanceController::class, 'ledger']);
	Route::get('/balance/summary', [BalanceController::class, 'summary']);
	Route::post('/balance/topup', [BalanceController::class, 'topup']);
	Route::post('/balance/withdraw', [BalanceController::class, 'withdraw']);
});

// Chat (auth)
Route::middleware(['auth:sanctum'])->group(function () {
	Route::get('/chats', [ChatController::class, 'index']);
	Route::get('/chats/{id}', [ChatController::class, 'show']);
	Route::post('/chats', [ChatController::class, 'store']);
	Route::put('/chats/{id}', [ChatController::class, 'update']);
	Route::patch('/chats/{id}', [ChatController::class, 'update']);
	Route::delete('/chats/{id}', [ChatController::class, 'destroy']);
});

// Feedback (auth required)
Route::middleware(['auth:sanctum'])->group(function () {
	Route::get('/feedback', [FeedbackController::class, 'index']);
	Route::get('/feedback/{id}', [FeedbackController::class, 'show']);
	Route::post('/feedback', [FeedbackController::class, 'store']);
	Route::put('/feedback/{id}', [FeedbackController::class, 'update']);
	Route::patch('/feedback/{id}', [FeedbackController::class, 'update']);
	Route::delete('/feedback/{id}', [FeedbackController::class, 'destroy']);
});

// Subscription routes (auth required)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/subscription/plans', [SubscriptionPlanController::class, 'index']);
    Route::get('/subscription/plans/{plan}', [SubscriptionPlanController::class, 'show']);
    Route::post('/subscription/plans', [SubscriptionPlanController::class, 'store'])->middleware('role:admin');
    Route::put('/subscription/plans/{plan}', [SubscriptionPlanController::class, 'update'])->middleware('role:admin');
    Route::patch('/subscription/plans/{plan}', [SubscriptionPlanController::class, 'update'])->middleware('role:admin');
    Route::delete('/subscription/plans/{plan}', [SubscriptionPlanController::class, 'destroy'])->middleware('role:admin');
    
    Route::get('/subscription/current', [SubscriptionController::class, 'getCurrentSubscription']);
    Route::get('/subscription/history', [SubscriptionController::class, 'getUserSubscriptions']);
    Route::post('/subscription/subscribe', [SubscriptionController::class, 'store']);
    Route::post('/subscription/{subscription}/activate', [SubscriptionController::class, 'activate']);
    Route::post('/subscription/{subscription}/cancel', [SubscriptionController::class, 'cancel']);
    Route::delete('/subscription/{subscription}', [SubscriptionController::class, 'destroy'])->middleware('role:admin');
});

// Dashboard summaries (auth + role scope)
Route::middleware(['auth:sanctum'])->group(function () {
	Route::get('/dashboard/mitra/{id}', [DashboardController::class, 'mitra'])->middleware('role:mitra,admin');
	Route::get('/dashboard/user/{id}', [DashboardController::class, 'user'])->middleware('role:end_user,admin');
});

// Reports (auth required)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports/{id}', [ReportController::class, 'show']);
    Route::post('/reports', [ReportController::class, 'store']);
    Route::put('/reports/{id}', [ReportController::class, 'update'])->middleware('role:admin');
    Route::patch('/reports/{id}', [ReportController::class, 'update'])->middleware('role:admin');
    Route::delete('/reports/{id}', [ReportController::class, 'destroy'])->middleware('role:admin');
});

// Admin endpoints (admin only)
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/stats', [AdminController::class, 'getStatistics']);
    
    // Users management
    Route::get('/admin/users', [AdminController::class, 'getUsers']);
    Route::get('/admin/users/{id}', [AdminController::class, 'getUser']);
    Route::post('/admin/users', [AdminController::class, 'createUser']);
    Route::put('/admin/users/{id}', [AdminController::class, 'updateUser']);
    Route::patch('/admin/users/{id}', [AdminController::class, 'updateUser']);
    Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser']);
    
    // Logs and system
    Route::get('/admin/logs', [AdminController::class, 'getLogs']);
    Route::delete('/admin/logs', [AdminController::class, 'clearLogs']);
    Route::get('/admin/export', [AdminController::class, 'exportData']);
    Route::post('/admin/notifications', [AdminController::class, 'sendNotification']);
    Route::get('/admin/health', [AdminController::class, 'getSystemHealth']);
});

// Admin settings
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::put('/settings', [SettingsController::class, 'update']);
    Route::patch('/settings', [SettingsController::class, 'update']);
    Route::delete('/settings/{key}', [SettingsController::class, 'destroy']);
});