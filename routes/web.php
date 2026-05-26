<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeployRequestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/* ── Public ───────────────────────────────────────────── */
Route::get('/', fn () => redirect()->route('dashboard'));

/* ── Authenticated ────────────────────────────────────── */
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (dari Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Deploy Requests — CRUD
    Route::resource('deploy-requests', DeployRequestController::class)
        ->except(['destroy']);

    // Approve & Reject — Policy 'decide' enforces PM-only access
    Route::post('/deploy-requests/{deployRequest}/approve', [DeployRequestController::class, 'approve'])
        ->name('deploy-requests.approve');
    Route::post('/deploy-requests/{deployRequest}/reject', [DeployRequestController::class, 'reject'])
        ->name('deploy-requests.reject');

    // Manajemen Aplikasi (khusus Project Manager)
    Route::middleware('role:project_manager')->group(function () {
        Route::resource('applications', ApplicationController::class);
    });

    // Manajemen User (khusus Admin)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class)->except(['show', 'destroy']);
        Route::post('/users/{user}/reset-password', [\App\Http\Controllers\UserController::class, 'resetPassword'])->name('users.reset-password');
    });

    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])
        ->name('notifications.read');
});

require __DIR__.'/auth.php';
