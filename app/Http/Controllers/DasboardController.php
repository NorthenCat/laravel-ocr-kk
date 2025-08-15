<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DasboardController extends Controller
{
    public function index()
    {
        try {
            // Get API URL from config, with proper fallback
            $apiUrl = config('app.api_url');

            if (!$apiUrl) {
                // If no API URL configured, use local API
                $apiUrl = config('app.url', 'http://localhost') . '/api';
            }

            $fullUrl = $apiUrl . '/desa';

            // Get API token from session
            $apiToken = session('api_token');

            if (!$apiToken) {
                \Log::warning('No API token found in session');
                $desas = [];
                return view('home', compact('desas'));
            }
            // Make HTTP request to API endpoint with authentication
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->get($fullUrl, [
                    'user_id' => session('api_user')['id'] ?? null
                ]);

            if ($response->successful()) {
                $apiData = $response->json();
                $desas = collect($apiData['data'] ?? []); // Convert to Laravel Collection

                \Log::info('Successfully fetched ' . count($desas) . ' desas');
            } else {
                // API call failed, you might want to log this
                \Log::warning('API call failed with status: ' . $response->status() . ', body: ' . $response->body());

                // If unauthorized, clear the token and redirect to login
                if ($response->status() === 401) {
                    session()->forget(['api_token', 'api_user']);
                    return redirect('/login')->with('error', 'Session expired. Please login again.');
                }

                $desas = [];
            }
        } catch (\Exception $e) {
            // Handle connection errors (e.g., API server down)
            \Log::error('Failed to fetch desa data: ' . $e->getMessage());
            $desas = [];
        }

        return view('home', compact('desas'));
    }
}
