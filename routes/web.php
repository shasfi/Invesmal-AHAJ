<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StartupController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\VerificationController;
use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\InvestmentController;
use App\Http\Controllers\PitchDeckController;
use App\Http\Controllers\NotificationPreferenceController;
use App\Http\Controllers\AI\InsightsController;
use App\Http\Controllers\AI\SentimentController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\FirebaseAuthController;

Route::get('/', [StartupController::class, 'landing'])->name('landing');

Route::get('/discover', [StartupController::class, 'discover'])->name('startups.discover');
Route::get('/discover/search', [StartupController::class, 'search'])->name('startups.search');
Route::redirect('/startups/landing', '/discover')->name('startups.landing');

Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt')->middleware('throttle:20,1');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.attempt')->middleware('throttle:10,1');
    Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:10,1');

    Route::get('/auth/complete-role', [SocialAuthController::class, 'showRoleForm'])->name('oauth.role');
    Route::post('/auth/complete-role', [SocialAuthController::class, 'completeRegistration'])->name('oauth.complete');
    Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('oauth.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('oauth.callback');
    Route::post('/auth/firebase', [FirebaseAuthController::class, 'authenticate'])->name('firebase.authenticate');
    Route::get('/auth/firebase/role', [FirebaseAuthController::class, 'showRoleForm'])->name('firebase.role');
    Route::post('/auth/firebase/role', [FirebaseAuthController::class, 'completeRegistration'])->name('firebase.complete');
});

// Works logged out or on any device — signs into the email from the link (not an old session)
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['signed', 'throttle:10,1'])
    ->name('verification.verify');

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/email/verify', [AuthController::class, 'showVerifyNotice'])->name('verification.notice');
    Route::post('/email/wrong-account', [AuthController::class, 'wrongAccount'])->name('verification.wrong-account');
    Route::post('/email/check-status', [AuthController::class, 'checkVerificationStatus'])->name('verification.check-status');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])
        ->middleware('throttle:5,1')
        ->name('verification.send');

    Route::middleware('verified')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/dashboard/admin', [AdminDashboardController::class, 'index'])->name('dashboard.admin');
    Route::get('/dashboard/founder', fn() => redirect()->route('dashboard'))->name('dashboard.founder');
    Route::get('/dashboard/investor', fn() => redirect()->route('dashboard'))->name('dashboard.investor');
    Route::get('/dashboard/mentor', fn() => redirect()->route('dashboard'))->name('dashboard.mentor');

    Route::resource('users', UserController::class)->except(['show', 'destroy']);
    Route::get('users/{user}/profile', [UserController::class, 'profile'])->name('users.profile');
    Route::get('users/investors', [UserController::class, 'showInvestors'])->name('users.investors');
    Route::get('users/founders', [UserController::class, 'showFounders'])->name('users.founders');
    Route::resource('startups', StartupController::class)->except(['show', 'destroy']);

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');

    Route::get('/notification-preferences', [NotificationPreferenceController::class, 'edit'])->name('notification.preferences.edit');
    Route::put('/notification-preferences', [NotificationPreferenceController::class, 'update'])->name('notification.preferences.update');

    Route::get('/ai/insights', [InsightsController::class, 'index'])->name('ai.insights');
    Route::get('/ai/sentiment', [SentimentController::class, 'index'])->name('ai.sentiment.index');
    Route::get('/ai/sentiment/{conversation}', [SentimentController::class, 'show'])->name('ai.sentiment.show');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
    Route::get('/reports/export-csv', [ReportController::class, 'exportExcel'])->name('reports.export-csv');

    Route::prefix('admin')->middleware('role:admin')->name('admin.')->group(function () {
        Route::get('/verification', [VerificationController::class, 'index'])->name('verification.index');
        Route::post('/users/{user}/verify', [VerificationController::class, 'verifyUser'])->name('verify.user');
        Route::post('/startups/{startup}/verify', [VerificationController::class, 'verifyStartup'])->name('verify.startup');
        Route::post('/users/{user}/approve', [VerificationController::class, 'approveUser'])->name('users.approve');
        Route::post('/users/{user}/reject', [VerificationController::class, 'rejectUser'])->name('users.reject');
        Route::post('/startups/{startup}/approve', [VerificationController::class, 'approveStartup'])->name('startups.approve');
        Route::post('/startups/{startup}/reject', [VerificationController::class, 'rejectStartup'])->name('startups.reject');
        Route::post('/users/{user}/update-status', [VerificationController::class, 'updateUserStatus'])->name('users.update-status');
        Route::post('/startups/{startup}/update-status', [VerificationController::class, 'updateStartupStatus'])->name('startups.update-status');
        Route::get('/moderation', [ModerationController::class, 'index'])->name('moderation.index');
        Route::post('/startups/{startup}/flag', [ModerationController::class, 'flag'])->name('moderation.flag');
        Route::post('/startups/{startup}/unflag', [ModerationController::class, 'unflag'])->name('moderation.unflag');
        Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    });

    Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');
    Route::get('/conversations/create/new', [ConversationController::class, 'create'])->name('conversations.create');
    Route::post('/conversations', [ConversationController::class, 'store'])->name('conversations.store');
    Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'sendMessage'])->name('conversations.send-message');
    Route::post('/conversations/{conversation}/read', [ConversationController::class, 'markRead'])->name('conversations.read');

    Route::resource('meetings', MeetingController::class)->except(['edit', 'destroy']);
    Route::patch('/meetings/{meeting}/accept', [MeetingController::class, 'accept'])->name('meetings.accept');
    Route::patch('/meetings/{meeting}/decline', [MeetingController::class, 'decline'])->name('meetings.decline');
    Route::patch('/meetings/{meeting}/cancel', [MeetingController::class, 'cancel'])->name('meetings.cancel');

    Route::resource('documents', DocumentController::class)->except(['show', 'edit', 'update']);
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');

    Route::get('/startups/{startup}/invest', [InvestmentController::class, 'create'])->name('investments.create.startup');
    Route::resource('investments', InvestmentController::class)->except(['edit', 'destroy']);
    Route::patch('/investments/{investment}/approve', [InvestmentController::class, 'approve'])->name('investments.approve');
    Route::patch('/investments/{investment}/reject', [InvestmentController::class, 'reject'])->name('investments.reject');

    Route::get('/pitch-decks', [PitchDeckController::class, 'index'])->name('pitch_decks.index');
    Route::get('/pitch-decks/generate', [PitchDeckController::class, 'create'])->name('pitch_decks.create');
    Route::post('/pitch-decks/generate', [PitchDeckController::class, 'generate'])->name('pitch_decks.generate');
    Route::get('/pitch-decks/upload', fn() => view('pitch_decks.upload'))->name('pitch_decks.upload.form');
    Route::post('/pitch-decks/upload', [PitchDeckController::class, 'upload'])->name('pitch_decks.upload');
    Route::get('/pitch-decks/{pitchDeck}', [PitchDeckController::class, 'show'])->name('pitch_decks.show');
    Route::get('/pitch-decks/{pitchDeck}/edit', [PitchDeckController::class, 'edit'])->name('pitch_decks.edit');
    Route::put('/pitch-decks/{pitchDeck}', [PitchDeckController::class, 'update'])->name('pitch_decks.update');
    Route::get('/pitch-decks/{pitchDeck}/analyze', [PitchDeckController::class, 'analyze'])->name('pitch_decks.analyze');
    Route::get('/pitch-decks/{pitchDeck}/analysis', [PitchDeckController::class, 'analysis'])->name('pitch_decks.analysis');
    Route::delete('/pitch-decks/{pitchDeck}', [PitchDeckController::class, 'destroy'])->name('pitch_decks.destroy');
    });
});

Route::get('/startups/{startup}', [StartupController::class, 'show'])->name('startups.show');
Route::get('/api/pitch-decks/{pitchDeck}/summary', [PitchDeckController::class, 'publicSummary'])->name('pitch_decks.public_summary');