@extends('layouts.app')

@section('page-title', 'Peta Choropleth')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Peta Choropleth</li>
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

/* ── Leaflet popup ──────────────────────────────── */
.leaflet-popup-content-wrapper {
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(44,26,14,.15);
    padding: 0; overflow: hidden;
    border: 1px solid var(--border);
}
.leaflet-popup-content { margin: 0; min-width: 200px; }
.popup-header {
    background: var(--brown-dark);
    color: #fff;
    padding: .6rem .9rem;
    font-family: 'Playfair Display', serif;
    font-weight: 700;
    font-size: .9rem;
}
.popup-body  { padding: .6rem .9rem; font-size: .82rem; background: #fff; }
.popup-row   { display: flex; justify-content: space-between; gap: .5rem; margin-bottom: .3rem; }
.popup-label { color: var(--text-muted); }
.popup-val   { font-weight: 700; color: var(--brown-dark); }

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
        Choropleth Total Kejadian Bencana
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

let choroplethLayer = null;
let currentBreaks   = [];

let loadingCount = 0;
function showSpinner() { loadingCount++; document.getElementById('map-spinner').classList.add('show'); }
function hideSpinner() { if (--loadingCount <= 0) { loadingCount = 0; document.getElementById('map-spinner').classList.remove('show'); } }

function getColor(value, breaks) {
    if (value === 0)        return '#808080';
    if (value >= breaks[5]) return '#800026';
    if (value >= breaks[4]) return '#BD0026';
    if (value >= breaks[3]) return '#E31A1C';
    if (value >= breaks[2]) return '#FC4E2A';
    return '#FD8D3C';
}

const choroplethLegend = L.control({ position: 'bottomright' });
choroplethLegend.onAdd = function () {
    this._div = L.DomUtil.create('div', 'leaflet-legend');
    return this._div;
};
choroplethLegend.addTo(map);

function updateChoroplethLegend(breaks) {
    if (!choroplethLegend._div) return;
    const colors = ['#FD8D3C', '#FC4E2A', '#E31A1C', '#BD0026', '#800026'];
    let html = '<h6>Choropleth</h6>';
    html += `<span class="legend-swatch" style="background:#808080;border:1px solid #d9cfc3"></span>
             0 <em style="color:#7a6552">(tidak ada)</em><br>`;
    for (let i = 1; i <= 5; i++) {
        html += `<span class="legend-swatch" style="background:${colors[i-1]}"></span>
                 ${Math.round(breaks[i])}&ndash;${Math.round(breaks[i+1])}<br>`;
    }
    choroplethLegend._div.innerHTML = html;
}

function loadChoropleth() {
    const jenis = document.getElementById('f-jenis').value;
    showSpinner();

    fetch(`/api/map/choropleth?jenis=${jenis}`)
        .then(r => r.json())
        .then(data => {
            if (choroplethLayer) map.removeLayer(choroplethLayer);

            const values         = data.features.map(f => Number(f.properties.total_kejadian || 0));
            const positiveValues = values.filter(v => v > 0);

            if (positiveValues.length === 0) {
                currentBreaks = [0,0,0,0,0,0,0];
            } else {
                const minPos = Math.min(...positiveValues);
                const maxPos = Math.max(...positiveValues);
                const step   = (maxPos - minPos) / 5 || 1;
                currentBreaks = [0, minPos, minPos+step, minPos+step*2, minPos+step*3, minPos+step*4, maxPos];
            }

            updateChoroplethLegend(currentBreaks);

            choroplethLayer = L.geoJSON(data, {
                style: f => ({
                    fillColor  : getColor(f.properties.total_kejadian ?? 0, currentBreaks),
                    weight     : 1,
                    opacity    : 1,
                    color      : '#fff',
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
                    layer.on('mouseover', () => layer.setStyle({ weight: 2.5, color: '#5a3a22' }));
                    layer.on('mouseout',  () => choroplethLayer.resetStyle(layer));
                },
            });

            choroplethLayer.addTo(map);
            if (choroplethLayer.getLayers().length > 0) map.fitBounds(choroplethLayer.getBounds());
        })
        .catch(e => console.error('Choropleth error:', e))
        .finally(hideSpinner);
}

function updateExportTitle() {
    const select = document.getElementById('f-jenis');
    document.getElementById('exportSubtitle').innerText = select.options[select.selectedIndex].text;
}

document.getElementById('btnFilter').addEventListener('click', () => { loadChoropleth(); updateExportTitle(); });

let originalBasemapKey = null;
function switchToExportBasemap() {
    originalBasemapKey = Object.keys(basemaps).find(k => map.hasLayer(basemaps[k]));
    if (originalBasemapKey !== 'CartoDB Light') {
        map.removeLayer(basemaps[originalBasemapKey]);
        basemaps['CartoDB Light'].addTo(map);
    } else { originalBasemapKey = null; }
}
function restoreBasemap() {
    if (originalBasemapKey) {
        map.removeLayer(basemaps['CartoDB Light']);
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
                link.download = 'choropleth-kejadian-bencana.png';
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

loadChoropleth();
updateExportTitle();
</script>
@endpush
