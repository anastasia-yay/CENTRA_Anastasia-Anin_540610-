<?php

namespace App\Http\Controllers;

use App\Models\DisasterEvent;
use App\Models\DisasterType;
use App\Models\Region;
use App\Models\RiskLevel;
use App\Imports\DisasterEventsImport;
use App\Exports\DisasterEventsExport;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DisasterEventController extends Controller
{
    /**
     * GET /kejadian
     * Daftar semua kejadian + filter DataTables.
     */
    public function index(): View
    {
        $events = DisasterEvent::with([
            'region',
            'disasterTypes',
            'riskLevel'
        ])->latest('tanggal_kejadian')->get();

        $disasterTypes = DisasterType::orderBy('nama_bencana')->get();
        $region       = Region::orderBy('nama_wilayah')->get();
        $riskLevel    = RiskLevel::orderBy('nama_tingkat')->get();

        return view('disaster-events.index', compact(
            'events',
            'disasterTypes',
            'region',
            'riskLevel'
        ));
    }

    /**
     * GET /kejadian/create
     */
    public function create(): View
    {
        $disasterTypes = DisasterType::orderBy('nama_bencana')->get();
        $region       = Region::orderBy('nama_wilayah')->get();
        $riskLevel    = RiskLevel::orderBy('nama_tingkat')->get();

        return view('disaster-events.create', compact(
            'disasterTypes',
            'region',
            'riskLevel',
        ));
    }

    /**
     * POST /kejadian
     * Simpan kejadian baru; koordinat diambil dari centroid wilayah.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'disaster_type_id' => ['required', 'exists:disaster_types,id'],
            'region_id'        => ['required', 'exists:regions,id'],
            'jumlah_kejadian'  => ['required', 'integer', 'min:0'],
            'jumlah_korban'    => ['required', 'integer', 'min:0'],
            'tanggal_kejadian' => ['required', 'date'],
            'status'           => ['nullable', 'string', 'max:50'],
            'keterangan'       => ['nullable', 'string'],
        ]);

        // Ambil centroid wilayah secara otomatis dari tabel regions
        $region = Region::findOrFail($validated['region_id']);

        DB::transaction(function () use ($validated, $region) {

            $validated['status'] = $validated['status'] ?? 'ACC';

            $event = DisasterEvent::create($validated);

            DB::statement(
                "UPDATE disaster_events
                SET geom_actual = ST_Centroid(
                    (SELECT geom_centroid FROM regions WHERE id = ?)
                )
                WHERE id = ?",
                [$region->id, $event->id]
            );
        });

        return redirect()
            ->route('disaster-events.index')
            ->with('success', 'Kejadian bencana berhasil ditambahkan.');
    }

    /**
     * GET /kejadian/{event}
     */
    public function show(DisasterEvent $kejadian)
    {
        $event = $kejadian;

        return view('disaster-events.show', compact('event'));
    }

    /**
     * GET /kejadian/{event}/edit
     */
    public function edit(DisasterEvent $kejadian): View
    {
        $event = $kejadian;
        $disasterTypes = DisasterType::orderBy('nama_bencana')->get();
        $region       = Region::orderBy('nama_wilayah')->get();
        $riskLevel    = RiskLevel::orderBy('nama_tingkat')->get();

        $event->load(['region', 'disasterTypes', 'riskLevel']);

        return view('disaster-events.edit', compact(
            'event',
            'disasterTypes',
            'region',
            'riskLevel',
        ));
    }

    /**
     * PATCH /kejadian/{event}
     */
    public function update(Request $request, DisasterEvent $kejadian): RedirectResponse
    {
        $event = $kejadian;
        $validated = $request->validate([
            'disaster_type_id' => ['required', 'exists:disaster_types,id'],
            'region_id'        => ['required', 'exists:regions,id'],
            'jumlah_kejadian'  => ['required', 'integer', 'min:0'],
            'jumlah_korban'    => ['required', 'integer', 'min:0'],
            'tanggal_kejadian' => ['required', 'date'],
            'status'           => ['nullable', 'string', 'max:50'],
            'keterangan'       => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($validated, $event) {
            $validated['status'] = $validated['status'] ?? 'ACC';
            $event->update($validated);
            // Perbarui geom_actual jika region berubah
            DB::statement(
                "UPDATE disaster_events
                 SET geom_actual = ST_Centroid(
                     (SELECT geom_centroid FROM regions WHERE id = ?)
                 )
                 WHERE id = ?",
                [$validated['region_id'], $event->id]
            );
        });

        return redirect()
            ->route('disaster-events.index')
            ->with('success', 'Kejadian bencana berhasil diperbarui.');
    }

    /**
     * DELETE /kejadian/{event}
     */
    public function destroy(DisasterEvent $kejadian): RedirectResponse
    {
        $kejadian->delete();

        return redirect()
            ->route('disaster-events.index')
            ->with('success', 'Kejadian bencana berhasil dihapus.');
    }

    // ──────────────────────────────────────────────────────────────
    // Import / Export
    // ──────────────────────────────────────────────────────────────

    /**
     * GET /kejadian/import/form
     */
    public function importForm(): View
    {
        return view('disaster-events.import');
    }

    /**
     * POST /kejadian/import
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        Excel::import(new DisasterEventsImport, $request->file('file'));

        return redirect()
            ->route('disaster-events.index')
            ->with('success', 'Import data berhasil.');
    }

    /**
     * GET /kejadian/export/excel
     */
    public function exportExcel()
    {
        return Excel::download(new DisasterEventsExport, 'kejadian-bencana.xlsx');
    }

    /**
     * GET /kejadian/export/pdf
     * (Gunakan barryvdh/laravel-dompdf atau snappy)
     */
    public function exportPdf()
    {
        $events = DisasterEvent::with(['region', 'disasterType', 'riskLevel'])
            ->latest('tanggal_kejadian')
            ->get();

        $pdf = app('dompdf.wrapper')
            ->loadView('disaster-events.pdf', compact('events'));

        return $pdf->download('kejadian-bencana.pdf');
    }
}
