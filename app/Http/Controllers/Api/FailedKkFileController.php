<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FailedKkFile;
use App\Models\Desa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FailedKkFileController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(Request $request, $desa_id, $rw_id, $file_id)
    {
        try {
            $userId = $request->input('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID is required'
                ], 400);
            }

            // Check desa access
            $desa = Desa::find($desa_id);
            if (!$desa || !$desa->hasAccess($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to this desa'
                ], 403);
            }

            $file = FailedKkFile::with(['rw.getDesa'])
                ->where('rw_id', $rw_id)
                ->findOrFail($file_id);
            $file->append('failure_reason_text');

            return response()->json([
                'success' => true,
                'data' => $file
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed file not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Mark file as processed
     */
    public function markAsProcessed(Request $request, $desa_id, $rw_id, $file_id)
    {
        try {
            $userId = $request->input('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID is required'
                ], 400);
            }

            // Check desa access
            $desa = Desa::find($desa_id);
            if (!$desa || !$desa->hasAccess($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to this desa'
                ], 403);
            }

            $file = FailedKkFile::where('rw_id', $rw_id)->findOrFail($file_id);

            $file->update([
                'manually_processed' => true,
                'processed_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'File marked as processed successfully',
                'data' => $file
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark file as processed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $desa_id, $rw_id, $file_id)
    {
        try {
            $userId = $request->input('user_id');
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User ID is required'
                ], 400);
            }

            // Check desa access
            $desa = Desa::find($desa_id);
            if (!$desa || !$desa->hasAccess($userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to this desa'
                ], 403);
            }

            $file = FailedKkFile::where('rw_id', $rw_id)->findOrFail($file_id);
            $file->delete();

            return response()->json([
                'success' => true,
                'message' => 'Failed file deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete file',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
