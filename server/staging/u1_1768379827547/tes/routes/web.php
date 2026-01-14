<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    LandingController,
    KalenderController,
    AuthController,
    ProfileController,
    TugasController,
    MateriController,
    CatatanController,
    KontenStatisController,
    AdminDashboardController,
    AdminUserController,
    AdminKontenController,
    AdminStatistikController,
};

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Landing Page - selalu bisa diakses
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/change-password', [AuthController::class, 'changePasswordPage'])->name('change');
Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change.post');

// Route untuk tombol "Akses Sistem" di landing page
// Jika sudah login → dashboard, jika belum → login
Route::get('/akses-sistem', function () {
    if (Auth::check()) {
        return Auth::user()->is_admin 
            ? redirect()->route('admin.dashboard') 
            : redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('akses.sistem')->middleware('web');

/*
|--------------------------------------------------------------------------
| Guest Routes (hanya untuk user yang belum login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthController::class, 'loginPage'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // Register
    Route::get('/register', [AuthController::class, 'registerPage'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    // Forgot & Change Password
    Route::get('/forgot-password', [AuthController::class, 'forgotPasswordPage'])->name('forgot');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot.post');
});

// Konten Statis (Public - bisa diakses siapa saja)
Route::get('/konten', [KontenStatisController::class, 'index'])->name('konten.index');
Route::get('/konten/{key}', [KontenStatisController::class, 'show'])->name('konten.show');

/*
|--------------------------------------------------------------------------
| Protected Routes (requires authentication)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Logout - gunakan POST untuk keamanan
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get'); // fallback GET

    // Dashboard
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    // Profile
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile/photo', [AuthController::class, 'updatePhoto'])->name('profile.updatePhoto');
    Route::post('/profile/photo/delete', [AuthController::class, 'deletePhoto'])->name('profile.deletePhoto');

    // Catatan Routes
    Route::get('/catatan', [CatatanController::class, 'index'])->name('catatan');
    Route::post('/catatan/tambah', [CatatanController::class, 'store'])->name('catatan.store');
    Route::post('/catatan/edit/{id}', [CatatanController::class, 'update'])->name('catatan.update');
    Route::get('/catatan/hapus/{id}', [CatatanController::class, 'destroy'])->name('catatan.delete');

    // Tugas Routes
    Route::controller(TugasController::class)->prefix('tugas')->name('tugas.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{id}', 'update')->name('update');
        Route::patch('/{id}/complete', 'complete')->name('complete');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // Kalender Routes
    Route::controller(KalenderController::class)->prefix('kalender')->name('kalender.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
    });

    // Materi Routes
    Route::controller(MateriController::class)->prefix('materi')->name('materi.')->group(function () {
        Route::get('/', 'index')->name('index');
        
        // Folder routes
        Route::post('/folder', 'storeFolder')->name('folder.store');
        Route::put('/folder/{id}', 'updateFolder')->name('folder.update');
        Route::delete('/folder/{id}', 'deleteFolder')->name('folder.delete');
        
        // File routes
        Route::post('/file', 'uploadFile')->name('file.upload');
        Route::get('/file/{id}/download', 'downloadFile')->name('file.download');
        Route::get('/file/{id}/preview', 'previewFile')->name('file.preview');
        Route::put('/file/{id}', 'renameFile')->name('file.rename');
        Route::delete('/file/{id}', 'deleteFile')->name('file.delete');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes (requires authentication + admin role)
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        // Dashboard Admin
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('home');

        // Kelola User
        Route::resource('users', AdminUserController::class)->names('users');

        // Konten Statis
        Route::resource('content', AdminKontenController::class)->names('content');

        // Statistik
        Route::get('statistics', [AdminStatistikController::class, 'index'])->name('statistics');
    });
});


