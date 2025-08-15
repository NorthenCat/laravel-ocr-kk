<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use App\Models\DesaUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DesaController extends Controller
{
    private function getApiUrl()
    {
        $apiUrl = config('app.api_url');
        if (!$apiUrl) {
            $apiUrl = config('app.url', 'http://127.0.0.1:8000') . '/api';
        }
        return $apiUrl;
    }

    private function getApiToken()
    {
        return session('api_token');
    }
    private function getUserId()
    {
        $apiUser = session('api_user');
        return $apiUser['id'] ?? null;
    }

    public function show($id)
    {
        try {
            $apiUrl = $this->getApiUrl();
            $apiToken = $this->getApiToken();
            $userId = $this->getUserId();

            if (!$apiToken || !$userId) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            // Get desa data from API
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->get($apiUrl . '/desa/' . $id, [
                    'user_id' => $userId
                ]);

            if ($response->successful()) {
                $apiData = $response->json();
                if ($apiData['success']) {
                    $desa = (object) $apiData['data'];
                    // Convert arrays to objects for compatibility with Blade views
                    $desa->getRw = collect($desa->get_rw ?? [])->map(function ($rw) {
                        $rwObj = (object) $rw;
                        $rwObj->getKK = collect($rwObj->get_k_k ?? [])->map(function ($kk) {
                            $kkObj = (object) $kk;
                            return $kkObj;
                        });

                        $rwObj->getWarga = collect($rwObj->get_warga ?? [])->map(function ($warga) {
                            return (object) $warga;
                        });

                        return $rwObj;
                    });

                    $desa->getUsers = collect($desa->get_users ?? [])->map(function ($user) {
                        return (object) $user;
                    });

                    // dd($desa); // Debugging line to inspect the desa object

                    return view('desa.index', compact('desa'));
                }
            }

            // Fallback to local data if API fails
            $desa = Desa::findOrFail($id);
            $desa->load([
                'getRw.getKK.getWarga',
                'getRw' => function ($query) {
                    $query->orderBy('nama_rw', 'asc');
                },
                'getUsers' => function ($query) {
                    $query->orderBy('name', 'asc');
                }
            ]);
            $desa->loadCount(['getRw', 'getKK']);

            return view('desa.index', compact('desa'));

        } catch (\Exception $e) {
            \Log::error('Failed to fetch desa: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Failed to load desa data.');
        }
    }

    public function create()
    {
        return view('desa.form');
    }

    public function store(Request $request)
    {
        try {
            $apiUrl = $this->getApiUrl();
            $apiToken = $this->getApiToken();
            $userId = $this->getUserId();

            if (!$apiToken || !$userId) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            // Create desa via API
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->post($apiUrl . '/desa', [
                    'nama_desa' => $request->nama_desa,
                    'google_drive' => $request->google_drive,
                    'user_id' => $userId,
                ]);

            if ($response->successful()) {
                $apiData = $response->json();
                if ($apiData['success']) {
                    return redirect()->route('desa.show', $apiData['data']['id'])
                        ->with('success', 'Desa created successfully.');
                }
            }

            // Handle API errors
            $errorData = $response->json();
            if (isset($errorData['errors'])) {
                return redirect()->back()
                    ->withErrors($errorData['errors'])
                    ->withInput();
            }

            return redirect()->back()
                ->withErrors(['error' => $errorData['message'] ?? 'Failed to create desa.'])
                ->withInput();

        } catch (\Exception $e) {
            \Log::error('Failed to create desa via API: ' . $e->getMessage());

            // Fallback to local creation
            $request->validate([
                'nama_desa' => 'required|string|max:255',
                'google_drive' => 'nullable|url|max:500',
            ]);

            $desa = Desa::create([
                'uuid' => Str::uuid(),
                'nama_desa' => $request->nama_desa,
                'google_drive' => $request->google_drive,
            ]);

            DesaUser::create([
                'desa_id' => $desa->id,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('desa.show', $desa->id)->with('success', 'Desa created successfully.');
        }
    }

    public function edit($id)
    {
        try {
            $apiUrl = $this->getApiUrl();
            $apiToken = $this->getApiToken();

            if (!$apiToken) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            // Get desa data from API
            $userId = $this->getUserId();
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->get($apiUrl . '/desa/' . $id, [
                    'user_id' => $userId
                ]);

            if ($response->successful()) {
                $apiData = $response->json();
                if ($apiData['success']) {
                    $desa = (object) $apiData['data'];
                    return view('desa.form', compact('desa'));
                }
            }

            // Fallback to local data
            $desa = Desa::findOrFail($id);
            return view('desa.form', compact('desa'));

        } catch (\Exception $e) {
            \Log::error('Failed to fetch desa for edit: ' . $e->getMessage());
            return redirect()->route('dashboard')->with('error', 'Failed to load desa data.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $apiUrl = $this->getApiUrl();
            $apiToken = $this->getApiToken();

            if (!$apiToken) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            // Update desa via API
            $userId = $this->getUserId();
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->put($apiUrl . '/desa/' . $id, [
                    'nama_desa' => $request->nama_desa,
                    'google_drive' => $request->google_drive,
                    'user_id' => $userId,
                ]);

            if ($response->successful()) {
                $apiData = $response->json();
                if ($apiData['success']) {
                    return redirect()->route('desa.show', $id)
                        ->with('success', 'Desa updated successfully.');
                }
            }

            // Handle API errors
            $errorData = $response->json();
            if (isset($errorData['errors'])) {
                return redirect()->back()
                    ->withErrors($errorData['errors'])
                    ->withInput();
            }

            return redirect()->back()
                ->withErrors(['error' => $errorData['message'] ?? 'Failed to update desa.'])
                ->withInput();

        } catch (\Exception $e) {
            \Log::error('Failed to update desa via API: ' . $e->getMessage());

            // Fallback to local update
            $request->validate([
                'nama_desa' => 'required|string|max:255',
                'google_drive' => 'nullable|url|max:500',
            ]);

            $desa = Desa::findOrFail($id);
            $desa->update([
                'nama_desa' => $request->nama_desa,
                'google_drive' => $request->google_drive,
            ]);

            return redirect()->route('desa.show', $desa->id)->with('success', 'Desa updated successfully.');
        }
    }

    public function destroy($id)
    {
        try {
            $apiUrl = $this->getApiUrl();
            $apiToken = $this->getApiToken();

            if (!$apiToken) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            // Delete desa via API
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->delete($apiUrl . '/desa/' . $id);

            if ($response->successful()) {
                $apiData = $response->json();
                if ($apiData['success']) {
                    return redirect()->route('dashboard')->with('success', 'Desa deleted successfully.');
                }
            }

            return redirect()->back()->with('error', 'Failed to delete desa.');

        } catch (\Exception $e) {
            \Log::error('Failed to delete desa via API: ' . $e->getMessage());

            // Fallback to local deletion
            $desa = Desa::findOrFail($id);
            $desa->delete();

            return redirect()->route('dashboard')->with('success', 'Desa deleted successfully.');
        }
    }

    public function addUser(Request $request, $id)
    {
        try {
            $apiUrl = $this->getApiUrl();
            $apiToken = $this->getApiToken();

            if (!$apiToken) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            // Add user via API
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->post($apiUrl . '/desa/' . $id . '/users', [
                    'user_email' => $request->user_email,
                    'user_id' => $this->getUserId(),
                ]);

            if ($response->successful()) {
                $apiData = $response->json();
                if ($apiData['success']) {
                    return redirect()->back()->with('success', 'User added successfully.');
                }
            }

            // Handle API errors
            $errorData = $response->json();
            return redirect()->back()
                ->withErrors(['user_email' => $errorData['message'] ?? 'Failed to add user.'])
                ->withInput();

        } catch (\Exception $e) {
            \Log::error('Failed to add user via API: ' . $e->getMessage());

            // Fallback to local operation
            $request->validate([
                'user_email' => 'required|email',
            ], [
                'user_email.required' => 'Email is required.',
                'user_email.email' => 'Please enter a valid email address.',
            ]);

            $desa = Desa::findOrFail($id);

            $user = User::where('email', $request->user_email)->first();
            if (!$user) {
                return back()->withInput()->with('error', 'User with this email does not exist in the system.');
            }

            if ($desa->hasAccess($user->id)) {
                return back()->withInput()->with('error', 'User already has access to this village.');
            }

            DesaUser::create([
                'desa_id' => $desa->id,
                'user_id' => $user->id,
            ]);

            return back()->with('success', 'User added successfully.');
        }
    }

    public function removeUser(Request $request, $id)
    {
        try {
            $apiUrl = $this->getApiUrl();
            $apiToken = $this->getApiToken();

            if (!$apiToken) {
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            // Remove user via API
            $response = Http::timeout(10)
                ->withToken($apiToken)
                ->delete($apiUrl . '/desa/' . $id . '/users/' . $request->user_id, [
                    'user_id' => $this->getUserId()
                ]);

            if ($response->successful()) {
                $apiData = $response->json();
                if ($apiData['success']) {
                    return redirect()->back()->with('success', 'User removed successfully.');
                }
            }

            $errorData = $response->json();
            return redirect()->back()->with('error', $errorData['message'] ?? 'Failed to remove user.');

        } catch (\Exception $e) {
            \Log::error('Failed to remove user via API: ' . $e->getMessage());

            // Fallback to local operation
            $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);

            $desa = Desa::findOrFail($id);

            if ($request->user_id == auth()->id()) {
                return back()->with('error', 'You cannot remove yourself from the village.');
            }

            DesaUser::where('desa_id', $desa->id)
                ->where('user_id', $request->user_id)
                ->delete();

            return back()->with('success', 'User removed successfully.');
        }
    }
}
