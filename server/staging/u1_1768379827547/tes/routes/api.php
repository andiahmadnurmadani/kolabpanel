<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\Api\AuthController as ApiAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Route prefix default: /api (lihat file public/index.php & Route config)
| Semua response di sini berupa JSON sehingga mudah diakses via Postman.
|
*/

Route::get('/ping', function () {
    return response()->json(['message' => 'API OK']);
});

// ------------ AUTH API (Token) ------------
Route::post('/auth/register', [ApiAuthController::class, 'register']);
Route::post('/auth/login', [ApiAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [ApiAuthController::class, 'me']);
    Route::post('/auth/logout', [ApiAuthController::class, 'logout']);
    Route::post('/auth/logout-all', [ApiAuthController::class, 'logoutAll']);

    // Task (Tugas) REST API – protected dengan token
    Route::get('/tasks', [TaskController::class, 'index']);
    Route::get('/tasks/{id}', [TaskController::class, 'show']);
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::put('/tasks/{id}', [TaskController::class, 'update']);
    Route::patch('/tasks/{id}', [TaskController::class, 'update']);
    Route::patch('/tasks/{id}/complete', [TaskController::class, 'complete']);
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);

    // Catatan (Notes) REST API
    Route::get('/notes', [NoteController::class, 'index']);
    Route::get('/notes/{id}', [NoteController::class, 'show']);
    Route::post('/notes', [NoteController::class, 'store']);
    Route::put('/notes/{id}', [NoteController::class, 'update']);
    Route::patch('/notes/{id}', [NoteController::class, 'update']);
    Route::delete('/notes/{id}', [NoteController::class, 'destroy']);

    // Kalender (Activities) REST API
    Route::get('/activities', [ActivityController::class, 'index']);
    Route::get('/activities/{id}', [ActivityController::class, 'show']);
    Route::post('/activities', [ActivityController::class, 'store']);
    Route::put('/activities/{id}', [ActivityController::class, 'update']);
    Route::patch('/activities/{id}', [ActivityController::class, 'update']);
    Route::delete('/activities/{id}', [ActivityController::class, 'destroy']);

    // Materi (Folders & Files) REST API
    // Folders
    Route::get('/folders', [MaterialController::class, 'folders']);
    Route::get('/folders/{id}', [MaterialController::class, 'folderShow']);
    Route::post('/folders', [MaterialController::class, 'folderStore']);
    Route::put('/folders/{id}', [MaterialController::class, 'folderUpdate']);
    Route::patch('/folders/{id}', [MaterialController::class, 'folderUpdate']);
    Route::delete('/folders/{id}', [MaterialController::class, 'folderDestroy']);

    // Files
    Route::get('/files', [MaterialController::class, 'files']);
    Route::get('/files/{id}', [MaterialController::class, 'fileShow']);
    Route::post('/files', [MaterialController::class, 'fileStore']);
    Route::put('/files/{id}', [MaterialController::class, 'fileUpdate']);
    Route::patch('/files/{id}', [MaterialController::class, 'fileUpdate']);
    Route::delete('/files/{id}', [MaterialController::class, 'fileDestroy']);
    Route::get('/files/{id}/download', [MaterialController::class, 'fileDownload']);
    Route::get('/files/{id}/preview', [MaterialController::class, 'filePreview']);
});
