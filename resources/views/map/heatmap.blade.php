@extends('layouts.app')

@section('page-title', 'Peta Heatmap')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Peta Heatmap</li>
@endsection

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
:root {
    --cream     : #faf7f2;
    --cream2    : #f3ede4;
    --brown-dark: #2c1a0e;
    --brown-mid : #5a3a22;
    --earth     : #c4501a;
    --earth-lt  : #e8734a;
    --river     : #1a5276;
    --amber     : #b7770d;
    --border    : #d9cfc3;
    --text-main : #2c1a0e;
    --text-muted: #7a6552;
    --navy      : #112240;
}

body, .content-wrapper, #main-content {
    font-family: 'Source Sans 3', sans-serif;
}

#main-content {
    padding: 0 !important;
    display: flex;
    flex-direction: column;
    height: calc(100vh - var(--topbar-h));
    overflow: hidden;
}

/* ── Tab navigasi ───────────────────────────────── */
.map-tabs {
    display: flex;
    gap: .4rem;
    padding: .6rem 1.25rem 0;
    background: var(--cream);
    border-bottom: 1px solid var(--border);
}
.map-tab {
    font-size: .8rem;
    font-weight: 700;
    padding: .5rem 1.1rem;
    border-radius: 8px 8px 0 0;
    color: var(--text-muted);
    text-decoration: none;
    border: 1px solid transparent;
    display: flex; align-items: center; gap: .4rem;
    letter-spacing: .02em;
    transition: color .15s;
}
.map-tab.active {
    background: #fff;
    color: var(--brown-dark);
    border-color: var(--border);
    border-bottom-color: #fff;
}
.map-tab:hover:not(.active) { color: var(--earth); }

/* ── Filter bar ─────────────────────────────────── */
#filter-bar {
    display: flex;
    align-items: flex-end;
    gap: .75rem;
    padding: .75rem 1.25rem;
    background: #fff;
    border-bottom: 1px solid var(--border);
    flex-wrap: wrap;
    z-index: 900;
    box-shadow: 0 1px 6px rgba(44,26,14,.06);
}
#filter-bar label {
    font-size: .68rem;
    font-weight: 700;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .08em;
    margin-bottom: .3rem;
    display: block;
}
#filter-bar select {
    font-size: .86rem;
    min-width: 170px;
    border: 1px solid var(--border);
    border-radius: 6px;
    background: var(--cream);
    color: var(--text-main);
    font-family: 'Source Sans 3', sans-serif;
    padding: .4rem .65rem;
    transition: border-color .15s, box-shadow .15s;
}
#filter-bar select:focus {
    outline: none;
    border-color: var(--earth);
    box-shadow: 0 0 0 3px rgba(196,80,26,.12);
}

.btn-filter {
    background: var(--earth);
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: .45rem 1.1rem;
    font-size: .82rem;
    font-weight: 700;
    font-family: 'Source Sans 3', sans-serif;
    display: flex; align-items: center; gap: .4rem;
    cursor: pointer;
    transition: background .15s;
}
.btn-filter:hover { background: var(--earth-lt, #e8734a); }

.btn-export {
    background: var(--navy);
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: .45rem 1.1rem;
    font-size: .82rem;
    font-weight: 700;
    font-family: 'Source Sans 3', sans-serif;
    display: flex; align-items: center; gap: .4rem;
    cursor: pointer;
    transition: background .15s;
    margin-left: auto;
}
.btn-export:hover { background: #1d3a6e; }
.btn-export:disabled { opacity: .6; cursor: not-allowed; }

/* ── Map export wrapper ─────────────────────────── */
.map-export-wrapper {
    position: relative;
    flex: 1;
    display: flex;
    flex-direction: column;
}
#map { flex: 1; width: 100%; }

.map-title-overlay {
    position: absolute;
    top: 12px; left: 12px;
    z-index: 950;
    background: rgba(250,247,242,.95);
    padding: .6rem 1.1rem;
    border-radius: 8px;
    border: 1px solid var(--border);
    font-family: 'Playfair Display', serif;
    font-weight: 700;
    font-size: .95rem;
    color: var(--brown-dark);
    box-shadow: 0 2px 10px rgba(44,26,14,.12);
    max-width: 70%;
}
.map-title-overlay small {
    display: block;
    font-family: 'Source Sans 3', sans-serif;
    font-weight: 600;
    font-size: .7rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: .07em;
    margin-top: .15rem;
}

.map-export-wrapper.exporting .leaflet-control-zoom,
.map-export-wrapper.exporting .leaflet-control-layers,
.map-export-wrapper.exporting .leaflet-control-scale,
.map-export-wrapper.exporting .leaflet-control-attribution {
    display: none !important;
}

/* ── Spinner ────────────────────────────────────── */
#map-spinner {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1000;
    background: rgba(250,247,242,.9);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 1.1rem 1.6rem;
    display: none;
    align-items: center; gap: .7rem;
    font-weight: 600; font-size: .85rem;
    color: var(--brown-dark);
    box-shadow: 0 4px 20px rgba(44,26,14,.12);
}
#map-spinner.show { display: flex; }

/* ── Legend ─────────────────────────────────────── */
.leaflet-legend {
    background: rgba(250,247,242,.96);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: .75rem 1rem;
    font-size: .78rem;
    font-family: 'Source Sans 3', sans-serif;
    box-shadow: 0 2px 10px rgba(44,26,14,.1);
    line-height: 1.7;
    min-width: 165px;
}
.leaflet-legend h6 {
    font-family: 'Playfair Display', serif;
    font-size: .8rem;
    font-weight: 700;
    color: var(--brown-dark);
    margin-bottom: .5rem;
    padding-bottom: .3rem;
    border-bottom: 1px solid var(--border);
}
.legend-swatch {
    display: inline-block;
    width: 13px; height: 13px;
    border-radius: 3px;
    margin-right: 6px;
    vertical-align: middle;
}
</style>
@endpush

@section('content')

{{-- ── Tab navigasi ─────────────────────────────── --}}
<div class="map-tabs">
    <a href="{{ route('map.choropleth') }}" class="map-tab">
        <i class="bi bi-layers"></i> Choropleth
    </a>
    <a href="{{ route('map.heatmap') }}" class="map-tab active">
        <i class="bi bi-fire"></i> Heatmap
    </a>
</div>

{{-- ── Filter bar ──────────────────────────────────── --}}
<div id="filter-bar">
    <div>
        <label>Jenis Bencana</label>
        <select id="f-jenis">
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
        <i class="bi bi-download"></i> Export PNG
    </button>
</div>

{{-- ── Map ─────────────────────────────────────────── --}}
<div class="map-export-wrapper" id="mapExportArea">

    <div id="exportTitle" class="map-title-overlay">
        Heatmap Kepadatan Kejadian Bencana
        <small id="exportSubtitle">Semua Jenis Bencana</small>
    </div>

    <div id="map"></div>

    <div id="map-spinner">
        <div class="spinner-border spinner-border-sm" style="color:var(--earth)"></div>
        Memuat data…
    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
const map = L.map('map', { zoomControl: false }).setView([-7.15, 110.15], 8);
L.control.zoom({ position: 'bottomleft' }).addTo(map);

const basemaps = {
    'OpenStreetMap': L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19, crossOrigin: true,
    }),
    'Google Satellite': L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        attribution: '© Google', maxZoom: 20, crossOrigin: true,
    }),
    'Google Hybrid': L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        attribution: '© Google', maxZoom: 20, crossOrigin: true,
    }),
    'CartoDB Light': L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '© CartoDB', maxZoom: 19, crossOrigin: true,
    }),
};
basemaps['OpenStreetMap'].addTo(map);
L.control.layers(basemaps, {}, { position: 'topright', collapsed: true }).addTo(map);
L.control.scale({ imperial: false, position: 'bottomleft' }).addTo(map);

let heatLayer         = null;
let currentHeatBreaks = [];

let loadingCount = 0;
function showSpinner() { loadingCount++; document.getElementById('map-spinner').classList.add('show'); }
function hideSpinner() { if (--loadingCount <= 0) { loadingCount = 0; document.getElementById('map-spinner').classList.remove('show'); } }

function getHeatIntensity(value, breaks) {
    if (value <= 0)         return 0;
    if (value <= breaks[1]) return 0.33;
    if (value <= breaks[2]) return 0.66;
    return 1.00;
}

const heatLegend = L.control({ position: 'bottomright' });
heatLegend.onAdd = function () {
    this._div = L.DomUtil.create('div', 'leaflet-legend');
    return this._div;
};
heatLegend.addTo(map);

function updateHeatLegend(breaks) {
    if (!heatLegend._div) return;
    const colors = ['#FEB24C', '#F03B20', '#BD0026'];
    const labels = ['Rendah', 'Sedang', 'Tinggi'];
    let html = '<h6>Heatmap</h6>';
    for (let i = 0; i < 3; i++) {
        html += `<span class="legend-swatch" style="background:${colors[i]}"></span>
                 <strong>${labels[i]}</strong>
                 <span style="color:var(--text-muted)">
                     ${Math.round(breaks[i])}&ndash;${Math.round(breaks[i+1])}
                 </span><br>`;
    }
    heatLegend._div.innerHTML = html;
}

function loadHeatmap() {
    const jenis = document.getElementById('f-jenis').value;
    showSpinner();

    fetch(`/api/map/centroid?jenis=${jenis}`)
        .then(r => r.json())
        .then(data => {
            if (heatLayer) map.removeLayer(heatLayer);

            const values   = data.features.map(f => Number(f.properties.total_kejadian || 0));
            const positive = values.filter(v => v > 0);

            if (positive.length === 0) { hideSpinner(); return; }

            const minVal = Math.min(...positive);
            const maxVal = Math.max(...positive);
            const step   = (maxVal - minVal) / 3 || 1;

            const breaks = [minVal, minVal+step, minVal+step*2, maxVal];
            currentHeatBreaks = breaks;
            updateHeatLegend(breaks);

            const pts = data.features
                .filter(f => Number(f.properties.total_kejadian || 0) > 0)
                .map(f => [
                    f.geometry.coordinates[1],
                    f.geometry.coordinates[0],
                    getHeatIntensity(Number(f.properties.total_kejadian), breaks)
                ]);

            heatLayer = L.heatLayer(pts, {
                radius: 45, blur: 30, maxZoom: 14, max: 1, minOpacity: 0.35,
                gradient: { 0.33: '#FEB24C', 0.66: '#F03B20', 1.00: '#BD0026' }
            });
            heatLayer.addTo(map);
        })
        .catch(e => console.error('Heatmap error:', e))
        .finally(hideSpinner);
}

function updateExportTitle() {
    const select = document.getElementById('f-jenis');
    document.getElementById('exportSubtitle').innerText = select.options[select.selectedIndex].text;
}

document.getElementById('btnFilter').addEventListener('click', () => { loadHeatmap(); updateExportTitle(); });

let originalBasemapKey = null;
function switchToExportBasemap() {
    originalBasemapKey = Object.keys(basemaps).find(k => map.hasLayer(basemaps[k]));
    if (originalBasemapKey !== 'OpenStreetMap') {
        map.removeLayer(basemaps[originalBasemapKey]);
        basemaps['OpenStreetMap'].addTo(map);
    } else { originalBasemapKey = null; }
}
function restoreBasemap() {
    if (originalBasemapKey) {
        map.removeLayer(basemaps['OpenStreetMap']);
        basemaps[originalBasemapKey].addTo(map);
        originalBasemapKey = null;
    }
}

document.getElementById('btnExportMap').addEventListener('click', function () {
    const btn = this;
    const wrapper = document.getElementById('mapExportArea');
    btn.disabled  = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Mengekspor...';
    wrapper.classList.add('exporting');
    switchToExportBasemap();
    setTimeout(() => {
        html2canvas(wrapper, { useCORS: true, scale: 2, backgroundColor: '#ffffff' })
            .then(canvas => {
                const link    = document.createElement('a');
                link.download = 'heatmap-kejadian-bencana.png';
                link.href     = canvas.toDataURL('image/png');
                link.click();
            })
            .catch(e => console.error('Export error:', e))
            .finally(() => {
                wrapper.classList.remove('exporting');
                restoreBasemap();
                btn.disabled  = false;
                btn.innerHTML = '<i class="bi bi-download"></i> Export PNG';
            });
    }, 300);
});

loadHeatmap();
updateExportTitle();
</script>
@endpush
