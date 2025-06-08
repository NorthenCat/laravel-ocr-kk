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


    // JSON Upload route for KK
    Route::get('/desa/{desa}/rw/{rw}/kk/upload', [\App\Http\Controllers\KKController::class, 'showUpload'])->name('kk.upload');
    Route::post('/desa/{desa}/rw/{rw}/kk/upload', [\App\Http\Controllers\KKController::class, 'processUpload'])->name('kk.upload.process');
    // Nested KK Routes under RW
    Route::get('/desa/{desa}/rw/{rw}/kk/create', [\App\Http\Controllers\KKController::class, 'create'])->name('kk.create');
    Route::post('/desa/{desa}/rw/{rw}/kk', [\App\Http\Controllers\KKController::class, 'store'])->name('kk.store');
    Route::get('/desa/{desa}/rw/{rw}/kk/{kk}/edit', [\App\Http\Controllers\KKController::class, 'edit'])->name('kk.edit');
    Route::put('/desa/{desa}/rw/{rw}/kk/{kk}', [\App\Http\Controllers\KKController::class, 'update'])->name('kk.update');
    Route::delete('/desa/{desa}/rw/{rw}/kk/{kk}', [\App\Http\Controllers\KKController::class, 'destroy'])->name('kk.destroy');
    Route::get('/desa/{desa}/rw/{rw}/kk/{kk}', [\App\Http\Controllers\KKController::class, 'index'])->name('kk.show');


    // User Management Routes
    // Route::resource('users', \App\Http\Controllers\UserController::class);

    // Settings Route
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});


Route::get('/', function () {
    return view('welcome');
});
