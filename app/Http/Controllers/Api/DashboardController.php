<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\RW;
use App\Models\KK;
use App\Models\Anggota;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display dashboard statistics.
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            // Get user's accessible desas
            $accessibleDesaIds = $user->hasRole('super-admin') 
                ? Desa::pluck('id') 
                : $user->desas()->pluck('desa_id');

            // Basic statistics
            $stats = [
                'total_desa' => Desa::whereIn('id', $accessibleDesaIds)->count(),
                'total_rw' => RW::whereIn('desa_id', $accessibleDesaIds)->count(),
                'total_kk' => KK::whereHas('getRw', function ($query) use ($accessibleDesaIds) {
                    $query->whereIn('desa_id', $accessibleDesaIds);
                })->count(),
                'total_anggota' => Anggota::whereHas('getKk.getRw', function ($query) use ($accessibleDesaIds) {
                    $query->whereIn('desa_id', $accessibleDesaIds);
                })->count(),
                'total_users' => User::count()
            ];

            // Recent data
            $recentDesas = Desa::whereIn('id', $accessibleDesaIds)
                ->with(['getRw', 'getUsers'])
                ->withCount(['getRw', 'getKK'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $recentRws = RW::whereIn('desa_id', $accessibleDesaIds)
                ->with(['getDesa', 'getKK'])
                ->withCount(['getKK', 'getWarga'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $recentKks = KK::whereHas('getRw', function ($query) use ($accessibleDesaIds) {
                $query->whereIn('desa_id', $accessibleDesaIds);
            })
                ->with(['getRw.getDesa', 'getWarga'])
                ->withCount('getWarga')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Statistics by desa
            $desaStats = Desa::whereIn('id', $accessibleDesaIds)
                ->with(['getRw', 'getKK', 'getWarga'])
                ->withCount(['getRw', 'getKK', 'getWarga'])
                ->get()
                ->map(function ($desa) {
                    return [
                        'id' => $desa->id,
                        'nama_desa' => $desa->nama_desa,
                        'total_rw' => $desa->get_rw_count,
                        'total_kk' => $desa->get_kk_count,
                        'total_warga' => $desa->get_warga_count,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'statistics' => $stats,
                    'recent_data' => [
                        'desas' => $recentDesas,
                        'rws' => $recentRws,
                        'kks' => $recentKks
                    ],
                    'desa_statistics' => $desaStats,
                    'user_info' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'roles' => $user->getRoleNames(),
                        'accessible_desas_count' => count($accessibleDesaIds)
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
