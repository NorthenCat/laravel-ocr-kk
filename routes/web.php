<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function () {
    // Authentication Routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

});

Route::middleware('auth')->group(function () {
    // Dashboard Route
    Route::get('/dashboard', [\App\Http\Controllers\DasboardController::class, 'index'])->name('dashboard');

    // Desa Management Routes
    Route::post('/desa/{id}/addUser', [\App\Http\Controllers\DesaController::class, 'addUser'])->name('desa.addUser');
    Route::delete('/desa/{id}/removeUser/{userId}', [\App\Http\Controllers\DesaController::class, 'removeUser'])->name('desa.removeUser');
    Route::resource('desa', \App\Http\Controllers\DesaController::class)->except(['index']);

    // Nested RW Routes under Desa
    Route::get('/desa/{desa}/rw/create', [\App\Http\Controllers\RwController::class, 'create'])->name('rw.create');
    Route::post('/desa/{desa}/rw', [\App\Http\Controllers\RwController::class, 'store'])->name('rw.store');
    Route::get('/desa/{desa}/rw/{rw}', [\App\Http\Controllers\RwController::class, 'index'])->name('rw.index');
    Route::get('/desa/{desa}/rw/{rw}/edit', [\App\Http\Controllers\RwController::class, 'edit'])->name('rw.edit');
    Route::put('/desa/{desa}/rw/{rw}', [\App\Http\Controllers\RwController::class, 'update'])->name('rw.update');
    Route::delete('/desa/{desa}/rw/{rw}', [\App\Http\Controllers\RwController::class, 'destroy'])->name('rw.destroy');
    Route::get('/desa/{desa}/rw/{rw}/export-excel', [\App\Http\Controllers\RwController::class, 'exportExcel'])->name('rw.export.excel');


    // JSON Upload route for KK
    Route::get('/desa/{desa}/rw/{rw}/kk/upload', [\App\Http\Controllers\KKController::class, 'showUpload'])->name('kk.upload');
    Route::post('/desa/{desa}/rw/{rw}/kk/upload', [\App\Http\Controllers\KKController::class, 'processUpload'])->name('kk.upload.process');
    // Nested KK Routes under RW
    Route::get('/desa/{desa}/rw/{rw}/kk/create', [\App\Http\Controllers\KKController::class, 'create'])->name('kk.create');
    Route::post('/desa/{desa}/rw/{rw}/kk', [\App\Http\Controllers\KKController::class, 'store'])->name('kk.store');
    Route::get('/desa/{desa}/rw/{rw}/kk/{kk}/edit', [\App\Http\Controllers\KKController::class, 'edit'])->name('kk.edit');
    Route::put('/desa/{desa}/rw/{rw}/kk/{kk}', [\App\Http\Controllers\KKController::class, 'update'])->name('kk.update');
    Route::delete('/desa/{desa}/rw/{rw}/kk/{kk}', [\App\Http\Controllers\KKController::class, 'destroy'])->name('kk.destroy');
    Route::get('/desa/{desa}/rw/{rw}/kk/{kk}', [\App\Http\Controllers\KKController::class, 'index'])->name('kk.index');

    // Nested Anggota Routes under KK
    Route::get('/desa/{desa}/rw/{rw}/kk/{kk}/anggota/create', [\App\Http\Controllers\AnggotaController::class, 'create'])->name('anggota.create');
    Route::post('/desa/{desa}/rw/{rw}/kk/{kk}/anggota', [\App\Http\Controllers\AnggotaController::class, 'store'])->name('anggota.store');
    Route::get('/desa/{desa}/rw/{rw}/kk/{kk}/anggota/{anggota}/edit', [\App\Http\Controllers\AnggotaController::class, 'edit'])->name('anggota.edit');
    Route::put('/desa/{desa}/rw/{rw}/kk/{kk}/anggota/{anggota}', [\App\Http\Controllers\AnggotaController::class, 'update'])->name('anggota.update');
    Route::delete('/desa/{desa}/rw/{rw}/kk/{kk}/anggota/{anggota}', [\App\Http\Controllers\AnggotaController::class, 'destroy'])->name('anggota.destroy');


    // Standalone Anggota Routes (without KK)
    Route::get('/desa/{desa}/rw/{rw}/standalone/{anggota}', [\App\Http\Controllers\AnggotaController::class, 'showStandalone'])->name('anggota.standalone.show');
    Route::get('/desa/{desa}/rw/{rw}/standalone/{anggota}/edit', [\App\Http\Controllers\AnggotaController::class, 'editStandalone'])->name('anggota.standalone.edit');
    Route::put('/desa/{desa}/rw/{rw}/standalone/{anggota}', [\App\Http\Controllers\AnggotaController::class, 'updateStandalone'])->name('anggota.standalone.update');
    Route::delete('/desa/{desa}/rw/{rw}/standalone/{anggota}', [\App\Http\Controllers\AnggotaController::class, 'destroyStandalone'])->name('anggota.standalone.destroy');


    // User Management Routes
    // Route::resource('users', \App\Http\Controllers\UserController::class);

    // Settings Route
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');

    // Failed Files Routes
    Route::get('/desa/{desa}/rw/{rw}/failed-files/{file}', [\App\Http\Controllers\FailedKkFileController::class, 'show'])->name('failed-files.show');
    Route::patch('/desa/{desa}/rw/{rw}/failed-files/{file}/mark-processed', [\App\Http\Controllers\FailedKkFileController::class, 'markAsProcessed'])->name('failed-files.mark-processed');
    Route::delete('/desa/{desa}/rw/{rw}/failed-files/{file}', [\App\Http\Controllers\FailedKkFileController::class, 'destroy'])->name('failed-files.destroy');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});


Route::get('/', function () {
    return view('welcome');
});
