<?php

use App\Http\Controllers\Api\{
    AuthController,
    DesaController,
    RwController,
    KKController,
    AnggotaController,
    FailedKkFileController,
    SettingsController,
    DashboardController
};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });
});

// Protected API Routes
Route::middleware(['auth:sanctum'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Desa Management
    Route::apiResource('desa', DesaController::class);
    Route::post('/desa/{desa}/users', [DesaController::class, 'addUser']);
    Route::delete('/desa/{desa}/users/{user}', [DesaController::class, 'removeUser']);

    // RW Management (nested under Desa)
    Route::prefix('desa/{desa}')->group(function () {
        Route::apiResource('rw', RwController::class);
        Route::get('/rw/{rw}/export-excel', [RwController::class, 'exportExcel']);
        Route::get('/rw/{rw}/export-excel-no-filename', [RwController::class, 'exportExcelWithoutFilename']);
        Route::post('/rw/{rw}/kk/process-ocr', [RwController::class, 'processOcr']);

        // KK Management (nested under RW)
        Route::prefix('rw/{rw}')->group(function () {
            Route::apiResource('kk', KKController::class);
            Route::get('/kk/upload', [KKController::class, 'showUpload'])->name('api.kk.upload');
            Route::post('/kk/upload', [KKController::class, 'processUpload'])->name('api.kk.upload.process');

            // Anggota Management (nested under KK)
            Route::prefix('kk/{kk}')->group(function () {
                Route::apiResource('anggota', AnggotaController::class);
            });

            // Standalone Anggota Routes (without KK)
            Route::prefix('standalone')->group(function () {
                Route::get('/{anggota}', [AnggotaController::class, 'showStandalone']);
                Route::put('/{anggota}', [AnggotaController::class, 'updateStandalone']);
                Route::delete('/{anggota}', [AnggotaController::class, 'destroyStandalone']);
            });

            // Failed Files Routes
            Route::prefix('failed-files')->group(function () {
                Route::get('/{file}', [FailedKkFileController::class, 'show']);
                Route::patch('/{file}/mark-processed', [FailedKkFileController::class, 'markAsProcessed']);
                Route::delete('/{file}', [FailedKkFileController::class, 'destroy']);
            });
        });
    });

    // Settings
    Route::get('/settings', [SettingsController::class, 'index']);
    Route::post('/settings', [SettingsController::class, 'update']);
});

// Public Routes
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API is working',
        'timestamp' => now()->toISOString()
    ]);
});
