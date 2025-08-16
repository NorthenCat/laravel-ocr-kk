<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KK;
use App\Models\RW;
use App\Models\Desa;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class KKController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $desa_id, $rw_id)
    {
        try {
            $userId = $request->input('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID is required'
                ], 400);
            }

            $desa = Desa::findOrFail($desa_id);

            // Check if user has access to this desa
            if (!$desa->hasAccess($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to this desa'
                ], 403);
            }

            $rw = RW::where('desa_id', $desa_id)->findOrFail($rw_id);
            $kks = KK::with([
                'getRw.getDesa',
                'getWarga' => function ($query) {
                    $query->orderBy('nama_lengkap', 'asc');
                }
            ])->where('rw_id', $rw_id)->withCount('getWarga')->get();

            return response()->json([
                'success' => true,
                'data' => $kks
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch KK data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $desa_id, $rw_id)
    {
        $validator = Validator::make($request->all(), [
            'no_kk' => 'required|string|max:20',
            'nama_kepala_keluarga' => 'required|string|max:255',
            'user_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = $request->input('user_id');
            $desa = Desa::findOrFail($desa_id);

            // Check if user has access to this desa
            if (!$desa->hasAccess($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to this desa'
                ], 403);
            }

            $rw = RW::where('desa_id', $desa_id)->findOrFail($rw_id);

            // Check if KK number already exists in this RW
            $existingKK = KK::where('rw_id', $rw->id)->where('no_kk', $request->no_kk)->first();
            if ($existingKK) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor KK sudah terdaftar di RW ini'
                ], 409);
            }

            $kk = KK::create([
                'uuid' => Str::uuid(),
                'no_kk' => $request->no_kk,
                'nama_kepala_keluarga' => $request->nama_kepala_keluarga,
                'rw_id' => $rw->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'KK created successfully',
                'data' => $kk->load(['getRw.getDesa', 'getWarga'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create KK',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $desa_id, $rw_id, $kk_id)
    {
        try {
            $userId = $request->input('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID is required'
                ], 400);
            }

            $desa = Desa::findOrFail($desa_id);

            // Check if user has access to this desa
            if (!$desa->hasAccess($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to this desa'
                ], 403);
            }

            $kk = KK::with([
                'getRw.getDesa',
                'getWarga' => function ($query) {
                    $query->orderBy('nama_lengkap', 'asc');
                }
            ])->where('rw_id', $rw_id)->findOrFail($kk_id);

            $kk->loadCount('getWarga');

            return response()->json([
                'success' => true,
                'data' => $kk
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'KK not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $desa_id, $rw_id, $kk_id)
    {
        $validator = Validator::make($request->all(), [
            'no_kk' => 'required|string|max:20',
            'nama_kepala_keluarga' => 'required|string|max:255',
            'user_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = $request->input('user_id');
            $desa = Desa::findOrFail($desa_id);

            // Check if user has access to this desa
            if (!$desa->hasAccess($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to this desa'
                ], 403);
            }

            $kk = KK::where('rw_id', $rw_id)->findOrFail($kk_id);

            // Check if KK number already exists in this RW (excluding current KK)
            $existingKK = KK::where('rw_id', $kk->rw_id)
                ->where('no_kk', $request->no_kk)
                ->where('id', '!=', $kk->id)
                ->first();

            if ($existingKK) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor KK sudah terdaftar di RW ini'
                ], 409);
            }

            $kk->update([
                'no_kk' => $request->no_kk,
                'nama_kepala_keluarga' => $request->nama_kepala_keluarga,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'KK updated successfully',
                'data' => $kk->load(['getRw.getDesa', 'getWarga'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update KK',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $desa_id, $rw_id, $kk_id)
    {
        try {
            $userId = $request->input('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID is required'
                ], 400);
            }

            $desa = Desa::findOrFail($desa_id);

            // Check if user has access to this desa
            if (!$desa->hasAccess($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to this desa'
                ], 403);
            }

            $kk = KK::where('rw_id', $rw_id)->findOrFail($kk_id);
            $kk->delete();

            return response()->json([
                'success' => true,
                'message' => 'KK deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete KK',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show upload form (for API, return upload information)
     */
    public function showUpload($desa_id, $rw_id)
    {
        try {
            $rw = RW::with('getDesa')->where('desa_id', $desa_id)->findOrFail($rw_id);

            return response()->json([
                'success' => true,
                'message' => 'Upload endpoint ready',
                'data' => [
                    'rw' => $rw,
                    'upload_url' => route('api.kk.upload.process', [$desa_id, $rw_id]),
                    'supported_formats' => ['json', 'zip'],
                    'max_file_size' => '50MB'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'RW not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Process file upload
     */
    public function processUpload(Request $request, $desa_id, $rw_id)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:json,zip|max:51200', // 50MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $rw = RW::where('desa_id', $desa_id)->findOrFail($rw_id);

            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            // Store the uploaded file
            $path = $file->store('uploads/kk/' . $rw_id, 'public');

            // Here you would typically process the file
            // For now, we'll just return success with file info
            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'data' => [
                    'file_name' => $originalName,
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'file_type' => $extension,
                    'rw' => $rw
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
