<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PitchDeckController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StartupController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Sanctum authenticated API for mobile/SPA clients
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn(Request $request) => $request->user());
    Route::post('/email/verification-notification', [AuthController::class, 'sendEmailVerificationNotification']);
    Route::post('/verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Startups
    Route::apiResource('startups', StartupController::class);
    Route::get('/startups/{startup}/analysis', [PitchDeckController::class, 'analysis']);
    Route::get('/discover', [StartupController::class, 'landing']);
    Route::get('/search', [StartupController::class, 'search']);

    // Pitch Decks
    Route::apiResource('pitch-decks', PitchDeckController::class);
    Route::post('/pitch-decks/{pitchDeck}/analyze', [PitchDeckController::class, 'analyze']);

    // Investments
    Route::apiResource('investments', InvestmentController::class);
    Route::post('/investments/{investment}/approve', [InvestmentController::class, 'approve']);
    Route::post('/investments/{investment}/reject', [InvestmentController::class, 'reject']);

    // Documents
    Route::get('/documents', [DocumentController::class, 'index']);
    Route::post('/documents', [DocumentController::class, 'store']);
    Route::get('/documents/{document}/download', [DocumentController::class, 'download']);
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy']);

    // Conversations
    Route::get('/conversations', [ConversationController::class, 'index']);
    Route::post('/conversations', [ConversationController::class, 'store']);
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show']);
    Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'sendMessage']);
    Route::post('/conversations/{conversation}/read', [ConversationController::class, 'markRead']);

    // Meetings
    Route::apiResource('meetings', MeetingController::class);
    Route::post('/meetings/{meeting}/accept', [MeetingController::class, 'accept']);
    Route::post('/meetings/{meeting}/decline', [MeetingController::class, 'decline']);
    Route::post('/meetings/{meeting}/cancel', [MeetingController::class, 'cancel']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);

    // Reports
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf']);
    Route::get('/reports/export-csv', [ReportController::class, 'exportExcel']);

    // Users
    Route::get('/users/profile', [UserController::class, 'profile']);
    Route::put('/users/profile', [UserController::class, 'updateProfile']);
    Route::put('/notification-preferences', [UserController::class, 'updateNotificationPreferences']);
});