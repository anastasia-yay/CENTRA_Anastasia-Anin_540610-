@extends('layouts.app')

@section('page-title', 'Peta Interaktif')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Peta</li>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    /* ── Layout: full-height map page ───────────── */
    #main-content {
        padding: 0 !important;
        display: flex;
        flex-direction: column;
        height: calc(100vh - var(--topbar-h));
        overflow: hidden;
    }

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
    #filter-bar select:focus {
        border-color: var(--accent, #e84c1e);
        box-shadow: 0 0 0 3px rgba(232,76,30,.1);
    }
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

    /* ── Layer toggle pills ─────────────────────── */
    .layer-toggles {
        display: flex; gap: .4rem; margin-left: auto;
        flex-wrap: wrap;
    }
    .layer-toggle {
        font-size: .75rem;
        font-weight: 600;
        border-radius: 99px;
        padding: .3rem .9rem;
        cursor: pointer;
        border: 1.5px solid #cbd5e1;
        background: #fff;
        color: #475569;
        transition: all .15s;
        display: flex; align-items: center; gap: .35rem;
    }
    .layer-toggle.active-toggle {
        background: var(--brand-800, #112240);
        color: #fff;
        border-color: var(--brand-800, #112240);
    }
    .layer-toggle i { font-size: .8rem; }

    /* ── Map container ──────────────────────────── */
    #map { flex: 1; width: 100%; }

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
    .popup-body  { padding: .6rem .9rem; font-size: .82rem; }
    .popup-row   { display: flex; justify-content: space-between; gap: .5rem; margin-bottom: .3rem; }
    .popup-label { color: #64748b; }
    .popup-val   { font-weight: 600; }

    /* ── Legend boxes ────────────────────────────── */
    .leaflet-legend {
        background: #fff;
        border-radius: 8px;
        padding: .75rem 1rem;
        font-size: .78rem;
        box-shadow: 0 2px 10px rgba(0,0,0,.12);
        line-height: 1.8;
        min-width: 170px;
    }
    .leaflet-legend h6 {
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: #334155;
        margin-bottom: .45rem;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: .3rem;
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

{{-- Filter bar ──────────────────────────────────── --}}
<div id="filter-bar">
    {{-- Filter jenis bencana --}}
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

    {{-- Layer toggles --}}
    <div class="layer-toggles" id="layerToggles">
        <button class="layer-toggle active-toggle" data-layer="choropleth">
            <i class="bi bi-layers"></i> Choropleth
        </button>
        <button class="layer-toggle active-toggle" data-layer="heatmap">
            <i class="bi bi-fire"></i> Heatmap
        </button>
    </div>
</div>

{{-- Map wrapper ─────────────────────────────────── --}}
<div style="position:relative; flex:1; display:flex; flex-direction:column;">
    <div id="map"></div>
    <div id="map-spinner">
        <div class="spinner-border spinner-border-sm text-danger"></div>
        Memuat data…
    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
<script>
/* ════════════════════════════════════════════════════════════════════
   WEBGIS KEBENCANAAN — Map Page
   Layer: Choropleth (kiri bawah) + Heatmap (kanan bawah)
   Klasifikasi: 1 kelas abu-abu (0 kejadian) + 5 kelas Equal Interval
════════════════════════════════════════════════════════════════════ */

// ── 1. Init peta ──────────────────────────────────
const map = L.map('map', { zoomControl: false }).setView([-7.15, 110.15], 8);
L.control.zoom({ position: 'bottomleft' }).addTo(map);

// ── 2. Basemaps ───────────────────────────────────
const basemaps = {
    'OpenStreetMap' : L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }),
    'Google Satellite': L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        attribution: '© Google', maxZoom: 20,
    }),
    'Google Hybrid'  : L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        attribution: '© Google', maxZoom: 20,
    }),
    'CartoDB Light'  : L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '© CartoDB', maxZoom: 19,
    }),
};
basemaps['OpenStreetMap'].addTo(map);

// ── 3. State ──────────────────────────────────────
let choroplethLayer = null;
let heatLayer       = null;
let currentBreaks   = [0, 0, 0, 0, 0, 0, 0]; // [0, b1…b5, max] — 7 elemen
let heatBreaks      = [0, 0, 0, 0];        // [min, b1, b2, max] — 4 elemen

const layerVisible  = { choropleth: true, heatmap: true };

// ── 4. Spinner ────────────────────────────────────
let loadingCount = 0;
const spinner = document.getElementById('map-spinner');
function showSpinner() { loadingCount++; spinner.classList.add('show'); }
function hideSpinner() { if (--loadingCount <= 0) { loadingCount = 0; spinner.classList.remove('show'); } }

// ═════════════════════════════════════════════════
// CHOROPLETH
// ═════════════════════════════════════════════════

// ── 5a. Klasifikasi Equal Interval choropleth ─────
// Skema: kelas 0 = abu-abu, kelas 1-5 = oranye → maroon
// breaks[7] = [0, minPos, b2, b3, b4, b5, maxPos]
function calcChoroplethBreaks(values)
{
    const positive = values.filter(v => v > 0);

    if (positive.length === 0) {
        return [0, 0, 0, 0, 0, 0, 0];
    }

    const minPos = Math.min(...positive);
    const maxPos = Math.max(...positive);
    const step   = (maxPos - minPos) / 5 || 1;

    return [
        0,
        minPos,
        minPos + step,
        minPos + step * 2,
        minPos + step * 3,
        minPos + step * 4,
        maxPos,
    ];
}

// ── 5b. Warna choropleth ──────────────────────────
// [PERUBAHAN] Fungsi lama getColor(v) dengan threshold statis (>300, >200…)
// diganti dengan getColor(value, breaks) berbasis dinamis Equal Interval.
// Wilayah dengan nilai 0 selalu abu-abu; pakai dashArray:'3' di style.
function getColor(value, breaks)
{
    if (value === 0)     return '#808080';
    if (value >= breaks[5]) return '#800026';
    if (value >= breaks[4]) return '#BD0026';
    if (value >= breaks[3]) return '#E31A1C';
    if (value >= breaks[2]) return '#FC4E2A';
    return '#FD8D3C'; // kelas 1 (>= breaks[1])
}

// ── 5c. Update legenda choropleth ─────────────────
// [PERUBAHAN] Legenda choropleth dibuat TERPISAH di bottomleft,
// diupdate dinamis setiap loadChoropleth() dipanggil.
const choroplethLegend = L.control({ position: 'bottomleft' });
choroplethLegend.onAdd = function () {
    this._div = L.DomUtil.create('div', 'leaflet-legend');
    return this._div;
};
choroplethLegend.addTo(map);

function updateChoroplethLegend(breaks)
{
    if (!choroplethLegend._div) return;

    const colors = ['#FD8D3C', '#FC4E2A', '#E31A1C', '#BD0026', '#800026'];
    let html = '<h6>Choropleth Kejadian</h6>';

    html += `<span class="legend-swatch"
                   style="background:#808080;border:1px solid #CBD5E1"></span>
             0 &nbsp;<em style="color:#94a3b8">(tidak ada)</em><br>`;

    for (let i = 1; i <= 5; i++) {
        html += `<span class="legend-swatch" style="background:${colors[i - 1]}"></span>`
             + `${Math.round(breaks[i])}&ndash;${Math.round(breaks[i + 1])}<br>`;
    }

    choroplethLegend._div.innerHTML = html;
}

// ── 6. Load Choropleth ────────────────────────────
function loadChoropleth()
{
    if (!layerVisible.choropleth) return;

    const jenis = document.getElementById('f-jenis').value;
    showSpinner();

    fetch(`/api/map/choropleth?jenis=${jenis}`)
        .then(r => r.json())
        .then(data => {
            if (choroplethLayer) map.removeLayer(choroplethLayer);

            // Hitung breaks dari seluruh nilai
            const values  = data.features.map(f => Number(f.properties.total_kejadian || 0));
            currentBreaks = calcChoroplethBreaks(values);
            updateChoroplethLegend(currentBreaks);

            choroplethLayer = L.geoJSON(data, {
                // [PERUBAHAN] style pakai getColor(v, breaks) dinamis
                // dan dashArray:'3' untuk wilayah tanpa kejadian
                style: f => ({
                    fillColor  : getColor(f.properties.total_kejadian ?? 0, currentBreaks),
                    weight     : 1,
                    opacity    : 1,
                    color      : '#FFFFFF',
                    fillOpacity: 0.80,
                    dashArray  : (f.properties.total_kejadian ?? 0) === 0 ? '3' : null,
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
                    layer.on('mouseover', () => layer.setStyle({ weight: 2.5, color: '#334155' }));
                    layer.on('mouseout',  () => choroplethLayer.resetStyle(layer));
                },
            });

            if (layerVisible.choropleth) choroplethLayer.addTo(map);

            if (choroplethLayer.getLayers().length > 0) {
                map.fitBounds(choroplethLayer.getBounds());
            }
        })
        .catch(e => console.error('Choropleth error:', e))
        .finally(hideSpinner);
}

// ═════════════════════════════════════════════════
// HEATMAP
// ═════════════════════════════════════════════════

// ── 7a. Klasifikasi Equal Interval heatmap — 3 kelas ─
// breaks[4] = [min, b1, b2, max]
// Kelas 1 (Rendah)  : min  ≤ v < b1
// Kelas 2 (Sedang)  : b1   ≤ v < b2
// Kelas 3 (Tinggi)  : b2   ≤ v ≤ max
function calcHeatBreaks(values)
{
    const positive = values.filter(v => v > 0);
    if (positive.length === 0) return [0, 0, 0, 0];

    const minVal = Math.min(...positive);
    const maxVal = Math.max(...positive);
    const step   = (maxVal - minVal) / 3 || 1;

    return [
        minVal,
        minVal + step,
        minVal + step * 2,
        maxVal,
    ];
}

// ── 7b. Intensitas heatmap (0–1) — 3 kelas ───────
// Kelas 1 Rendah  → 0.33 (kuning)
// Kelas 2 Sedang  → 0.66 (jingga)
// Kelas 3 Tinggi  → 1.00 (merah tua)
function getHeatIntensity(value, breaks)
{
    if (value <= 0)         return 0;
    if (value <= breaks[1]) return 0.33; // Rendah
    if (value <= breaks[2]) return 0.66; // Sedang
    return 1.00;                          // Tinggi
}

// ── 7c. Update legenda heatmap — 3 kelas ─────────
// Posisi: bottomright, terpisah dari choropleth (bottomleft).
// Label: Rendah / Sedang / Tinggi + rentang angka.
const heatLegend = L.control({ position: 'bottomright' });
heatLegend.onAdd = function () {
    this._div = L.DomUtil.create('div', 'leaflet-legend');
    return this._div;
};
heatLegend.addTo(map);

function updateHeatLegend(breaks)
{
    if (!heatLegend._div) return;

    // 3 warna: kuning → jingga tua → merah gelap
    const colors = ['#FEB24C', '#F03B20', '#BD0026'];
    const labels = ['Rendah', 'Sedang', 'Tinggi'];

    let html = '<h6>Heatmap Intensitas</h6>';

    for (let i = 0; i < 3; i++) {
        html += `<span class="legend-swatch" style="background:${colors[i]}"></span>`
             + `<strong>${labels[i]}</strong> `
             + `<span style="color:#94a3b8">`
             + `${Math.round(breaks[i])}&ndash;${Math.round(breaks[i + 1])}`
             + `</span><br>`;
    }

    heatLegend._div.innerHTML = html;
}

// ── 8. Load Heatmap ───────────────────────────────
function loadHeatmap()
{
    if (!layerVisible.heatmap) return;

    const jenis = document.getElementById('f-jenis').value;
    showSpinner();

    fetch(`/api/map/centroid?jenis=${jenis}`)
        .then(r => r.json())
        .then(data => {
            if (heatLayer) map.removeLayer(heatLayer);

            const allValues = data.features.map(f => Number(f.properties.total_kejadian || 0));
            heatBreaks = calcHeatBreaks(allValues);

            if (heatBreaks.every(v => v === 0)) {
                hideSpinner();
                return; // tidak ada data positif
            }

            updateHeatLegend(heatBreaks);

            // Filter titik bernilai 0 di sisi JS
            const pts = data.features
                .filter(f => Number(f.properties.total_kejadian || 0) > 0)
                .map(f => [
                    f.geometry.coordinates[1],
                    f.geometry.coordinates[0],
                    getHeatIntensity(Number(f.properties.total_kejadian), heatBreaks),
                ]);

            // Gradient 3 kelas: kuning → jingga → merah gelap
            // Titik stop diselaraskan dengan intensitas kelas: 0.33 / 0.66 / 1.00
            heatLayer = L.heatLayer(pts, {
                radius    : 45,
                blur      : 30,
                maxZoom   : 14,
                max       : 1,
                minOpacity: 0.40,
                gradient  : {
                    0.33 : '#FEB24C', // Rendah  — kuning jingga
                    0.66 : '#F03B20', // Sedang  — jingga merah
                    1.00 : '#BD0026', // Tinggi  — merah gelap
                },
            });

            if (layerVisible.heatmap) heatLayer.addTo(map);
        })
        .catch(e => console.error('Heatmap error:', e))
        .finally(hideSpinner);
}

// ── 9. Refresh semua layer ────────────────────────
function refreshAll() {
    loadChoropleth();
    loadHeatmap();
}

// ── 10. Filter button ─────────────────────────────
document.getElementById('btnFilter').addEventListener('click', refreshAll);

// ── 11. Layer toggle pills ────────────────────────
document.querySelectorAll('.layer-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
        const key = btn.dataset.layer;
        layerVisible[key] = !layerVisible[key];
        btn.classList.toggle('active-toggle', layerVisible[key]);

        const map_ = { choropleth: choroplethLayer, heatmap: heatLayer };
        const lyr  = map_[key];
        if (lyr) layerVisible[key] ? map.addLayer(lyr) : map.removeLayer(lyr);
    });
});

// ── 12. Basemap control ───────────────────────────
L.control.layers(basemaps, {}, { position: 'topright', collapsed: true }).addTo(map);

// ── 13. Scale ─────────────────────────────────────
L.control.scale({ imperial: false, position: 'bottomleft' }).addTo(map);

// ── 14. Initial load ──────────────────────────────
refreshAll();
</script>
@endpush
