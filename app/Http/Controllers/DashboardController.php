<?php

namespace App\Http\Controllers;

use App\Models\DisasterEvent;
use App\Models\DisasterType;
use App\Models\Region;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Halaman Dashboard — ringkasan statistik kebencanaan Jawa Tengah.
     * GET /dashboard
     */
    public function index(): View
    {
        // ── Statistik utama (card KPI) ──────────────────────────
        $totalKejadian     = (int) DisasterEvent::sum('jumlah_kejadian');
        $totalJenisBencana = DisasterType::count();
        $totalWilayah      = Region::count();
        $totalKorban       = (int) DisasterEvent::sum('jumlah_korban');

        // ── Wilayah extremes ─────────────────────────────────────
        $wilayahTertinggi = DisasterEvent::select(
                'region_id',
                DB::raw('SUM(jumlah_kejadian) as total')
            )
            ->with('region:id,nama_wilayah,jenis_wilayah')
            ->groupBy('region_id')
            ->orderByDesc('total')
            ->first();

        $wilayahTerendah = DisasterEvent::select(
                'region_id',
                DB::raw('SUM(jumlah_kejadian) as total')
            )
            ->with('region:id,nama_wilayah,jenis_wilayah')
            ->groupBy('region_id')
            ->orderBy('total')
            ->first();

        // ── Top 3 jenis bencana untuk wilayah tertinggi ──────────
        $topDisasterWilayahTertinggi = $wilayahTertinggi
            ? DisasterEvent::select(
                    'disaster_type_id',
                    DB::raw('SUM(jumlah_kejadian) as total')
                )
                ->where('region_id', $wilayahTertinggi->region_id)
                ->with('disasterTypes:id,nama_bencana')
                ->groupBy('disaster_type_id')
                ->orderByDesc('total')
                ->take(3)
                ->get()
            : collect();

        // ── Top 3 jenis bencana untuk wilayah terendah ───────────
        $topDisasterWilayahTerendah = $wilayahTerendah
            ? DisasterEvent::select(
                    'disaster_type_id',
                    DB::raw('SUM(jumlah_kejadian) as total')
                )
                ->where('region_id', $wilayahTerendah->region_id)
                ->with('disasterTypes:id,nama_bencana')
                ->groupBy('disaster_type_id')
                ->orderByDesc('total')
                ->take(3)
                ->get()
            : collect();

        // ── Distribusi per jenis bencana (untuk doughnut chart) ──
        $distribusiJenis = DisasterEvent::select(
                'disaster_type_id',
                DB::raw('SUM(jumlah_kejadian) as total')
            )
            ->with('disasterTypes:id,nama_bencana')
            ->whereNotNull('disaster_type_id')
            ->groupBy('disaster_type_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(fn($r) => [
                'nama'  => $r->disasterTypes->nama_bencana ?? 'Lainnya',
                'total' => (int) $r->total,
            ]);

        // ── Distribusi Risk Level (untuk bar/doughnut chart) ─────
        $riskDistribution = DisasterEvent::select(
                'risk_level_id',
                DB::raw('SUM(jumlah_kejadian) as total')
            )
            ->with('riskLevel:id,nama_level,warna')
            ->whereNotNull('risk_level_id')
            ->groupBy('risk_level_id')
            ->orderByDesc('total')
            ->get()
            ->map(fn($r) => [
                'nama'   => $r->riskLevel->nama_level ?? 'Tidak Diketahui',
                'warna'  => $r->riskLevel->warna      ?? '#94a3b8',
                'total'  => (int) $r->total,
            ]);

        // ── Top 10 wilayah kejadian terbanyak (untuk bar chart) ──
        $topWilayah = DisasterEvent::select(
                'region_id',
                DB::raw('SUM(jumlah_kejadian) as total')
            )
            ->with('region:id,nama_wilayah,jenis_wilayah')
            ->whereNotNull('region_id')
            ->groupBy('region_id')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(fn($r) => [
                'nama'  => $r->region->nama_wilayah ?? '—',
                'total' => (int) $r->total,
            ]);

        // ── Kejadian terbaru (tabel aktivitas) ───────────────────
        $recentEvents = DisasterEvent::with([
                'region:id,nama_wilayah',
                'disasterTypes:id,nama_bencana',
            ])
            ->latest('tanggal_kejadian')
            ->limit(8)
            ->get();

        return view('dashboard', compact(
            'totalKejadian',
            'totalJenisBencana',
            'totalWilayah',
            'totalKorban',
            'wilayahTertinggi',
            'wilayahTerendah',
            'topDisasterWilayahTertinggi',
            'topDisasterWilayahTerendah',
            'distribusiJenis',
            'riskDistribution',
            'topWilayah',
            'recentEvents',
        ));
    }
}
