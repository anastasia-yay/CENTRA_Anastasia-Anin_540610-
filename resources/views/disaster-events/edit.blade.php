@extends('layouts.app')

@section('page-title', 'Edit Kejadian Bencana')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
:root {
    --cream     : #faf7f2;
    --cream2    : #f3ede4;
    --brown-dark: #2c1a0e;
    --brown-mid : #5a3a22;
    --earth     : #c4501a;
    --earth-lt  : #e8734a;
    --moss      : #3d6b3f;
    --river     : #1a5276;
    --amber     : #b7770d;
    --border    : #d9cfc3;
    --text-main : #2c1a0e;
    --text-muted: #7a6552;
}

body, .content-wrapper, #main-content { font-family: 'Source Sans 3', sans-serif; }

.topo-page {
    background: var(--cream);
    background-image:
        radial-gradient(ellipse at 20% 50%, rgba(196,80,26,.04) 0%, transparent 60%),
        radial-gradient(ellipse at 80% 20%, rgba(26,82,118,.04) 0%, transparent 60%);
    min-height: 100vh;
    padding: 1.5rem 1.75rem 2.5rem;
}

.topo-back {
    display: inline-flex; align-items: center; gap: .35rem;
    font-size: .8rem; font-weight: 600;
    color: var(--text-muted); text-decoration: none;
    transition: color .15s;
}
.topo-back:hover { color: var(--earth); }

.page-eyebrow {
    font-size: .68rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .15em;
    color: var(--earth); margin-bottom: .2rem;
}
.page-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.5rem; font-weight: 900;
    color: var(--brown-dark); margin-bottom: 0;
}

/* ── Card ──────────────────────────────────────────── */
.topo-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 10px;
    box-shadow: 0 1px 6px rgba(44,26,14,.05);
}
.topo-card-head {
    padding: .85rem 1.25rem;
    border-bottom: 1px solid var(--border);
    font-family: 'Playfair Display', serif;
    font-size: 1rem; font-weight: 700;
    color: var(--brown-dark);
    display: flex; align-items: center; gap: .5rem;
}
.topo-card-head i { color: var(--earth); font-size: .9rem; }
.topo-card-body { padding: 1.25rem; }

/* ── Form fields ───────────────────────────────────── */
.field-group { margin-bottom: 1rem; }
.field-label {
    display: block;
    font-size: .72rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .08em;
    color: var(--text-muted); margin-bottom: .35rem;
}
.field-control {
    width: 100%;
    background: var(--cream);
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: .5rem .75rem;
    font-size: .88rem;
    font-family: 'Source Sans 3', sans-serif;
    color: var(--text-main);
    transition: border-color .15s, box-shadow .15s;
    appearance: none;
}
.field-control:focus {
    outline: none;
    border-color: var(--earth);
    box-shadow: 0 0 0 3px rgba(196,80,26,.1);
    background: #fff;
}
.field-control.is-invalid { border-color: var(--earth); }
.field-error {
    font-size: .75rem; color: var(--earth);
    margin-top: .25rem;
}

/* ── Map ───────────────────────────────────────────── */
#editMap {
    height: 300px;
    border-radius: 8px;
    border: 1px solid var(--border);
}
.map-caption {
    font-size: .72rem; color: var(--text-muted);
    font-style: italic; margin-top: .5rem;
}

/* ── Divider ───────────────────────────────────────── */
.topo-divider {
    border: none;
    border-top: 1px solid var(--border);
    margin: 1.25rem 0;
}

/* ── Buttons ───────────────────────────────────────── */
.topo-btn {
    display: inline-flex; align-items: center; gap: .35rem;
    font-size: .8rem; font-weight: 700;
    padding: .42rem 1.1rem;
    border-radius: 6px;
    text-decoration: none; cursor: pointer;
    transition: all .15s; border: 1.5px solid;
}
.topo-btn-outline {
    border-color: var(--border);
    background: transparent; color: var(--text-muted);
}
.topo-btn-outline:hover { border-color: var(--earth); color: var(--earth); }
.topo-btn-primary {
    border-color: var(--amber);
    background: transparent; color: var(--amber);
}
.topo-btn-primary:hover { background: rgba(183,119,13,.1); }
.topo-btn-danger {
    border-color: rgba(196,80,26,.4);
    background: transparent; color: var(--earth);
}
.topo-btn-danger:hover { background: rgba(196,80,26,.08); }

/* ── Alert validasi ────────────────────────────────── */
.topo-alert {
    background: rgba(196,80,26,.07);
    border: 1px solid rgba(196,80,26,.25);
    border-left: 4px solid var(--earth);
    border-radius: 8px;
    padding: .75rem 1rem;
    margin-bottom: 1.25rem;
    font-size: .82rem; color: var(--brown-mid);
}
.topo-alert ul { margin: .25rem 0 0 1rem; padding: 0; }
</style>
@endpush

@section('content')
<div class="topo-page">

    {{-- ── Header ──────────────────────────────────── --}}
    <div class="mb-4 d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
            <a href="{{ route('disaster-events.show', $event->id) }}" class="topo-back mb-2 d-inline-flex">
                <i class="bi bi-arrow-left"></i> Kembali ke Detail
            </a>
            <div class="page-eyebrow mt-2">Edit Data · ID #{{ $event->id }}</div>
            <div class="page-title">Ubah Kejadian Bencana</div>
        </div>
        {{-- Tombol hapus tetap tersedia dari halaman edit --}}
        <form action="{{ route('disaster-events.destroy', $event->id) }}"
              method="POST"
              onsubmit="return confirm('Yakin ingin menghapus data ini? Tindakan ini permanen.')">
            @csrf @method('DELETE')
            <button type="submit" class="topo-btn topo-btn-danger">
                <i class="bi bi-trash"></i> Hapus Data
            </button>
        </form>
    </div>

    {{-- ── Validasi error ───────────────────────────── --}}
    @if($errors->any())
    <div class="topo-alert">
        <strong>Terdapat {{ $errors->count() }} kesalahan input:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- ── Main grid ───────────────────────────────── --}}
    <div class="row g-4">

        {{-- Kolom form --}}
        <div class="col-lg-7">
            <form action="{{ route('disaster-events.update', $event->id) }}" method="POST">
                @csrf @method('PATCH')

                {{-- Waktu & Lokasi --}}
                <div class="topo-card mb-4">
                    <div class="topo-card-head">
                        <i class="bi bi-calendar3"></i> Waktu & Lokasi
                    </div>
                    <div class="topo-card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label class="field-label" for="tanggal_kejadian">Tanggal Kejadian</label>
                                    <input type="date"
                                           id="tanggal_kejadian"
                                           name="tanggal_kejadian"
                                           class="field-control @error('tanggal_kejadian') is-invalid @enderror"
                                           value="{{ old('tanggal_kejadian', $event->tanggal_kejadian?->format('Y-m-d')) }}"
                                           required>
                                    @error('tanggal_kejadian')
                                        <div class="field-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label class="field-label" for="region_id">Wilayah / Kabupaten</label>
                                    <select id="selectRegion"
                                            name="region_id"
                                            class="field-control @error('region_id') is-invalid @enderror"
                                            required>
                                        @foreach($region as $r)
                                            <option value="{{ $r->id }}"
                                                {{ old('region_id', $event->region_id) == $r->id ? 'selected' : '' }}>
                                                {{ $r->nama_wilayah }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('region_id')
                                        <div class="field-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Jenis & Skala Bencana --}}
                <div class="topo-card mb-4">
                    <div class="topo-card-head">
                        <i class="bi bi-exclamation-triangle"></i> Jenis &amp; Skala Bencana
                    </div>
                    <div class="topo-card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label class="field-label" for="disaster_type_id">Jenis Bencana</label>
                                    <select name="disaster_type_id"
                                            class="field-control @error('disaster_type_id') is-invalid @enderror"
                                            required>
                                        @foreach($disasterTypes as $type)
                                            <option value="{{ $type->id }}"
                                                {{ old('disaster_type_id', $event->disaster_type_id) == $type->id ? 'selected' : '' }}>
                                                {{ $type->nama_bencana }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('disaster_type_id')
                                        <div class="field-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label class="field-label" for="status">Status Penanganan</label>
                                    <input type="text"
                                           name="status"
                                           class="field-control @error('status') is-invalid @enderror"
                                           placeholder="Contoh: Waspada, Selesai Ditangani"
                                           value="{{ old('status', $event->status) }}">
                                    @error('status')
                                        <div class="field-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label class="field-label" for="jumlah_kejadian">Jumlah Kejadian</label>
                                    <input type="number"
                                           name="jumlah_kejadian"
                                           min="0"
                                           class="field-control @error('jumlah_kejadian') is-invalid @enderror"
                                           value="{{ old('jumlah_kejadian', $event->jumlah_kejadian) }}"
                                           required>
                                    @error('jumlah_kejadian')
                                        <div class="field-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label class="field-label" for="jumlah_korban">Jumlah Korban Jiwa</label>
                                    <input type="number"
                                           name="jumlah_korban"
                                           min="0"
                                           class="field-control @error('jumlah_korban') is-invalid @enderror"
                                           value="{{ old('jumlah_korban', $event->jumlah_korban) }}"
                                           required>
                                    @error('jumlah_korban')
                                        <div class="field-error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Keterangan --}}
                <div class="topo-card mb-4">
                    <div class="topo-card-head">
                        <i class="bi bi-journal-text"></i> Deskripsi &amp; Catatan
                    </div>
                    <div class="topo-card-body">
                        <div class="field-group mb-0">
                            <label class="field-label" for="keterangan">Keterangan Lapangan</label>
                            <textarea name="keterangan"
                                      class="field-control"
                                      rows="4"
                                      placeholder="Kronologi kejadian, kondisi lapangan, tindakan yang sudah diambil...">{{ old('keterangan', $event->keterangan) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Action --}}
                <div class="d-flex align-items-center gap-2 justify-content-end">
                    <a href="{{ route('disaster-events.show', $event->id) }}"
                       class="topo-btn topo-btn-outline">
                        Batal
                    </a>
                    <button type="submit" class="topo-btn topo-btn-primary">
                        <i class="bi bi-check2"></i> Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>

        {{-- Kolom peta --}}
        <div class="col-lg-5">
            <div class="topo-card" style="position: sticky; top: 1.5rem;">
                <div class="topo-card-head">
                    <i class="bi bi-geo-alt-fill"></i> Lokasi Terpilih
                </div>
                <div class="topo-card-body">
                    <div id="editMap"></div>
                    <p class="map-caption mt-2 mb-0">
                        <i class="bi bi-info-circle"></i>
                        Titik ditentukan dari centroid geometri wilayah terpilih.
                        Ganti wilayah di form untuk memperbarui peta.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const map = L.map('editMap').setView([-7.150975, 110.140259], 8);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

// Custom icon konsisten dengan show.blade
const makeIcon = () => L.divIcon({
    className: '',
    html: `<div style="
        width:36px; height:36px;
        background:#c4501a;
        border:3px solid #fff;
        border-radius:50% 50% 50% 0;
        transform: rotate(-45deg);
        box-shadow: 0 3px 12px rgba(196,80,26,.5);
    "></div>`,
    iconSize: [36, 36],
    iconAnchor: [18, 36],
    popupAnchor: [0, -36],
});

let currentMarker;

function flyToRegion(regionId, regionName) {
    if (!regionId) return;
    fetch(`/api/regions/${regionId}`)
        .then(r => r.json())
        .then(data => {
            const coords = data.centroid
                ? [data.centroid.lat, data.centroid.lng]
                : (data.lat && data.lng ? [data.lat, data.lng] : null);

            if (!coords) return;

            map.flyTo(coords, 11);
            if (currentMarker) map.removeLayer(currentMarker);
            currentMarker = L.marker(coords, { icon: makeIcon() })
                .addTo(map)
                .bindPopup(`
                    <div style="font-family:'Source Sans 3',sans-serif; min-width:160px;">
                        <div style="font-weight:700; font-size:.9rem; color:#2c1a0e;">
                            ${regionName || data.nama_wilayah || ''}
                        </div>
                        <div style="font-size:.75rem; color:#7a6552; margin-top:.2rem;">
                            Pusat koordinat wilayah
                        </div>
                    </div>
                `)
                .openPopup();
        })
        .catch(() => {});
}

// Load lokasi dari data event yang sudah tersimpan saat halaman dibuka
@if($event->region_id)
    flyToRegion({{ $event->region_id }}, '{{ addslashes($event->region?->nama_wilayah ?? '') }}');
@endif

// Update peta ketika wilayah diubah di form
document.getElementById('selectRegion').addEventListener('change', function () {
    const selected = this.options[this.selectedIndex];
    flyToRegion(this.value, selected.text);
});
</script>
@endpush
