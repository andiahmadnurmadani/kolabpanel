<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;

// --- 1. CONTROLLERS FRONTEND ---
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VolunteerApplicationController;
// Alias Frontend
use App\Http\Controllers\CampaignController as FrontendCampaignController;

// --- 2. CONTROLLERS ADMIN ---
use App\Http\Controllers\Admin\NotifikasiController;
use App\Http\Controllers\Admin\CampaignController as AdminCampaignController;
use App\Http\Controllers\Admin\VolunteerVerificationController;
use App\Http\Controllers\Admin\VolunteerAdminController; 
use App\Http\Controllers\Admin\WithdrawalController;


// Halaman Utama
Route::get('/', [Controller::class, 'home'])->name('home');

// Autentikasi - Updated to use new auth views
Route::get('/login', function() {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::post('/login', [LoginController::class, 'login'])->name('login.post')->middleware('guest');

Route::get('/register', function() {
    return view('auth.register');
})->name('register')->middleware('guest');

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Donasi (Frontend Public)
Route::get('/donation-details/{campaign?}', [DonationController::class, 'index'])->name('donation.details');

// NEW: Donasi Detail dengan slug - menggunakan CampaignController@showDonation
Route::get('/donasi/{slug}', [FrontendCampaignController::class, 'showDonation'])->name('donations.details');

// Donation checkout and processing routes (require authentication to link to user account)
Route::middleware(['auth'])->group(function () {
    Route::get('/donation-checkout/{campaign?}', [DonationController::class, 'checkout'])->name('donation.checkout');
    Route::post('/donation-process', [DonationController::class, 'process'])->name('donation.process');
});

// Donation success and finish routes
Route::get('/donation-success', function () {
    return view('donation-success');
})->name('donation.success');

Route::get('/donation-success/{order_id}', function ($order_id) {
    return view('donation-success', ['order_id' => $order_id]);
})->name('donation.success.with.order');

// Relawan (Frontend Public)
// 1. Landing Page Relawan
Route::get('/relawan', [FrontendCampaignController::class, 'landing'])->name('volunteer.landing');
// 2. List Semua Kampanye Relawan (Pencarian)
Route::get('/volunteer/campaigns', [FrontendCampaignController::class, 'volunteerIndex'])->name('volunteer.campaigns.index');
Route::get('/campaigns', [FrontendCampaignController::class, 'index'])->name('campaigns.all');
// 3. Detail Kampanye
Route::get('/volunteer/campaigns/{slug}', [FrontendCampaignController::class, 'show'])->name('volunteer.campaigns.show');


Route::middleware(['auth'])->group(function () {

    // Profil & Riwayat User (Frontend) - Allow all authenticated users (including admins)
    Route::prefix('profiles')->name('profiles.')->group(function() {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::get('/invoice/{id}', [ProfileController::class, 'invoice'])->name('invoice');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::post('/upload-proof/{order_id}', [ProfileController::class, 'uploadProof'])->name('upload.proof');
    });

    // Authenticated donation routes (require login) - Prevent admin access
    Route::middleware(['auth'])->group(function () {
        Route::get('/donation/manual-transfer/{order_id}', [DonationController::class, 'manualTransfer'])->name('donation.manual.transfer');
        Route::post('/donation/upload-proof/{order_id}', [DonationController::class, 'uploadProof'])->name('donation.upload.proof');
        Route::get('/transaction/download/{order_id}', [DonationController::class, 'downloadTransactionPDF'])->name('transaction.download.pdf');

        // Route untuk melihat status lamaran (Halaman "Surat")
        Route::get('/volunteer/status/{slug}', [VolunteerApplicationController::class, 'checkStatus'])
            ->name('volunteer.application.status');
    });

    // Fitur Notifikasi - Tidak menggunakan middleware prevent.admin.user.pages agar bisa diakses oleh admin juga
    Route::post('/notifications/mark-all-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.markAllRead');

    // Proses Pendaftaran Relawan - tidak menggunakan middleware prevent.admin.user.pages untuk sementara
    Route::get('/volunteer/{slug}/register', [VolunteerApplicationController::class, 'create'])->name('volunteer.register.create');
    Route::post('/volunteer/{slug}/register', [VolunteerApplicationController::class, 'store'])->name('volunteer.register.store');


    // --- ROUTE ADMIN ---
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {

        // Route Penyaluran Dana (Finance)
        Route::get('/withdrawals', [App\Http\Controllers\Admin\WithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::post('/withdrawals', [App\Http\Controllers\Admin\WithdrawalController::class, 'store'])->name('withdrawals.store');
        // Route History
        Route::get('/withdrawals/{id}/history', [App\Http\Controllers\Admin\WithdrawalController::class, 'history'])->name('withdrawals.history');
        
        // Dashboard & Settings
        Route::get('dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::get('settings', function () { return view('admin.settings'); })->name('settings');
        Route::resource('profiles', \App\Http\Controllers\Admin\UserController::class);

        Route::resource('relawan', AdminCampaignController::class);

        // Manajemen Kampanye Donasi (Resource)
        Route::resource('campaigns', \App\Http\Controllers\Admin\DonationCampaignController::class);

        // Manajemen Notifikasi & Relawan (Master Data)
        Route::resource('notifications', NotifikasiController::class);
        Route::resource('volunteers', VolunteerAdminController::class);

        // Manajemen Transaksi Donasi
        Route::get('donation-transactions', [DonationController::class, 'adminIndex'])->name('donations.index');
        Route::get('donation-transactions/{order_id}', [DonationController::class, 'showForAdmin'])->name('donations.show');
        Route::get('donation-transactions/{order_id}/invoice', [DonationController::class, 'showInvoiceForAdmin'])->name('donations.invoice');
        Route::put('donation-transactions/{order_id}/status', [DonationController::class, 'updateStatus'])->name('donations.updateStatus');
        Route::delete('donation-transactions/{order_id}', [DonationController::class, 'destroy'])->name('donations.destroy');

        // API Verifikasi Donasi untuk Admin
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('donation-transactions/{order_id}', [App\Http\Controllers\API\DonationVerificationController::class, 'getTransactionWithProof'])->name('donations.get');
            Route::put('donation-transactions/{order_id}/verify', [App\Http\Controllers\API\DonationVerificationController::class, 'verifyTransaction'])->name('donations.verify');
            Route::get('donation-transactions/pending', [App\Http\Controllers\API\DonationVerificationController::class, 'getPendingVerifications'])->name('donations.pending');
            Route::delete('donation-transactions/{order_id}/proof', [App\Http\Controllers\API\DonationVerificationController::class, 'deleteProof'])->name('donations.proof.delete');
        });


        // Halaman API Verifikasi Donasi
        Route::get('donation-verifications', [App\Http\Controllers\Admin\DonationVerificationController::class, 'index'])->name('donations.api.index');

        // === VERIFIKASI PENDAFTAR RELAWAN ===
        // Route ini disesuaikan dengan Controller yang baru kita buat
        Route::prefix('verifikasi-relawan')->name('verifikasi-relawan.')->group(function () {
            Route::get('/', [VolunteerVerificationController::class, 'index'])->name('index');
            Route::get('/{id}', [VolunteerVerificationController::class, 'show'])->name('show');
            
            // Method UPDATE menangani Approve DAN Reject (Sesuai kode Controller Anda)
            Route::patch('/{id}', [VolunteerVerificationController::class, 'update'])->name('update'); 
            
            // Route Hapus
            Route::delete('/{id}', [VolunteerVerificationController::class, 'destroy'])->name('destroy');
        });
        
        // Route tambahan untuk list pendaftar (jika VolunteerAdminController punya method ini)
        Route::get('/daftar-pendaftar', [VolunteerAdminController::class, 'pendaftarIndex'])->name('pendaftar.list');
    });

// Route untuk testing gambar
Route::get('/test-image/{folder}/{filename}', [App\Http\Controllers\TestImageController::class, 'testImage']);
});