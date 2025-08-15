<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Str;

class LoginController extends Controller
{

    /**
     * Show the application's login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }


        try {
            // Get API URL for authentication
            $apiUrl = config('app.api_url');
            if (!$apiUrl) {
                $apiUrl = config('app.url', 'http://127.0.0.1:8000') . '/api';
            }

            // Authenticate with API server
            $response = Http::timeout(10)->post($apiUrl . '/auth/login', [
                'email' => $request->email,
                'password' => $request->password,
            ]);

            if ($response->successful()) {
                $apiData = $response->json();
                if ($apiData['success'] && isset($apiData['data']['token'])) {
                    // Store API token in session for subsequent requests
                    session(['api_token' => $apiData['data']['token']]);
                    session(['api_user' => $apiData['data']['user']]);

                    // Find local user by email and manually login
                    $user = User::where('email', $apiData['data']['user']['email'])->first();

                    if ($user) {
                        // Manually set the authenticated user without password check
                        Auth::login($user, $request->filled('remember'));
                        $request->session()->regenerate();

                        return redirect()->intended('/dashboard')
                            ->with('success', 'Login successful! Welcome back to the dashboard.');
                    } else {
                        // User doesn't exist locally, create from API data
                        $user = User::create([
                            'name' => $apiData['data']['user']['name'],
                            'email' => $apiData['data']['user']['email'],
                            'password' => Hash::make(Str::random(32)), // Random password since we use API auth
                        ]);

                        // Assign default role if you're using roles
                        $user->assignRole('admin');

                        Auth::login($user, $request->filled('remember'));
                        $request->session()->regenerate();

                        return redirect()->intended('/dashboard')
                            ->with('success', 'Login successful! Welcome to the dashboard.');
                    }
                } else {
                    return redirect()->back()
                        ->withErrors(['email' => $apiData['message'] ?? 'Authentication failed.'])
                        ->withInput($request->only('email'));
                }
            } else {
                $errorData = $response->json();
                return redirect()->back()
                    ->withErrors(['email' => $errorData['message'] ?? 'These credentials do not match our records.'])
                    ->withInput($request->only('email'));
            }
        } catch (\Exception $e) {
            \Log::error('API authentication failed: ' . $e->getMessage());

            // Fallback to local authentication only
            $credentials = $request->only('email', 'password');
            $remember = $request->filled('remember');

            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();
                return redirect()->intended('/dashboard')
                    ->with('warning', 'Login successful, but API connection failed. Some features may be limited.');
            }

            return redirect()->back()
                ->withErrors(['email' => 'Authentication failed. Please try again.'])
                ->withInput($request->only('email'));
        }
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        try {
            // If we have an API token, try to logout from API server
            if (session('api_token')) {
                $apiUrl = config('app.api_url');
                if (!$apiUrl) {
                    $apiUrl = config('app.url', 'http://localhost') . '/api';
                }

                Http::timeout(5)
                    ->withToken(session('api_token'))
                    ->post($apiUrl . '/auth/logout');
            }
        } catch (\Exception $e) {
            \Log::warning('API logout failed: ' . $e->getMessage());
        }

        // Clear API session data
        session()->forget(['api_token', 'api_user']);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
