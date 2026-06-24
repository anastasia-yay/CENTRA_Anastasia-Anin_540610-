@extends('layouts.app')

@section('page-title', 'Peta Choropleth')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Peta Choropleth</li>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #main-content {
        padding: 0 !important;
        display: flex;
        flex-direction: column;
        height: calc(100vh - var(--topbar-h));
        overflow: hidden;
    }

    /* ── Tab navigasi Choropleth / Heatmap ─────────── */
    .map-tabs {
        display: flex;
        gap: .4rem;
        padding: .6rem 1.25rem 0;
        background: #fff;
    }
    .map-tab {
        font-size: .8rem;
        font-weight: 600;
        padding: .5rem 1rem;
        border-radius: 8px 8px 0 0;
        color: #64748b;
        text-decoration: none;
        border: 1px solid transparent;
        display: flex; align-items: center; gap: .4rem;
    }
    .map-tab.active {
        background: #f8fafc;
        color: #112240;
        border-color: #e2e8f0;
        border-bottom-color: #f8fafc;
    }
    .map-tab:hover:not(.active) { color: #112240; }

    /* ── Filter bar ─────────────────────────────── */
    #filter-bar {
        display: flex;
        align-items: flex-end;
        gap: .75rem;
        padding: .75rem 1.25rem;
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
        flex-wrap: wrap;
        z-index: 900;
        box-shadow: 0 1px 4px rgba(0,0,0,.05);
    }
    #filter-bar label {
        font-size: .75rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: .25rem;
        display: block;
    }
    #filter-bar select {
        font-size: .875rem;
        min-width: 160px;
        border-color: #cbd5e1;
        border-radius: 6px;
    }
    #filter-bar select:focus { border-color: var(--accent, #e84c1e); box-shadow: 0 0 0 3px rgba(232,76,30,.1); }
    .btn-filter {
        background: var(--accent, #e84c1e);
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: .45rem 1.1rem;
        font-size: .875rem;
        font-weight: 600;
        display: flex; align-items: center; gap: .4rem;
        cursor: pointer;
        transition: background .15s;
    }
    .btn-filter:hover { background: var(--accent-lt, #ff6b3d); }

    .btn-export {
        background: #112240;
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: .45rem 1.1rem;
        font-size: .875rem;
        font-weight: 600;
        display: flex; align-items: center; gap: .4rem;
        cursor: pointer;
        transition: background .15s;
        margin-left: auto;
    }
    .btn-export:hover { background: #1d3a6e; }
    .btn-export:disabled { opacity: .65; cursor: not-allowed; }

    /* ── Map export wrapper ────────────────────────── */
    .map-export-wrapper {
        position: relative;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    #map { flex: 1; width: 100%; }

    .map-title-overlay {
        position: absolute;
        top: 12px;
        left: 12px;
        z-index: 950;
        background: rgba(255,255,255,.92);
        padding: .6rem 1.1rem;
        border-radius: 8px;
        font-family: 'Space Grotesk', sans-serif;
        font-weight: 700;
        font-size: 1rem;
        color: #112240;
        box-shadow: 0 2px 10px rgba(0,0,0,.12);
        max-width: 70%;
    }
    .map-title-overlay small {
        display: block;
        font-family: 'Inter', sans-serif;
        font-weight: 500;
        font-size: .72rem;
        color: #64748b;
        margin-top: .15rem;
    }

    /* Sembunyikan kontrol Leaflet yang tidak perlu saat proses export */
    .map-export-wrapper.exporting .leaflet-control-zoom,
    .map-export-wrapper.exporting .leaflet-control-layers,
    .map-export-wrapper.exporting .leaflet-control-scale,
    .map-export-wrapper.exporting .leaflet-control-attribution {
        display: none !important;
    }

    /* ── Spinner overlay ────────────────────────── */
    #map-spinner {
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1000;
        background: rgba(255,255,255,.85);
        border-radius: 12px;
        padding: 1.2rem 1.8rem;
        display: none;
        align-items: center; gap: .75rem;
        font-weight: 600; font-size: .875rem;
        box-shadow: 0 4px 20px rgba(0,0,0,.12);
    }
    #map-spinner.show { display: flex; }

    /* ── Leaflet popup custom ───────────────────── */
    .leaflet-popup-content-wrapper {
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,.15);
        padding: 0;
        overflow: hidden;
    }
    .leaflet-popup-content { margin: 0; min-width: 200px; }
    .popup-header {
        background: var(--brand-800, #112240);
        color: #fff;
        padding: .6rem .9rem;
        font-family: 'Space Grotesk', sans-serif;
        font-weight: 700;
        font-size: .9rem;
    }
    .popup-body { padding: .6rem .9rem; font-size: .82rem; }
    .popup-row { display: flex; justify-content: space-between; gap: .5rem; margin-bottom: .3rem; }
    .popup-label { color: #64748b; }
    .popup-val { font-weight: 600; }

    /* ── Legend ─────────────────────────────────── */
    .leaflet-legend {
        background: #fff;
        border-radius: 8px;
        padding: .75rem 1rem;
        font-size: .78rem;
        box-shadow: 0 2px 10px rgba(0,0,0,.12);
        line-height: 1.6;
        min-width: 160px;
    }
    .leaflet-legend h6 {
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #334155;
        margin-bottom: .5rem;
    }
    .legend-swatch {
        display: inline-block;
        width: 14px; height: 14px;
        border-radius: 3px;
        margin-right: 6px;
        vertical-align: middle;
    }
</style>
@endpush

@section('content')

{{-- ── Tab navigasi ─────────────────────────────── --}}
<div class="map-tabs">
    <a href="{{ route('map.choropleth') }}" class="map-tab active">
        <i class="bi bi-layers"></i> Choropleth
    </a>
    <a href="{{ route('map.heatmap') }}" class="map-tab">
        <i class="bi bi-fire"></i> Heatmap
    </a>
</div>

{{-- ── Filter bar ──────────────────────────────────── --}}
<div id="filter-bar">
    <div>
        <label>Jenis Bencana</label>
        <select id="f-jenis" class="form-select form-select-sm">
            <option value="">Semua Jenis</option>
            @foreach($disasterTypes as $type)
                <option value="{{ $type->id }}">{{ $type->nama_bencana }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label>&nbsp;</label>
        <button class="btn-filter" id="btnFilter">
            <i class="bi bi-funnel-fill"></i> Terapkan Filter
        </button>
    </div>

    <button class="btn-export" id="btnExportMap">
        <i class="bi bi-download"></i> Export Map (PNG)
    </button>
</div>

{{-- ── Map container + export wrapper ─────────────── --}}
<div class="map-export-wrapper" id="mapExportArea">

    {{-- Judul overlay, otomatis berubah sesuai filter aktif --}}
    <div id="exportTitle" class="map-title-overlay">
        Choropleth Total Kejadian Bencana
        <small id="exportSubtitle">Semua Jenis Bencana</small>
    </div>

    <div id="map"></div>

    <div id="map-spinner">
        <div class="spinner-border spinner-border-sm text-danger"></div>
        Memuat data…
    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
/* ════════════════════════════════════════════════════
   WEBGIS KEBENCANAAN — Halaman Choropleth
════════════════════════════════════════════════════ */

// ── 1. Inisialisasi peta ──────────────────────────
// preferCanvas: true memaksa Leaflet merender vector layer (polygon
// choropleth) ke <canvas> raster, bukan <svg> dengan CSS transform
// terpisah dari tile-pane. Inilah akar masalah "polygon bergeser ke
// barat laut" saat di-screenshot html2canvas — SVG overlay-pane punya
// transform sendiri yang sering salah dihitung ulang oleh html2canvas,
// sedangkan heatmap (leaflet.heat) sejak awal sudah berupa <canvas>
// tunggal sehingga selalu presisi. Dengan preferCanvas, choropleth
// kini memakai mekanisme render yang sama seperti heatmap.
const map = L.map('map', { zoomControl: false, preferCanvas: true }).setView([-7.15, 110.15], 8);
L.control.zoom({ position: 'bottomleft' }).addTo(map);

// ── 2. Basemaps ───────────────────────────────────
const basemaps = {
    'OpenStreetMap': L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
        crossOrigin: true,
    }),
    'Google Satellite': L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        attribution: '© Google',
        maxZoom: 20,
        crossOrigin: true,
    }),
    'Google Hybrid': L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        attribution: '© Google',
        maxZoom: 20,
        crossOrigin: true,
    }),
    'CartoDB Light': L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '© CartoDB',
        maxZoom: 19,
        crossOrigin: true,
    }),
};
basemaps['OpenStreetMap'].addTo(map);

L.control.layers(basemaps, {}, { position: 'topright', collapsed: true }).addTo(map);
L.control.scale({ imperial: false, position: 'bottomleft' }).addTo(map);

// ── 3. State layer ────────────────────────────────
let choroplethLayer = null;
let currentBreaks   = [];

// ── 4. Spinner helper ─────────────────────────────
let loadingCount = 0;
function showSpinner() {
    loadingCount++;
    document.getElementById('map-spinner').classList.add('show');
}
function hideSpinner() {
    if (--loadingCount <= 0) {
        loadingCount = 0;
        document.getElementById('map-spinner').classList.remove('show');
    }
}

// ── 5. Warna choropleth (Equal Interval Dinamis) ──
// Klasifikasi: 1 kelas abu-abu (nilai 0) + 5 kelas Equal Interval (nilai > 0)
// breaks = [0, minPositive, b2, b3, b4, b5, maxPositive] → 7 elemen
function getColor(value, breaks)
{
    if (value === 0)
        return '#808080';

    return value >= breaks[5] ? '#800026' :
           value >= breaks[4] ? '#BD0026' :
           value >= breaks[3] ? '#E31A1C' :
           value >= breaks[2] ? '#FC4E2A' :
                                 '#FD8D3C';
}

// ── 6. Legend Choropleth (bottomright) ────────────
const choroplethLegend = L.control({ position: 'bottomright' });

choroplethLegend.onAdd = function ()
{
    this._div = L.DomUtil.create('div', 'leaflet-legend');
    return this._div;
};

choroplethLegend.addTo(map);

function updateChoroplethLegend(breaks)
{
    if (!choroplethLegend._div) return;

    let html = '<h6>Choropleth</h6>';

    html += `
        <span class="legend-swatch" style="background:#808080"></span>
        0 (Tidak Ada Kejadian)
        <br>
    `;

    const colors = ['#FD8D3C', '#FC4E2A', '#E31A1C', '#BD0026', '#800026'];

    for (let i = 1; i <= 5; i++)
    {
        html += `
            <span class="legend-swatch" style="background:${colors[i - 1]}"></span>
            ${Math.round(breaks[i])}
            &ndash;
            ${Math.round(breaks[i + 1])}
            <br>
        `;
    }

    choroplethLegend._div.innerHTML = html;
}

// ── 7. Load Choropleth ────────────────────────────
function loadChoropleth() {
    const jenis = document.getElementById('f-jenis').value;
    showSpinner();

    fetch(`/api/map/choropleth?jenis=${jenis}`)
        .then(r => r.json())
        .then(data => {
            if (choroplethLayer) map.removeLayer(choroplethLayer);

            const values         = data.features.map(f => Number(f.properties.total_kejadian || 0));
            const positiveValues = values.filter(v => v > 0);

            if (positiveValues.length === 0)
            {
                currentBreaks = [0, 0, 0, 0, 0, 0, 0];
            }
            else
            {
                const minPositive = Math.min(...positiveValues);
                const maxPositive = Math.max(...positiveValues);
                const interval    = (maxPositive - minPositive) / 5 || 1;

                currentBreaks = [
                    0,
                    minPositive,
                    minPositive + interval,
                    minPositive + interval * 2,
                    minPositive + interval * 3,
                    minPositive + interval * 4,
                    maxPositive
                ];
            }

            updateChoroplethLegend(currentBreaks);

            choroplethLayer = L.geoJSON(data, {
                // renderer: L.canvas() — jaminan ganda selain preferCanvas
                // pada inisialisasi map, memastikan layer ini pasti
                // digambar ke <canvas>, bukan <svg>. Inilah perbaikan
                // utama untuk masalah "polygon bergeser saat export PNG".
                renderer: L.canvas(),

                style: f => ({
                    fillColor  : getColor(f.properties.total_kejadian ?? 0, currentBreaks),
                    weight     : 1,
                    opacity    : 1,
                    color      : '#FFFFFF',
                    fillOpacity: 0.8,
                    dashArray  : (f.properties.total_kejadian ?? 0) === 0 ? '3' : null
                }),
                onEachFeature: (f, layer) => {
                    const p = f.properties;
                    layer.bindPopup(`
                        <div class="popup-header">${p.nama_wilayah ?? '—'}</div>
                        <div class="popup-body">
                            <div class="popup-row">
                                <span class="popup-label">Total Kejadian</span>
                                <span class="popup-val">${p.total_kejadian ?? 0}</span>
                            </div>
                            <div class="popup-row">
                                <span class="popup-label">Jenis Wilayah</span>
                                <span class="popup-val">${p.jenis_wilayah ?? '—'}</span>
                            </div>
                        </div>`);
                    // Catatan: efek hover (mouseover/mouseout) di bawah ini
                    // tetap berfungsi normal pada Canvas renderer — Leaflet
                    // tetap mendeteksi event mouse pada path canvas via hit
                    // detection internalnya sendiri, bukan event DOM SVG.
                    layer.on('mouseover', () => layer.setStyle({ weight: 3, color: '#334155' }));
                    layer.on('mouseout',  () => choroplethLayer.resetStyle(layer));
                },
            });

            choroplethLayer.addTo(map);

            if (choroplethLayer.getLayers().length > 0) {
                map.fitBounds(choroplethLayer.getBounds());
            }
        })
        .catch(e => console.error('Choropleth error:', e))
        .finally(hideSpinner);
}

// ── 8. Judul overlay dinamis (untuk hasil export) ─
function updateExportTitle() {
    const select   = document.getElementById('f-jenis');
    const jenisTxt = select.options[select.selectedIndex].text;

    document.getElementById('exportSubtitle').innerText = jenisTxt;
}

// ── 9. Filter button ──────────────────────────────
document.getElementById('btnFilter').addEventListener('click', () => {
    loadChoropleth();
    updateExportTitle();
});

// ── 10. Export Map ke PNG ─────────────────────────
// Basemap export tetap dipertahankan apa adanya (OpenStreetMap) karena
// akar masalah pergeseran BUKAN pada basemap/CORS, melainkan pada
// renderer SVG vs Canvas di atas. Tidak perlu lagi switch basemap
// khusus untuk proses export.
document.getElementById('btnExportMap').addEventListener('click', function () {
    const btn     = this;
    const wrapper = document.getElementById('mapExportArea');

    btn.disabled  = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Mengekspor...';

    wrapper.classList.add('exporting');

    // map.invalidateSize() + delay kecil memastikan Leaflet sudah
    // menyelesaikan repaint pane sebelum html2canvas membaca DOM,
    // mencegah snapshot diambil di tengah-tengah animasi/transisi pane.
    map.invalidateSize();

    setTimeout(() => {
        html2canvas(wrapper, {
            useCORS        : true,
            scale          : 2,
            backgroundColor: '#ffffff',
        }).then(canvas => {
            const link    = document.createElement('a');
            link.download = 'choropleth-kejadian-bencana.png';
            link.href     = canvas.toDataURL('image/png');
            link.click();

            wrapper.classList.remove('exporting');
            btn.disabled  = false;
            btn.innerHTML = '<i class="bi bi-download"></i> Export Map (PNG)';
        }).catch(err => {
            console.error('Export error:', err);
            wrapper.classList.remove('exporting');
            btn.disabled  = false;
            btn.innerHTML = '<i class="bi bi-download"></i> Export Map (PNG)';
        });
    }, 300);
});

// ── 11. Initial load ──────────────────────────────
loadChoropleth();
updateExportTitle();
</script>
@endpush
