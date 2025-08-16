<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{

    /**
     * Show the application registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     */
    public function register(Request $request)
    {
        try {
            // Get API URL for registration
            $apiUrl = config('app.api_url');
            if (!$apiUrl) {
                $apiUrl = config('app.url', 'http://127.0.0.1:8000') . '/api';
            }

            // Register with API server
            $response = Http::timeout(10)->post($apiUrl . '/auth/register', [
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
            ]);

            if ($response->successful()) {
                $apiData = $response->json();

                if ($apiData['success'] && isset($apiData['data']['token'])) {
                    // Store API token in session for subsequent requests
                    session(['api_token' => $apiData['data']['token']]);
                    session(['api_user' => $apiData['data']['user']]);

                    // Now login using API as well
                    $loginResponse = Http::timeout(10)->post($apiUrl . '/auth/login', [
                        'email' => $request->email,
                        'password' => $request->password,
                    ]);

                    if ($loginResponse->successful()) {
                        $loginData = $loginResponse->json();
                        if ($loginData['success'] && isset($loginData['data']['token'])) {
                            // Update session with login token
                            session(['api_token' => $loginData['data']['token']]);
                            session(['api_user' => $loginData['data']['user']]);

                            $request->session()->regenerate();
                            return redirect('/dashboard')
                                ->with('success', 'Registration successful! Welcome to the dashboard.');
                        }
                    }

                    // API login failed after registration - clear tokens
                    session()->forget(['api_token', 'api_user']);
                    return redirect()->back()
                        ->withErrors(['email' => 'Login failed after registration.'])
                        ->withInput($request->only('name', 'email'));
                } else {
                    return redirect()->back()
                        ->withErrors(['email' => $apiData['message'] ?? 'Registration failed.'])
                        ->withInput($request->only('name', 'email'));
                }
            } else {
                $errorData = $response->json();
                return redirect()->back()
                    ->withErrors(['email' => $errorData['message'] ?? 'Registration failed. Please try again.'])
                    ->withInput($request->only('name', 'email'));
            }
        } catch (\Exception $e) {
            \Log::error('API registration failed: ' . $e->getMessage());

            // Fallback to local registration only
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('admin'); // Assign default role

            Auth::login($user);

            // Create API token for the new user using Sanctum
            try {
                $token = $user->createToken('web-app-token')->plainTextToken;
                session(['api_token' => $token]);
                session(['api_user' => $user->toArray()]);

                \Log::info('Registration successful with API token created', ['user_id' => $user->id]);
            } catch (\Exception $e) {
                \Log::warning('API token creation failed during registration: ' . $e->getMessage());
                // Continue without API token - the app will work with limited functionality
            }

            return redirect('/dashboard')->with('warning', 'Registration successful, but API connection failed. Some features may be limited.');
        }
    }
}
