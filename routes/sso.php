<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SSOController;

/**
 * SSO Authentication Routes
 * 
 * Routes untuk handling SSO login, logout, dan token management
 * Integration dengan centralized SSO Server (localhost:8000)
 */

Route::prefix('sso')->name('sso.')->group(function () {
    // Public routes (tidak perlu auth)
    Route::group(['middleware' => 'guest'], function () {
        // Show SSO login form
        Route::get('/login', [SSOController::class, 'showLoginForm'])->name('show-login');

        // Handle SSO login
        Route::post('/login', [SSOController::class, 'login'])->name('login');

        // SSO callback (dari SSO server redirect)
        Route::get('/callback', [SSOController::class, 'handleCallback'])->name('callback');

        // Create session from token and redirect to dashboard
        Route::get('/session-create', [SSOController::class, 'createSessionFromToken'])->name('session-create');
    });

    // Token verification (public - no auth required)
    Route::get('/verify', [SSOController::class, 'verify'])->name('verify');

    // Debug session status (public - helps troubleshoot login issues)
    Route::get('/debug-session', [SSOController::class, 'debugSession'])->name('debug-session');

    // Protected routes (memerlukan auth)
    Route::group(['middleware' => 'auth:custom'], function () {
        // Get current user
        Route::get('/me', [SSOController::class, 'getCurrentUser'])->name('me');

        // Refresh session
        Route::post('/refresh', [SSOController::class, 'refreshSession'])->name('refresh');

        // Logout
        Route::post('/logout', [SSOController::class, 'logout'])->name('logout');

        // Get user permissions
        Route::get('/permissions', [SSOController::class, 'getUserPermissions'])->name('permissions');
    });

    // Health check (public)
    Route::get('/health', [SSOController::class, 'health'])->name('health');
});

/**
 * API Routes for SSO
 */
Route::prefix('api/sso')->name('api.sso.')->middleware('api')->group(function () {
    // Token validation endpoint (untuk client apps)
    Route::post('/verify-token', [SSOController::class, 'verifyToken'])->name('verify-token');

    // Health check
    Route::get('/health', [SSOController::class, 'health'])->name('health');

    // Protected endpoints
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/me', [SSOController::class, 'getCurrentUser'])->name('me');
    });
});

/**
 * Debug/Test Routes (jika APP_DEBUG=true)
 * 
 * Gunakan untuk testing dan diagnostics tanpa SSO Server
 */
if (config('app.debug')) {
    Route::prefix('sso-test')->name('sso.test.')->group(function () {
        // Debug info endpoint
        Route::get('/debug-info', function () {
            return response()->json([
                'status' => 'debug',
                'environment' => [
                    'app_env' => config('app.env'),
                    'app_debug' => config('app.debug'),
                ],
                'sso_config' => [
                    'enabled' => config('sso.enabled'),
                    'url' => config('sso.url'),
                    'app_id' => config('sso.app_id'),
                    'app_secret_preview' => substr(config('sso.app_secret'), 0, 5) . '***',
                    'timeout' => config('sso.timeout'),
                    'user_model' => config('sso.user_model'),
                ],
                'endpoints_configured' => config('sso.endpoints'),
                'cache' => [
                    'enabled' => config('sso.cache_enabled'),
                    'ttl' => config('sso.cache_ttl'),
                ],
                'test_endpoints' => [
                    'mock_verify' => route('sso.test.mock-verify'),
                    'sso_health' => route('sso.health'),
                    'debug_request' => route('sso.test.debug-request'),
                ],
            ]);
        })->name('debug-info');

        // Mock endpoint untuk testing (tanpa SSO Server)
        Route::get('/mock-verify', function (\Illuminate\Http\Request $request) {
            $token = $request->query('token');

            if (!$token) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No token provided (use ?token=YOUR_TOKEN)',
                ], 400);
            }

            // Return mock user data (untuk testing)
            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => [
                        'nik' => 'TEST' . substr($token, 0, 5),
                        'name' => 'Test User - ' . substr($token, 0, 10),
                        'email' => 'test@example.com',
                        'phone' => '08123456789',
                        'role' => 'user',
                        'organization' => 'Test Organization',
                    ],
                    'permissions' => ['view_reports', 'edit_user', 'delete_user'],
                    'verified' => true,
                    'note' => 'This is mock data. Configure SSO_URL to use real SSO Server.',
                ],
            ]);
        })->name('mock-verify');

        // Debug request details
        Route::get('/debug-request', function (\Illuminate\Http\Request $request) {
            return response()->json([
                'status' => 'debug',
                'request' => [
                    'method' => $request->getMethod(),
                    'url' => $request->getRequestUri(),
                    'headers' => [
                        'Authorization' => $request->header('Authorization') ? 'Bearer ' . substr($request->bearerToken(), 0, 10) . '...' : 'None',
                        'X-App-Id' => $request->header('X-App-Id'),
                        'X-App-Secret' => $request->header('X-App-Secret') ? 'Present' : 'None',
                    ],
                    'query_params' => $request->query(),
                ],
                'advice' => [
                    'if_401_error' => 'Token invalid or SSO Server rejected it',
                    'if_404_error' => 'SSO Server not reachable or endpoint path wrong',
                    'check' => [
                        'SSO_URL' => 'Is your SSO Server running?',
                        'SSO_APP_ID' => 'Is this app registered in SSO Server?',
                        'SSO_APP_SECRET' => 'Is the secret correct?',
                        'Token' => 'Is the token valid and not expired?',
                    ],
                ],
            ]);
        })->name('debug-request');

        // URL to test with mock data
        Route::get('/test-flow', function () {
            $mockToken = 'MOCK_TOKEN_' . uniqid();
            return response()->json([
                'status' => 'test',
                'message' => 'Use these URLs to test SSO flow',
                'flow' => [
                    1 => 'Mock verify: ' . route('sso.test.mock-verify', ['token' => $mockToken]),
                    2 => 'Real verify: /sso/verify?token=' . $mockToken . '&app_id=' . config('sso.app_id'),
                    3 => 'Health check: ' . route('sso.health'),
                    4 => 'Debug info: ' . route('sso.test.debug-info'),
                ],
            ]);
        })->name('test-flow');
    });
}
;
