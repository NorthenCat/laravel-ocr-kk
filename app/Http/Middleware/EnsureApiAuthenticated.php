<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!session('api_token')) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'API token required'], 401);
            }
            dd(session()->all());

            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        return $next($request);
    }
}
