<?php

namespace App\Services\SSO;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * SSO Service - Client App Integration
 * 
 * Communicates with SSO Server via HTTP API
 * Never accesses db_sso directly (API only architecture)
 */
class SSOService
{
    protected $ssoUrl;
    protected $appId;
    protected $appSecret;
    protected $timeout;
    protected $cacheEnabled;
    protected $endpoints;

    public function __construct()
    {
        $this->ssoUrl = rtrim(config('sso.url'), '/');
        $this->appId = config('sso.app_id');
        $this->appSecret = config('sso.app_secret');
        $this->timeout = config('sso.timeout', 10);
        $this->cacheEnabled = config('sso.cache_enabled', true);
        $this->endpoints = config('sso.endpoints', []);
    }

    /**
     * Validate SSO Token with SSO Server
     * 
     * @param string $token SSO Bearer token
     * @return array User data {nik, name, email, phone, role, roles, organization}
     * @throws Exception
     */
    public function validateToken(string $token): array
    {
        try {
            // Check cache first
            $cacheKey = "sso:token:{$token}";
            if ($this->cacheEnabled && Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            // Call SSO Server API - use POST with token in body
            $endpoint = $this->getEndpoint('validate_token', '/api/auth/validate-sso-token');
            $fullUrl = "{$this->ssoUrl}{$endpoint}";

            if (config('app.debug')) {
                Log::info('SSO validateToken request', [
                    'url' => $fullUrl,
                    'token_preview' => substr($token, 0, 10) . '...',
                    'app_id' => $this->appId,
                    'app_secret_preview' => substr($this->appSecret, 0, 5) . '***',
                ]);
            }

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'X-App-Id' => $this->appId,
                    'X-App-Secret' => $this->appSecret,
                ])
                ->post($fullUrl, [
                    'token' => $token,
                    'app_id' => $this->appId,
                ]);

            if (!$response->successful()) {
                Log::warning('SSO Token validation failed', [
                    'status' => $response->status(),
                    'url' => $fullUrl,
                    'app_id' => $this->appId,
                    'response_body' => $response->body(),
                ]);
                throw new Exception("SSO validation failed: {$response->status()} " . $response->body());
            }

            $userData = $response->json('data', []);

            // Cache the result
            if ($this->cacheEnabled) {
                $cacheTtl = config('sso.cache_ttl', 300); // 5 minutes default
                Cache::put($cacheKey, $userData, $cacheTtl);
            }

            return $userData;
        } catch (Exception $e) {
            Log::error('SSO Token validation error', [
                'error' => $e->getMessage(),
                'token' => substr($token, 0, 20) . '...',
            ]);
            throw $e;
        }
    }

    /**
     * Get endpoint from config or use default
     */
    protected function getEndpoint(string $key, string $default): string
    {
        return (array_key_exists($key, $this->endpoints) && $this->endpoints[$key])
            ? $this->endpoints[$key]
            : $default;
    }

    /**
     * Get Bearer Token for SSO Login
     * 
     * @param string $nik User NIK
     * @param string $password User password
     * @return string SSO Bearer token
     * @throws Exception
     */
    public function login(string $nik, string $password): string
    {
        try {
            $endpoint = $this->getEndpoint('login', '/api/auth/login');
            $response = Http::timeout($this->timeout)
                ->post("{$this->ssoUrl}{$endpoint}", [
                    'nik' => $nik,
                    'password' => $password,
                    'app_id' => $this->appId,
                ]);

            if (!$response->successful()) {
                Log::warning('SSO Login failed', [
                    'status' => $response->status(),
                    'nik' => $nik,
                ]);
                throw new Exception("SSO login failed: {$response->status()}");
            }

            $token = $response->json('data.token');
            if (!$token) {
                throw new Exception("No token received from SSO");
            }

            return $token;
        } catch (Exception $e) {
            Log::error('SSO Login error', [
                'error' => $e->getMessage(),
                'nik' => $nik,
            ]);
            throw $e;
        }
    }

    /**
     * Verify if user has access to this app
     * 
     * @param string $nik User NIK
     * @param string $token SSO Token (optional)
     * @return bool
     */
    public function verifyAppAccess(string $nik, ?string $token = null): bool
    {
        try {
            $headers = [
                'X-App-Id' => $this->appId,
                'X-App-Secret' => $this->appSecret,
            ];

            if ($token) {
                $headers['Authorization'] = "Bearer {$token}";
            }

            $endpoint = $this->getEndpoint('verify_access', '/api/auth/verify-app-access');
            $response = Http::timeout($this->timeout)
                ->withHeaders($headers)
                ->post("{$this->ssoUrl}{$endpoint}", [
                    'nik' => $nik,
                    'app_id' => $this->appId,
                ]);

            Log::info('SSO verify-app-access response', [
                'nik' => $nik,
                'app_id' => $this->appId,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            $hasAccess = $response->successful() && $response->json('data.has_access', false);

            Log::info('SSO verify-app-access result', [
                'nik' => $nik,
                'has_access' => $hasAccess,
            ]);

            return $hasAccess;
        } catch (Exception $e) {
            Log::error('SSO App access verification error', [
                'error' => $e->getMessage(),
                'nik' => $nik,
            ]);
            return false;
        }
    }

    /**
     * Get user permissions for this app
     * 
     * @param string $nik User NIK
     * @return array Permissions
     */
    public function getUserPermissions(string $nik): array
    {
        try {
            $cacheKey = "sso:permissions:{$nik}";
            if ($this->cacheEnabled && Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $endpoint = $this->getEndpoint('permissions', '/api/auth/permissions');
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'X-App-Id' => $this->appId,
                    'X-App-Secret' => $this->appSecret,
                ])
                ->get("{$this->ssoUrl}{$endpoint}/{$nik}", [
                    'app_id' => $this->appId,
                ]);

            if (!$response->successful()) {
                return [];
            }

            $permissions = $response->json('data.permissions', []);

            if ($this->cacheEnabled) {
                $cacheTtl = config('sso.cache_ttl', 300);
                Cache::put($cacheKey, $permissions, $cacheTtl);
            }

            return $permissions;
        } catch (Exception $e) {
            Log::error('SSO Permissions fetch error', [
                'error' => $e->getMessage(),
                'nik' => $nik,
            ]);
            return [];
        }
    }

    /**
     * Logout from SSO
     * 
     * @param string $token SSO Token
     * @return bool
     */
    public function logout(string $token): bool
    {
        try {
            $endpoint = $this->getEndpoint('logout', '/api/auth/logout');
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => "Bearer {$token}",
                    'X-App-Id' => $this->appId,
                ])
                ->post("{$this->ssoUrl}{$endpoint}");

            // Clear cache
            Cache::forget("sso:token:{$token}");

            return $response->successful();
        } catch (Exception $e) {
            Log::error('SSO Logout error', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Check if SSO Server is healthy
     * 
     * @return bool
     */
    public function isHealthy(): bool
    {
        try {
            $endpoint = $this->getEndpoint('health', '/api/health');
            $response = Http::timeout($this->timeout)
                ->get("{$this->ssoUrl}{$endpoint}");
            return $response->successful();
        } catch (Exception $e) {
            Log::error('SSO Health check failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Refresh user data from SSO
     * Clears cache for this user
     * 
     * @param string $nik User NIK
     * @return void
     */
    public function refreshUser(string $nik): void
    {
        Cache::forget("sso:permissions:{$nik}");
    }
}
