<?php

return [
    /**
     * SSO Server Configuration
     * 
     * This configuration manages communication with the central SSO server.
     * All credentials are read from .env for security.
     */

    // Enable/Disable SSO
    'enabled' => env('SSO_ENABLED', true),

    // SSO Server Base URL
    'url' => env('SSO_URL', 'http://localhost:8000'),

    // This application's identifier at SSO Server
    'app_id' => env('SSO_APP_ID', 'dm'),

    // Secret key for API authentication with SSO
    'app_secret' => env('SSO_APP_SECRET', ''),

    // HTTP request timeout (seconds)
    'timeout' => env('SSO_TIMEOUT', 10),

    // User model for local session management
    'user_model' => env('SSO_USER_MODEL', 'users'),

    /**
     * Cache Configuration
     * 
     * Cache SSO responses to reduce API calls and improve performance
     */
    'cache_enabled' => env('SSO_CACHE_ENABLED', true),

    // Cache TTL in seconds (5 minutes default)
    'cache_ttl' => env('SSO_CACHE_TTL', 300),

    /**
     * Cookie Configuration
     * 
     * Store SSO token in secure HTTP-only cookie
     */
    'cookie_lifetime' => env('SSO_COOKIE_LIFETIME', 120), // minutes

    /**
     * SSO API Endpoints (relative to SSO_URL)
     * 
     * Customize these if your SSO Server uses different endpoint paths
     */
    'endpoints' => [
        'login' => env('SSO_ENDPOINT_LOGIN', '/api/auth/login'),
        'logout' => env('SSO_ENDPOINT_LOGOUT', '/api/auth/logout'),
        'validate_token' => env('SSO_ENDPOINT_VALIDATE_TOKEN', '/api/auth/validate-sso-token'),
        'verify_access' => env('SSO_ENDPOINT_VERIFY_ACCESS', '/api/auth/verify-app-access'),
        'permissions' => env('SSO_ENDPOINT_PERMISSIONS', '/api/auth/permissions'),
        'health' => env('SSO_ENDPOINT_HEALTH', '/api/health'),
    ],

    /**
     * Local User Fields Mapping
     */
    'user_fields_map' => [
        'nik' => 'nik',
        'name' => 'name',
        'email' => 'email',
        'phone_wa' => 'phone',
        'role' => 'role',
        'organization' => 'organization',
    ],

    /**
     * Redirect Paths
     * 
     * Where to redirect after login/logout/unauthorized
     */
    'redirect' => [
        'login' => env('SSO_REDIRECT_LOGIN', '/'),
        'logout' => env('SSO_REDIRECT_LOGOUT', '/login'),
        'unauthorized' => env('SSO_REDIRECT_UNAUTHORIZED', '/login'),
    ],

    /**
     * Guards Configuration
     */
    'guards' => ['web'],

];
