<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeployRequestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/* ── Public ───────────────────────────────────────────── */
Route::get('/', fn() => redirect()->route('dashboard'));

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
    Route::middleware('role:admin')->group(function () {
        Route::resource('applications', ApplicationController::class);
    });

    // Manajemen User (khusus Admin)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class)->except(['show', 'destroy']);
        Route::post('/users/{user}/reset-password', [\App\Http\Controllers\UserController::class, 'resetPassword'])->name('users.reset-password');
        
        // Cek Koneksi WAHA
        Route::get('/waha-connection', [\App\Http\Controllers\WahaConnectionController::class, 'index'])->name('waha-connection.index');
    });

    // IT Work Hub
    Route::prefix('it-work-hub')->name('it-work-hub.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\ItWorkHubController::class, 'dashboard'])->name('dashboard');
        Route::get('/longlist', [\App\Http\Controllers\ItWorkHubController::class, 'longlist'])->name('longlist');
        Route::get('/create', [\App\Http\Controllers\ItWorkHubController::class, 'create'])->name('create');
        Route::post('/store', [\App\Http\Controllers\ItWorkHubController::class, 'store'])->name('store');
        Route::get('/show/{id}', [\App\Http\Controllers\ItWorkHubController::class, 'show'])->name('show');
        
        // Activities
        Route::get('/activities/{id}', [\App\Http\Controllers\ItWorkHubController::class, 'activities'])->name('activities');
        Route::post('/activities/{id}/save', [\App\Http\Controllers\ItWorkHubController::class, 'updateActivities'])->name('activities.save');
        
        // Documents
        Route::post('/documents/{id}/save', [\App\Http\Controllers\ItWorkHubController::class, 'storeDocument'])->name('documents.save');
        Route::delete('/documents/{id}', [\App\Http\Controllers\ItWorkHubController::class, 'destroyDocument'])->name('documents.destroy');

        Route::get('/repository', [\App\Http\Controllers\ItWorkHubController::class, 'repository'])->name('repository');
    });

    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])
        ->name('notifications.read');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/sso.php';
