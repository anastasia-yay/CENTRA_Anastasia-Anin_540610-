<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RegionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | CRUD REGION
    |--------------------------------------------------------------------------
    */

    public function index(): View
    {
        $regions = Region::orderBy('nama_wilayah')->paginate(20);

        return view('regions.index', compact('regions'));
    }

    public function create(): View
    {
        return view('regions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_wilayah' => ['nullable','string','max:20'],
            'nama_wilayah' => ['required','string','max:255'],
            'jenis_wilayah'=> ['nullable','string','max:100'],
        ]);

        Region::create($validated);

        return redirect()
            ->route('regions.index')
            ->with('success','Wilayah berhasil ditambahkan.');
    }

    public function edit(Region $wilayah): View
    {
        return view('regions.edit', [
            'region' => $wilayah
        ]);
    }

    public function update(
        Request $request,
        Region $wilayah
    ): RedirectResponse {

        $validated = $request->validate([
            'kode_wilayah' => ['nullable','string','max:20'],
            'nama_wilayah' => ['required','string','max:255'],
            'jenis_wilayah'=> ['nullable','string','max:100'],
        ]);

        $wilayah->update($validated);

        return redirect()
            ->route('regions.index')
            ->with('success','Wilayah berhasil diperbarui.');
    }

    public function destroy(Region $wilayah): RedirectResponse
    {
        $wilayah->delete();

        return redirect()
            ->route('regions.index')
            ->with('success','Wilayah berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | API REGION
    |--------------------------------------------------------------------------
    */

    public function apiList(): JsonResponse
    {
        $regions = DB::table('regions')
            ->select(
                'id',
                'nama_wilayah',
                DB::raw('ST_Y(geom_centroid) as lat'),
                DB::raw('ST_X(geom_centroid) as lng')
            )
            ->orderBy('nama_wilayah')
            ->get();

        return response()->json($regions);
    }

    public function apiShow(Region $region): JsonResponse
    {
        $data = DB::table('regions')
            ->select(
                'id',
                'nama_wilayah',
                'jenis_wilayah',
                DB::raw('ST_Y(geom_centroid) as lat'),
                DB::raw('ST_X(geom_centroid) as lng'),
                DB::raw('ST_AsGeoJSON(geom_polygon) as polygon')
            )
            ->where('id',$region->id)
            ->first();

        return response()->json([
            'id' => $data->id,
            'nama_wilayah' => $data->nama_wilayah,
            'jenis_wilayah' => $data->jenis_wilayah,
            'lat' => (float)$data->lat,
            'lng' => (float)$data->lng,
            'polygon' => json_decode($data->polygon)
        ]);
    }
}
