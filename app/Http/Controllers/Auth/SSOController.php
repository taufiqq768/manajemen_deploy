<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SSO\SSOService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use App\Models\CustomUser;
use Exception;

/**
 * SSO Authentication Controller
 * 
 * Handles login, logout, and SSO token management
 */
class SSOController extends Controller
{
    protected $ssoService;

    public function __construct(SSOService $ssoService)
    {
        $this->ssoService = $ssoService;
    }

    /**
     * Show SSO login form
     */
    public function showLoginForm()
    {
        return view('auth.sso-login');
    }

    /**
     * Handle SSO Login
     * 
     * POST /sso/login
     * {
     *   "nik": "user_nik",
     *   "password": "password"
     * }
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            // Get token from SSO Server
            $token = $this->ssoService->login($validated['nik'], $validated['password']);

            // Validate and get user data
            $userData = $this->ssoService->validateToken($token);

            // Verify app access
            if (!$this->ssoService->verifyAppAccess($userData['nik'], $token)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Access to this application is not authorized',
                ], 403);
            }

            // Create local session
            $this->createUserSession($userData, $token);

            // Store token in secure cookie
            Cookie::queue(
                'sso_token',
                $token,
                config('sso.cookie_lifetime', 120),
                '/',
                null,
                config('app.env') === 'production',
                true
            );

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'data' => [
                    'user' => $userData,
                    'token' => $token,
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Handle SSO Logout
     * 
     * POST /sso/logout
     */
    public function logout(Request $request)
    {
        $token = session('sso_token') ?? $request->bearerToken();

        if ($token) {
            // Notify SSO Server
            $this->ssoService->logout($token);
        }

        // Clear local session
        Auth::logout();
        session()->flush();

        // Clear SSO cookie
        Cookie::forget('sso_token');

        return response()->json([
            'status' => 'success',
            'message' => 'Logout successful',
        ]);
    }

    /**
     * Get current user from SSO
     * 
     * GET /sso/me
     */
    public function getCurrentUser(Request $request)
    {
        $token = session('sso_token') ?? $request->bearerToken();

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'No SSO token found',
            ], 401);
        }

        try {
            $userData = $this->ssoService->validateToken($token);
            $permissions = $this->ssoService->getUserPermissions($userData['nik']);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => $userData,
                    'permissions' => $permissions,
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid or expired token',
            ], 401);
        }
    }

    /**
     * Refresh user session
     * 
     * POST /sso/refresh
     */
    public function refreshSession(Request $request)
    {
        $token = session('sso_token') ?? $request->bearerToken();

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'No SSO token found',
            ], 401);
        }

        try {
            $userData = $this->ssoService->validateToken($token);
            $this->ssoService->refreshUser($userData['nik']);

            return response()->json([
                'status' => 'success',
                'message' => 'Session refreshed',
                'data' => $userData,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to refresh session',
            ], 401);
        }
    }

    /**
     * Verify SSO Token (API Endpoint - Pure Token Validation)
     * 
     * GET /sso/verify?token=TOKEN or with Authorization: Bearer TOKEN
     * 
     * Validates token without creating session (use /sso/session-create for that)
     * Returns user data and permissions if valid and authorized
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request)
    {
        $token = $request->query('token');
        $appId = $request->query('app_id', config('sso.app_id', 'dm'));

        if (!$token) {
            return redirect('/login')->with('error', 'Token SSO tidak ditemukan');
        }

        try {
            // Melakukan request HTTP POST menggunakan URL dari .env
            $ssoUrl = rtrim(config('sso.url'), '/');
            $response = \Illuminate\Support\Facades\Http::asForm()->post($ssoUrl . '/api/sso/verify-token', [
                'token' => $token,
                'app_id' => $appId
            ]);

            \Log::info('SSO API Response: ' . $response->body());

            // Jika request berhasil (HTTP status 200)
            if ($response->successful()) {
                $responseData = $response->json();

                // Cek apakah response dari SSO mengembalikan sukses dan memiliki NIK
                // Sesuaikan 'status' atau 'success' sesuai response JSON asli dari SSO Anda
                $isSuccess = $responseData['status'] ?? $responseData['success'] ?? true;

                // Ambil NIK (jika ada di dalam objek 'data', gunakan $responseData['data']['nik'])
                $nik = $responseData['nik'] ?? ($responseData['data']['nik'] ?? null);

                if ($isSuccess && $nik) {
                    // Standarisasi NIK: Pastikan selalu 8 digit. Jika 7 digit, tambahkan '0' di depannya
                    $nik = str_pad((string) $nik, 8, '0', STR_PAD_LEFT);

                    // 1. Cari user di database lokal berdasarkan NIK yang sudah distandarisasi
                    // 2. Jika tidak ada, buatkan data baru otomatis tanpa password
                    $user = \App\Models\User::firstOrCreate(
                        ['nik' => $nik],
                        [
                            'name' => $responseData['name'] ?? ($responseData['data']['name'] ?? 'User SSO'),
                            'email' => $responseData['email'] ?? ($responseData['data']['email'] ?? ''),
                            'phone_wa' => $responseData['phone'] ?? ($responseData['data']['phone'] ?? ''),
                            'role' => 'programmer', // Default role untuk pendaftar baru dari SSO
                        ]
                    );

                    // 3. Lakukan proses login langsung tanpa password menggunakan Session Guard Web
                    \Illuminate\Support\Facades\Auth::login($user);

                    // 4. Arahkan pengguna ke halaman dashboard atau beranda
                    return redirect('/dashboard');
                }
            }

            return redirect('/login')->with('error', 'Gagal verifikasi token SSO atau NIK tidak ditemukan.');

        } catch (\Exception $e) {
            \Log::error('SSO Token Verification Failed: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Terjadi kesalahan sistem saat menghubungi Server SSO.');
        }
    }

    /**
     * Verify Token (API endpoint - POST)
     * 
     * POST /api/sso/verify-token
     * {
     *   "token": "TOKEN",
     *   "app_id": "APP_ID"
     * }
     */
    public function verifyToken(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'app_id' => 'required|string',
        ]);

        try {
            $userData = $this->ssoService->validateToken($validated['token']);
            $hasAccess = $this->ssoService->verifyAppAccess($userData['nik'], $validated['token']);

            if (!$hasAccess) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No access to this application',
                ], 403);
            }

            $permissions = $this->ssoService->getUserPermissions($userData['nik']);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'user' => $userData,
                    'permissions' => $permissions,
                ],
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token verification failed',
            ], 401);
        }
    }

    /**
     * Get User Permissions
     * 
     * GET /sso/permissions
     */
    public function getUserPermissions(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->nik) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated',
            ], 401);
        }

        try {
            $permissions = $this->ssoService->getUserPermissions($user->nik);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'nik' => $user->nik,
                    'permissions' => $permissions,
                ],
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get permissions',
            ], 500);
        }
    }

    /**
     * SSO Callback
     * 
     * GET /sso/callback?code=CODE&state=STATE
     */
    public function callback(Request $request)
    {
        $code = $request->query('code');
        $state = $request->query('state');

        if (!$code || !$state) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid callback parameters',
            ], 400);
        }

        try {
            // Exchange code for token with SSO Server
            $token = $this->exchangeCodeForToken($code, $state);

            if (!$token) {
                throw new Exception('Failed to get token from SSO Server');
            }

            // Validate token
            $userData = $this->ssoService->validateToken($token);

            // Create session
            $this->createUserSession($userData, $token);

            return redirect()->intended(config('sso.redirect.login', '/dashboard'));

        } catch (Exception $e) {
            return redirect()->to(config('sso.redirect.logout', '/login'))->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Exchange authorization code for token
     */
    protected function exchangeCodeForToken(string $code, string $state): ?string
    {
        try {
            $response = \Illuminate\Support\Facades\Http::post(
                config('sso.url') . '/api/oauth/token',
                [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'client_id' => config('sso.app_id'),
                    'client_secret' => config('sso.app_secret'),
                    'redirect_uri' => route('sso.callback'),
                ]
            );

            return $response->json('access_token');
        } catch (Exception $e) {
            \Log::error('Failed to exchange code for token', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Check SSO Server health
     * 
     * GET /sso/health
     */
    public function health()
    {
        $isHealthy = $this->ssoService->isHealthy();

        return response()->json([
            'status' => $isHealthy ? 'ok' : 'error',
            'sso_server' => config('sso.url'),
            'healthy' => $isHealthy,
        ], $isHealthy ? 200 : 503);
    }

    /**
     * Debug: Check current session status
     * 
     * GET /sso/debug-session
     */
    public function debugSession(Request $request)
    {
        $user = Auth::user();
        $userWeb = Auth::guard('web')->user();
        $userCustom = Auth::guard('custom')->user();

        return response()->json([
            'status' => 'debug',
            'timestamp' => now(),
            'session_id' => session()->getId(),
            'authentication' => [
                'authenticated' => Auth::check(),
                'authenticated_web' => Auth::guard('web')->check(),
                'authenticated_custom' => Auth::guard('custom')->check(),
            ],
            'user_data' => [
                'default_user' => $user ? ['id' => $user->id, 'nik' => $user->nik ?? null, 'name' => $user->name, 'email' => $user->email] : null,
                'web_user' => $userWeb ? ['id' => $userWeb->id, 'nik' => $userWeb->nik ?? null, 'name' => $userWeb->name, 'email' => $userWeb->email] : null,
                'custom_user' => $userCustom ? ['id' => $userCustom->id, 'nik' => $userCustom->nik ?? null, 'name' => $userCustom->name, 'email' => $userCustom->email] : null,
            ],
            'session_storage' => [
                'sso_data' => session('sso_data') ? ['nik' => session('sso_data.nik'), 'name' => session('sso_data.name')] : null,
                'sso_token' => session('sso_token') ? '***present***' : null,
            ],
            'configuration' => [
                'user_model' => config('sso.user_model'),
                'guards_configured' => config('sso.guards'),
                'default_guard' => config('auth.defaults.guard'),
                'web_provider' => config('auth.guards.web.provider'),
                'custom_guard' => config('auth.guards.custom'),
                'redirect_login_url' => config('sso.redirect.login'),
            ],
            'cookies' => [
                'sso_token' => $request->cookie('sso_token') ? '***present***' : null,
                'LARAVEL_SESSION' => $request->cookie('LARAVEL_SESSION') ? '***present***' : null,
            ],
        ]);
    }

    /**
     * Create user session in local database
     */
    protected function createUserSession(array $userData, string $token): void
    {
        $userModel = config('sso.user_model', 'users');
        $guards = config('sso.guards', ['web']);

        foreach ($guards as $guard) {
            try {
                if ($userModel === 'custom') {
                    $user = \App\Models\CustomUser::updateOrCreate(
                        ['nik' => $userData['nik']],
                        [
                            'name' => $userData['name'] ?? '',
                            'email' => $userData['email'] ?? '',
                            'phone' => $userData['phone'] ?? '',
                            'role' => $userData['role'] ?? 'user',
                        ]
                    );
                } else {
                    $user = \App\Models\User::updateOrCreate(
                        ['nik' => $userData['nik']],
                        [
                            'name' => $userData['name'] ?? '',
                            'email' => $userData['email'] ?? '',
                            'phone_wa' => $userData['phone'] ?? '',
                            'role' => $userData['role'] ?? 'user',
                        ]
                    );
                }

                session(['sso_token' => $token, 'sso_data' => $userData]);
                Auth::guard($guard)->login($user, true);

                \Log::debug('SSO Session Created Successfully', [
                    'nik' => $userData['nik'],
                    'guard' => $guard,
                    'user_id' => $user->id ?? 'N/A',
                    'authenticated' => Auth::guard($guard)->check(),
                ]);
                break;

            } catch (Exception $e) {
                \Log::warning("Failed to create user session with guard: {$guard}", [
                    'nik' => $userData['nik'] ?? 'unknown',
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                continue;
            }
        }
    }

    /**
     * Handle SSO callback (redirect dari SSO Server)
     * 
     * GET /sso/callback?token=XXX&app_id=dashcockpit
     * or GET /sso/callback?code=CODE&state=STATE (OAuth)
     */
    /**
     * Handle SSO Callback with Token Query Parameter
     * 
     * GET /sso/callback?token=TOKEN_FROM_SSO
     * Or for OAuth: /sso/callback?code=CODE&state=STATE
     * 
     * Creates user session and redirects to home/dashboard
     */
    public function handleCallback(Request $request)
    {
        try {
            // OAuth flow with code
            if ($request->has('code')) {
                $token = $this->exchangeCodeForToken(
                    $request->query('code'),
                    $request->query('state')
                );

                if (!$token) {
                    throw new Exception('Failed to get token from SSO Server');
                }
            } else {
                // Direct token from callback
                $token = $request->query('token');

                if (!$token) {
                    return redirect('/login')->with('error', 'Invalid callback parameters');
                }
            }

            // Validate token
            $userData = $this->ssoService->validateToken($token);

            // Verify app access
            if (!$this->ssoService->verifyAppAccess($userData['nik'], $token)) {
                return redirect('/login')->with('error', 'Access to this application is not authorized');
            }

            // Create session and set cookie
            $this->createUserSession($userData, $token);

            // Store token in secure cookie
            Cookie::queue(
                'sso_token',
                $token,
                config('sso.cookie_lifetime', 120),
                '/',
                null,
                config('app.env') === 'production',
                true
            );

            return redirect(config('sso.redirect.login', '/'))->with('success', 'Login successful');

        } catch (Exception $e) {
            return redirect('/login')->with('error', $e->getMessage());
        }
    }

    /**
     * Create Session from Token
     * 
     * GET /sso/session-create?token=TOKEN_HERE
     * 
     * Quick endpoint to create a session from SSO token
     * Useful for redirect flows from other systems
     */
    public function createSessionFromToken(Request $request)
    {
        $token = $request->query('token') ?? $request->bearerToken();

        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'No token provided',
            ], 400);
        }

        try {
            \Log::debug('SSO Session Create Started', ['token_preview' => substr($token, 0, 20) . '...']);

            // Validate token
            $userData = $this->ssoService->validateToken($token);
            \Log::debug('SSO Token Validated', ['nik' => $userData['nik'] ?? 'unknown', 'name' => $userData['name'] ?? 'unknown']);

            // Verify app access
            if (!$this->ssoService->verifyAppAccess($userData['nik'], $token)) {
                \Log::warning('SSO App Access Denied', ['nik' => $userData['nik']]);
                throw new Exception('Access to this application is not authorized');
            }

            // Create session
            \Log::debug('Creating user session...');
            $this->createUserSession($userData, $token);

            // Verify session was created
            $isAuthenticated = Auth::check() || Auth::guard('web')->check() || Auth::guard('custom')->check();
            \Log::debug('Session Creation Result', [
                'authenticated' => $isAuthenticated,
                'user_id' => Auth::id(),
                'username' => Auth::user()?->name ?? 'N/A',
            ]);

            // Store token in secure cookie
            Cookie::queue(
                'sso_token',
                $token,
                config('sso.cookie_lifetime', 120),
                '/',
                null,
                config('app.env') === 'production',
                true
            );

            $redirectUrl = config('sso.redirect.login', '/');
            \Log::info('SSO Login Success - Redirecting', [
                'nik' => $userData['nik'],
                'name' => $userData['name'],
                'redirect_url' => $redirectUrl,
            ]);

            return redirect($redirectUrl)->with('success', 'Login successful');

        } catch (Exception $e) {
            \Log::error('SSO Session Create Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if (config('app.debug')) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ], 401);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Login failed. Please try again.',
            ], 401);
        }
    }
}

