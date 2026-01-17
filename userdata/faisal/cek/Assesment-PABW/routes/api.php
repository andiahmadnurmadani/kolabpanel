<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\DonationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\VolunteerCampaignController;
use App\Http\Controllers\Api\VolunteerApplicationController;
use App\Http\Controllers\Api\AdminDonationCampaignController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\CampaignController as MainCampaignController; // Import the main CampaignController

// Route untuk endpoint API campaign untuk Flutter
Route::get('/campaigns', [MainCampaignController::class, 'apiIndex']);

// Route untuk proxy gambar
Route::get('/images/{folder}/{filename}', [\App\Http\Controllers\ImageProxyController::class, 'show'])->name('image.proxy');

// Route untuk API volunteer campaigns (untuk Flutter - tidak memerlukan autentikasi)
Route::get('/volunteer-campaigns', [\App\Http\Controllers\Api\VolunteerCampaignController::class, 'index']);
Route::get('/volunteer-campaigns/{id}', [\App\Http\Controllers\Api\VolunteerCampaignController::class, 'show']);

// Route untuk register dan login (untuk Flutter - tidak memerlukan autentikasi)
Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

// Endpoint debugging sementara untuk membantu Flutter
Route::get('/debug-token', function (Request $request) {
    return response()->json([
        'authenticated' => $request->user() ? true : false,
        'user' => $request->user() ? $request->user()->only(['id', 'name', 'email', 'role']) : null,
        'bearer_token_present' => $request->bearerToken() ? true : false,
        'headers' => $request->headers->all(),
    ]);
});

// Endpoint debugging untuk /v1/me
Route::get('/v1/me-debug', function (Request $request) {
    return response()->json([
        'message' => 'This is a test endpoint for /v1/me',
        'authenticated' => $request->user() ? true : false,
        'bearer_token_present' => $request->bearerToken() ? true : false,
    ]);
});

// Bungkus semua rute dengan prefix v1
Route::prefix('v1')->middleware('cors')->group(function () {
    // API resource untuk volunteer campaigns
    Route::apiResource('volunteer-campaigns', \App\Http\Controllers\Api\VolunteerCampaignController::class);

    // Public routes (Sekarang: /api/v1/register, dsb)
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::apiResource('campaigns', CampaignController::class)->only(['index', 'show']);
    Route::get('/campaigns/urgent', [CampaignController::class, 'urgent']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/refresh', [AuthController::class, 'refresh']);

        // Protected campaign routes
        Route::apiResource('campaigns', CampaignController::class)->except(['index', 'show']);

        Route::get('/donations', [DonationController::class, 'index'])->name('api.donations.index');
        Route::get('/donations/{order_id}', [DonationController::class, 'show']);
        Route::get('/donations/{order_id}/admin', [DonationController::class, 'showForAdmin']);
        Route::get('/my-donations', [DonationController::class, 'getUserDonations'])->name('api.donations.user');
        Route::put('/donations/{order_id}/status', [DonationController::class, 'updateStatus']);

        // User profile routes
        Route::get('/profile', [ProfileController::class, 'show'])->name('api.profile.show');
        Route::put('/profile', [ProfileController::class, 'update'])->name('api.profile.update');
        Route::get('/profile/donations', [ProfileController::class, 'getDonationHistory'])->name('api.profile.donations');
        Route::get('/profile/volunteer', [ProfileController::class, 'getVolunteerHistory'])->name('api.profile.volunteer');
        Route::get('/profile/history', [ProfileController::class, 'getCompleteHistory'])->name('api.profile.history');
        Route::get('/profile/stats', [ProfileController::class, 'getStats'])->name('api.profile.stats');


        // Admin dashboard stats routes (Sekarang: /api/v1/admin/...)
        Route::prefix('admin')->group(function () {
            Route::get('/dashboard/stats', [App\Http\Controllers\Api\AdminController::class, 'dashboardStats']);
            Route::get('/campaigns/stats', [App\Http\Controllers\Api\AdminController::class, 'campaignStats']);
            Route::get('/donations/stats', [App\Http\Controllers\Api\AdminController::class, 'donationStats']);

            // Notification routes
            Route::get('/notifications', [App\Http\Controllers\Api\AdminController::class, 'getNotifications']);
            Route::get('/notifications/unread-count', [App\Http\Controllers\Api\AdminController::class, 'getUnreadNotificationsCount']);
            Route::put('/notifications/mark-all-read', [App\Http\Controllers\Api\AdminController::class, 'markAllAsRead']);
            Route::put('/notifications/{id}/mark-read', [App\Http\Controllers\Api\AdminController::class, 'markAsRead']);
            Route::post('/notifications/send', [App\Http\Controllers\Api\AdminController::class, 'sendNotification']);

            // Coin routes
            Route::get('/coins/history', [App\Http\Controllers\Api\AdminController::class, 'coinHistory']);
            Route::get('/coins/user/{userId}', [App\Http\Controllers\Api\AdminController::class, 'coinHistoryByUser']);
            Route::get('/coins/stats', [App\Http\Controllers\Api\AdminController::class, 'coinStats']);
            Route::post('/coins/award/{userId}', [App\Http\Controllers\Api\AdminController::class, 'awardCoins']);

            // Donation verification routes
            Route::get('/donations/{orderId}/detail', [App\Http\Controllers\Api\AdminController::class, 'donationDetail']);
            Route::put('/donations/{orderId}/status', [App\Http\Controllers\Api\AdminController::class, 'updateDonationStatus']);

            // Additional donation verification routes
            Route::get('/donations/{order_id}/with-proof', [App\Http\Controllers\API\DonationVerificationController::class, 'getTransactionWithProof']);
            Route::put('/donations/{order_id}/verify', [App\Http\Controllers\API\DonationVerificationController::class, 'verifyTransaction']);
            Route::get('/donations/pending', [App\Http\Controllers\API\DonationVerificationController::class, 'getPendingVerifications']);
            Route::delete('/donations/{order_id}/proof', [App\Http\Controllers\API\DonationVerificationController::class, 'deleteProof']);

            // Volunteer management
            Route::get('/volunteers', [App\Http\Controllers\Api\AdminController::class, 'volunteers']);
            Route::get('/volunteers/{id}', [App\Http\Controllers\Api\AdminController::class, 'volunteer']);
            Route::post('/volunteers', [App\Http\Controllers\Api\AdminController::class, 'createVolunteer']);
            Route::put('/volunteers/{id}', [App\Http\Controllers\Api\AdminController::class, 'updateVolunteer']);
            Route::delete('/volunteers/{id}', [App\Http\Controllers\Api\AdminController::class, 'deleteVolunteer']);

            // Volunteer Campaigns admin
            Route::get('/volunteer-campaigns-admin', [App\Http\Controllers\Api\AdminController::class, 'adminVolunteerCampaigns']);
            Route::get('/volunteer-campaigns-admin/{id}', [App\Http\Controllers\Api\AdminController::class, 'adminVolunteerCampaign']);

            // Donation Campaigns management
            Route::apiResource('donation-campaigns', AdminDonationCampaignController::class);

            // Dashboard overview
            Route::get('/dashboard/overview', [App\Http\Controllers\Api\AdminController::class, 'dashboardOverview']);
        });

        // Volunteer Campaigns API endpoints
        Route::apiResource('volunteer-campaigns', VolunteerCampaignController::class);
        Route::apiResource('volunteer-applications', VolunteerApplicationController::class);
    });
});