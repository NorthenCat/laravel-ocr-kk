<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\DesaUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class DesaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $desas = Desa::with([
            'getRw.getKK.getWarga',
            'getRw' => function ($query) {
                $query->orderBy('nama_rw', 'asc');
            },
            'getUsers' => function ($query) {
                $query->orderBy('name', 'asc');
            }
        ])->withCount(['getRw', 'getKK'])->get();

        return response()->json([
            'success' => true,
            'data' => $desas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_desa' => 'required|string|max:255',
            'google_drive' => 'nullable|url|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $desa = Desa::create([
                'uuid' => Str::uuid(),
                'nama_desa' => $request->nama_desa,
                'google_drive' => $request->google_drive,
            ]);

            DesaUser::create([
                'desa_id' => $desa->id,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Desa created successfully',
                'data' => $desa->load(['getRw', 'getUsers'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create desa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $desa = Desa::with([
                'getRw.getKK.getWarga',
                'getRw' => function ($query) {
                    $query->orderBy('nama_rw', 'asc');
                },
                'getUsers' => function ($query) {
                    $query->orderBy('name', 'asc');
                }
            ])->withCount(['getRw', 'getKK'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $desa
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Desa not found'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_desa' => 'required|string|max:255',
            'google_drive' => 'nullable|url|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $desa = Desa::findOrFail($id);
            $desa->update([
                'nama_desa' => $request->nama_desa,
                'google_drive' => $request->google_drive,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Desa updated successfully',
                'data' => $desa->load(['getRw', 'getUsers'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update desa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $desa = Desa::findOrFail($id);
            $desa->delete();

            return response()->json([
                'success' => true,
                'message' => 'Desa deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete desa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add user to desa
     */
    public function addUser(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $desa = Desa::findOrFail($id);

            // Check if user exists
            $user = User::where('email', $request->user_email)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User with this email does not exist in the system'
                ], 404);
            }

            // Check if user already has access
            if ($desa->hasAccess($user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'User already has access to this village'
                ], 409);
            }

            DesaUser::create([
                'desa_id' => $desa->id,
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User added successfully',
                'data' => $desa->load('getUsers')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove user from desa
     */
    public function removeUser(Request $request, $id, $userId)
    {
        try {
            $desa = Desa::findOrFail($id);

            // Prevent removing the current user
            if ($userId == auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot remove yourself from the village'
                ], 403);
            }

            $deleted = DesaUser::where('desa_id', $desa->id)
                ->where('user_id', $userId)
                ->delete();

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found in this village'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'User removed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
