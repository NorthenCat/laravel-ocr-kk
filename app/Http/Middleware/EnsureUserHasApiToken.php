<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only process if user is authenticated but has no API token
        if (Auth::check() && !session('api_token')) {
            // Skip token generation for login/logout routes to avoid infinite loops
            if (!$request->routeIs('login', 'logout', 'register')) {
                $this->generateApiToken($request);
            }
        }

        return $next($request);
    }

    /**
     * Generate API token for authenticated user
     */
    private function generateApiToken(Request $request)
    {
        $user = Auth::user();

        // For now, let's prioritize local token generation since it's more reliable
        // TODO: In the future, we can add API server token generation if needed
        $this->createLocalApiToken($user);

        /* 
        // Optional: Try to get token from API server if available
        try {
            $apiUrl = config('app.api_url');
            if ($apiUrl && $apiUrl !== config('app.url', 'http://127.0.0.1:8000') . '/api') {
                // Only try API server if it's different from local server
                $this->tryApiServerToken($user, $apiUrl);
            }
        } catch (\Exception $e) {
            Log::debug('API server token generation skipped: ' . $e->getMessage());
        }
        */
    }

    /**
     * Try to get token from external API server (future enhancement)
     */
    private function tryApiServerToken($user, $apiUrl)
    {
        try {
            // Check if user exists on API server
            $response = Http::timeout(5)->get($apiUrl . '/auth/user-by-email', [
                'email' => $user->email,
            ]);

            if ($response->successful()) {
                $apiData = $response->json();
                if ($apiData['success'] && isset($apiData['data']['user'])) {
                    // Try to generate token for them
                    $tokenResponse = Http::timeout(5)->post($apiUrl . '/auth/generate-token', [
                        'email' => $user->email,
                        'user_id' => $apiData['data']['user']['id'],
                    ]);

                    if ($tokenResponse->successful()) {
                        $tokenData = $tokenResponse->json();
                        if ($tokenData['success'] && isset($tokenData['data']['token'])) {
                            // Overwrite local token with API server token
                            session(['api_token' => $tokenData['data']['token']]);
                            session(['api_user' => $apiData['data']['user']]);

                            Log::info('API token generated from API server for remember-me user', ['user_id' => $user->id]);
                            return true;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::debug('API server token generation failed: ' . $e->getMessage());
        }

        return false;
    }

    /**
     * Create local API token using Sanctum
     */
    private function createLocalApiToken($user)
    {
        try {
            // Check if user already has tokens and clean up old ones if too many
            $existingTokens = $user->tokens()->count();
            if ($existingTokens > 5) {
                // Keep only the 3 most recent tokens
                $user->tokens()
                    ->orderBy('created_at', 'desc')
                    ->skip(3)
                    ->take($existingTokens - 3)
                    ->delete();
            }

            // Create a new Sanctum token for the user
            $token = $user->createToken('remember-me-token')->plainTextToken;

            // Store in session
            session(['api_token' => $token]);
            session(['api_user' => $user->toArray()]);

            Log::info('API token generated for remember-me user', ['user_id' => $user->id]);

        } catch (\Exception $e) {
            Log::error('Failed to create local API token: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'error' => $e->getTraceAsString()
            ]);
        }
    }
}
