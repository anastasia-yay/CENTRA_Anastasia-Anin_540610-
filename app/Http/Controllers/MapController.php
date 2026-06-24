<?php

namespace App\Http\Controllers;

use App\Models\DisasterEvent;
use App\Models\DisasterType;
use App\Models\Region;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MapController extends Controller
{
    /**
     * GET /peta
     * Pintu masuk lama — diarahkan ke halaman Choropleth sebagai default.
     */
    public function index(): RedirectResponse
    {
        // return view('map.index', [
        //     'disasterTypes' => DisasterType::orderBy('nama_bencana')->get(),
        // ]);

        return redirect()->route('map.choropleth');
    }

    /**
     * GET /peta/choropleth
     * Halaman khusus visualisasi Choropleth (1 layer, 1 fokus export).
     */
    public function choroplethPage(): View
    {
        $disasterTypes = DisasterType::orderBy('nama_bencana')->get();

        return view('map.choropleth', [
            'disasterTypes' => $disasterTypes,
        ]);
    }

    /**
     * GET /peta/heatmap
     * Halaman khusus visualisasi Heatmap (1 layer, 1 fokus export).
     */
    public function heatmapPage(): View
    {
        $disasterTypes = DisasterType::orderBy('nama_bencana')->get();

        return view('map.heatmap', [
            'disasterTypes' => $disasterTypes,
        ]);
    }

    /**
     * Layer Choropleth — GeoJSON polygon wilayah + total kejadian.
     *
     * GET /api/map/choropleth
     * GET /api/map/choropleth?jenis=3
     */
    public function choropleth(Request $request)
    {
        // $jenis = $request->integer('jenis');
        //
        // return response()->json(
        //     Region::geojsonChoropleth($jenis ?: null)
        // );

        $jenis = $request->filled('jenis')
            ? (int) $request->input('jenis')
            : null;

        return response()->json(
            Region::geojsonChoropleth($jenis)
        );
    }

    /**
     * Layer Heatmap — GeoJSON titik centroid wilayah + bobot kejadian.
     *
     * GET /api/map/centroid
     * GET /api/map/centroid?jenis=3
     */
    public function centroid(Request $request)
    {
        // $jenis = $request->integer('jenis');
        //
        // return response()->json(
        //     Region::geojsonCentroid($jenis ?: null)
        // );

        $jenis = $request->filled('jenis')
            ? (int) $request->input('jenis')
            : null;

        return response()->json(
            Region::geojsonCentroid($jenis)
        );
    }

    /**
     * Layer Titik Bencana — GeoJSON titik aktual setiap kejadian bencana.
     * (nonaktif, dibiarkan sebagai referensi)
     */
    // public function actualPoints(Request $request): JsonResponse
    // {
    //     $tahun = $request->integer('tahun');
    //     $jenis = $request->integer('jenis');
    //
    //     $data = DisasterEvent::geojsonActualPoints(
    //         $tahun ?: null,
    //         $jenis ?: null
    //     );
    //
    //     return response()->json($data);
    // }
}
