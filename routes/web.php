<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeployRequestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/* ── Public ───────────────────────────────────────────── */
Route::get('/', fn() => redirect()->route('dashboard'));
Route::get('/api/version', [ApplicationController::class, 'getVersion']);

if (file_exists(__DIR__ . '/dev.php')) {
    require __DIR__ . '/dev.php';
}

/* ── Authenticated ────────────────────────────────────── */
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (dari Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Log Update Versi
    Route::get('/version-logs', [\App\Http\Controllers\VersionLogController::class, 'index'])->name('version-logs.index');

    // Deploy Requests — CRUD
    Route::resource('deploy-requests', DeployRequestController::class)
        ->except(['destroy']);

    // Approve & Reject — Policy 'decide' enforces PM-only access
    Route::post('/deploy-requests/{deployRequest}/approve', [DeployRequestController::class, 'approve'])
        ->name('deploy-requests.approve');
    Route::post('/deploy-requests/{deployRequest}/reject', [DeployRequestController::class, 'reject'])
        ->name('deploy-requests.reject');
    Route::post('/deploy-requests/{deployRequest}/retry-push', [DeployRequestController::class, 'retryPush'])
        ->name('deploy-requests.retry-push');

    // Manajemen Aplikasi (khusus Admin)
    Route::middleware('role:admin')->group(function () {
        Route::get('api-docs', fn() => view('api-docs'))->name('api-docs');
        Route::post('applications/sync', [ApplicationController::class, 'sync'])->name('applications.sync');
        Route::put('applications/{application}/version-api', [ApplicationController::class, 'updateVersionApi'])->name('applications.version-api.update');
        Route::put('applications/{application}/update-version', [ApplicationController::class, 'updateVersionManual'])->name('applications.version.update');
        Route::post('applications/{application}/push-version', [ApplicationController::class, 'pushVersion'])->name('applications.push-version');
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
        Route::post('/update/{id}', [\App\Http\Controllers\ItWorkHubController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [\App\Http\Controllers\ItWorkHubController::class, 'destroy'])->name('destroy');
        Route::post('/status/{id}', [\App\Http\Controllers\ItWorkHubController::class, 'updateStatus'])->name('status.update');
        
        // Activities
        Route::get('/activities/{id}', [\App\Http\Controllers\ItWorkHubController::class, 'activities'])->name('activities');
        Route::post('/activities/{id}/save', [\App\Http\Controllers\ItWorkHubController::class, 'updateActivities'])->name('activities.save');
        
        // Documents
        Route::post('/documents/{id}/save', [\App\Http\Controllers\ItWorkHubController::class, 'storeDocument'])->name('documents.save');
        Route::delete('/documents/{id}', [\App\Http\Controllers\ItWorkHubController::class, 'destroyDocument'])->name('documents.destroy');

        // Non App
        Route::prefix('non-app')->name('non-app.')->group(function () {
            Route::get('/longlist', [\App\Http\Controllers\ItWorkHubNonAppController::class, 'longlist'])->name('longlist');
            Route::get('/create', [\App\Http\Controllers\ItWorkHubNonAppController::class, 'create'])->name('create');
            Route::post('/store', [\App\Http\Controllers\ItWorkHubNonAppController::class, 'store'])->name('store');
            Route::get('/show/{id}', [\App\Http\Controllers\ItWorkHubNonAppController::class, 'show'])->name('show');
            Route::post('/update/{id}', [\App\Http\Controllers\ItWorkHubNonAppController::class, 'update'])->name('update');
            Route::delete('/destroy/{id}', [\App\Http\Controllers\ItWorkHubNonAppController::class, 'destroy'])->name('destroy');
            Route::post('/status/{id}', [\App\Http\Controllers\ItWorkHubNonAppController::class, 'updateStatus'])->name('status.update');
            
            Route::get('/activities/{id}', [\App\Http\Controllers\ItWorkHubNonAppController::class, 'activities'])->name('activities');
            Route::post('/activities/{id}/save', [\App\Http\Controllers\ItWorkHubNonAppController::class, 'updateActivities'])->name('activities.save');
            
            Route::post('/documents/{id}/save', [\App\Http\Controllers\ItWorkHubNonAppController::class, 'storeDocument'])->name('documents.save');
            Route::delete('/documents/{id}', [\App\Http\Controllers\ItWorkHubNonAppController::class, 'destroyDocument'])->name('documents.destroy');
        });

        // Governance
        Route::prefix('governance')->name('governance.')->group(function () {
            Route::get('/longlist', [\App\Http\Controllers\ItWhGovernanceController::class, 'longlist'])->name('longlist');
            Route::post('/store', [\App\Http\Controllers\ItWhGovernanceController::class, 'store'])->name('store');
            Route::get('/show/{id}', [\App\Http\Controllers\ItWhGovernanceController::class, 'show'])->name('show');
            Route::post('/update/{id}', [\App\Http\Controllers\ItWhGovernanceController::class, 'update'])->name('update');
            Route::delete('/destroy/{id}', [\App\Http\Controllers\ItWhGovernanceController::class, 'destroy'])->name('destroy');
            
            Route::get('/activities/{id}', [\App\Http\Controllers\ItWhGovernanceController::class, 'activities'])->name('activities');
            Route::post('/activities/{id}/save', [\App\Http\Controllers\ItWhGovernanceController::class, 'updateActivities'])->name('activities.save');
            
            Route::post('/documents/{id}/save', [\App\Http\Controllers\ItWhGovernanceController::class, 'storeDocument'])->name('documents.save');
            Route::delete('/documents/{id}', [\App\Http\Controllers\ItWhGovernanceController::class, 'destroyDocument'])->name('documents.destroy');
        });

        Route::get('/repository', [\App\Http\Controllers\ItWorkHubController::class, 'repository'])->name('repository');
        Route::get('/project-groups', [\App\Http\Controllers\ItWorkHubController::class, 'projectGroups'])->name('project-groups');
        Route::post('/project-groups/save', [\App\Http\Controllers\ItWorkHubController::class, 'updateProjectGroups'])->name('project-groups.save');
        
        // To-Do List
        Route::get('/todo', [\App\Http\Controllers\ItWorkHubController::class, 'todo'])->name('todo');
        Route::post('/todo/save', [\App\Http\Controllers\ItWorkHubController::class, 'updateTodos'])->name('todo.save');
    });

    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])
        ->name('notifications.read');
});

require __DIR__ . '/auth.php';
require __DIR__ . '/sso.php';
