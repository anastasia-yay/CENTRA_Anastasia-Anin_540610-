@extends('layouts.app')

@section('page-title', 'Detail Kejadian Bencana')

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

/* ── Breadcrumb nav ────────────────────────────────── */
.topo-back {
    display: inline-flex; align-items: center; gap: .35rem;
    font-size: .8rem; font-weight: 600;
    color: var(--text-muted);
    text-decoration: none;
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

/* ── Attribute table ───────────────────────────────── */
.attr-table { width: 100%; border-collapse: collapse; }
.attr-table tr td { padding: .6rem 0; vertical-align: top; }
.attr-table tr td:first-child {
    width: 38%;
    font-size: .8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: var(--text-muted);
    padding-right: 1rem;
}
.attr-table tr td:last-child {
    font-size: .9rem;
    color: var(--text-main);
    font-weight: 600;
}
.attr-table tr { border-bottom: 1px solid var(--cream2); }
.attr-table tr:last-child { border-bottom: none; }

.badge-bencana {
    font-size: .75rem; font-weight: 700;
    padding: .2rem .65rem;
    border-radius: 99px;
    background: rgba(196,80,26,.1);
    color: var(--earth);
    border: 1px solid rgba(196,80,26,.2);
}
.badge-status {
    font-size: .75rem; font-weight: 700;
    padding: .2rem .65rem;
    border-radius: 99px;
}

/* ── Keterangan box ────────────────────────────────── */
.keterangan-box {
    background: var(--cream2);
    border: 1px solid var(--border);
    border-left: 4px solid var(--brown-mid);
    border-radius: 8px;
    padding: .85rem 1rem;
    margin-top: 1.1rem;
}
.keterangan-box .label {
    font-size: .68rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .08em;
    color: var(--text-muted); margin-bottom: .35rem;
}
.keterangan-box p { font-size: .85rem; color: var(--brown-mid); margin: 0; font-style: italic; }

/* ── Stat strip (jumlah ringkas di atas) ───────────── */
.stat-strip {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1.25rem;
}
.stat-tile {
    background: #fff;
    border: 1px solid var(--border);
    border-left: 4px solid var(--border);
    border-radius: 8px;
    padding: .85rem 1.1rem;
    box-shadow: 0 1px 4px rgba(44,26,14,.04);
}
.stat-tile.earth { border-left-color: var(--earth); }
.stat-tile.river { border-left-color: var(--river); }
.stat-tile-label {
    font-size: .68rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .08em;
    color: var(--text-muted); margin-bottom: .3rem;
}
.stat-tile-val {
    font-family: 'Playfair Display', serif;
    font-size: 2rem; font-weight: 900;
    line-height: 1;
    color: var(--brown-dark);
}
.stat-tile.earth .stat-tile-val { color: var(--earth); }
.stat-tile.river .stat-tile-val { color: var(--river); }

/* ── Map ───────────────────────────────────────────── */
#detailMap {
    height: 320px;
    border-radius: 8px;
    border: 1px solid var(--border);
}

/* ── Action buttons ────────────────────────────────── */
.topo-btn {
    display: inline-flex; align-items: center; gap: .35rem;
    font-size: .8rem; font-weight: 700;
    padding: .42rem 1rem;
    border-radius: 6px;
    text-decoration: none; cursor: pointer;
    transition: all .15s;
}
.topo-btn-outline {
    border: 1.5px solid var(--border);
    background: transparent; color: var(--text-muted);
}
.topo-btn-outline:hover { border-color: var(--earth); color: var(--earth); }
.topo-btn-primary {
    border: 1.5px solid var(--amber);
    background: transparent; color: var(--amber);
}
.topo-btn-primary:hover { background: rgba(183,119,13,.1); }
.topo-btn-danger {
    border: 1.5px solid rgba(196,80,26,.4);
    background: transparent; color: var(--earth);
}
.topo-btn-danger:hover { background: rgba(196,80,26,.08); }
</style>
@endpush

@section('content')
<div class="topo-page">

    {{-- ── Breadcrumb & Header ─────────────────────── --}}
    <div class="mb-4 d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
            <a href="{{ route('disaster-events.index') }}" class="topo-back mb-2 d-inline-flex">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar
            </a>
            <div class="page-eyebrow mt-2">Detail Kejadian · ID #{{ $event->id }}</div>
            <div class="page-title">
                {{ $event->disasterTypes?->nama_bencana ?? 'Kejadian Bencana' }}
                <span style="font-size:1rem; font-weight:400; color:var(--text-muted);">
                    — {{ $event->region?->nama_wilayah ?? '' }}
                </span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('disaster-events.edit', $event->id) }}" class="topo-btn topo-btn-primary">
                <i class="bi bi-pencil"></i> Edit Data
            </a>
            <form action="{{ route('disaster-events.destroy', $event->id) }}"
                  method="POST"
                  onsubmit="return confirm('Yakin ingin menghapus data ini? Tindakan ini permanen.')">
                @csrf @method('DELETE')
                <button type="submit" class="topo-btn topo-btn-danger">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </form>
        </div>
    </div>

    {{-- ── Stat strip ──────────────────────────────── --}}
    <div class="stat-strip">
        <div class="stat-tile earth">
            <div class="stat-tile-label">Jumlah Kejadian</div>
            <div class="stat-tile-val">{{ number_format($event->jumlah_kejadian) }}</div>
        </div>
        <div class="stat-tile river">
            <div class="stat-tile-label">Jumlah Korban Jiwa</div>
            <div class="stat-tile-val">{{ number_format($event->jumlah_korban) }}</div>
        </div>
    </div>

    {{-- ── Main content ─────────────────────────────── --}}
    <div class="row g-4">

        {{-- Informasi atribut --}}
        <div class="col-lg-6">
            <div class="topo-card h-100">
                <div class="topo-card-head">
                    <i class="bi bi-card-text"></i> Informasi Atribut
                </div>
                <div class="topo-card-body">
                    <table class="attr-table">
                        <tr>
                            <td>Jenis Bencana</td>
                            <td><span class="badge-bencana">{{ $event->disasterTypes?->nama_bencana ?? '—' }}</span></td>
                        </tr>
                        <tr>
                            <td>Wilayah Terkena</td>
                            <td>{{ $event->region?->nama_wilayah ?? '—' }}
                                <span style="font-weight:400; color:var(--text-muted); font-size:.82rem;">
                                    ({{ $event->region?->jenis_wilayah ?? 'Kabupaten' }})
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>Waktu Kejadian</td>
                            <td>{{ $event->tanggal_kejadian?->format('d F Y') ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td>Tingkat Risiko</td>
                            <td>{{ $event->riskLevel?->nama_level ?? 'Tidak Teridentifikasi' }}</td>
                        </tr>
                        <tr>
                            <td>Jumlah Dampak</td>
                            <td>{{ number_format($event->jumlah_kejadian) }} kejadian terpisah</td>
                        </tr>
                        <tr>
                            <td>Korban Jiwa</td>
                            <td style="color:var(--earth);">{{ number_format($event->jumlah_korban) }} jiwa</td>
                        </tr>
                        <tr>
                            <td>Status Administrasi</td>
                            <td>
                                @php
                                    $s = $event->status;
                                    $c = match(strtoupper($s ?? '')) {
                                        'ACC'     => ['bg' => 'rgba(61,107,63,.12)',  'txt' => '#3d6b3f'],
                                        'PENDING' => ['bg' => 'rgba(183,119,13,.12)', 'txt' => '#b7770d'],
                                        default   => ['bg' => 'rgba(122,101,82,.1)',  'txt' => '#7a6552'],
                                    };
                                @endphp
                                <span class="badge-status" style="background:{{ $c['bg'] }}; color:{{ $c['txt'] }};">
                                    {{ $s ?? '—' }}
                                </span>
                            </td>
                        </tr>
                    </table>

                    <div class="keterangan-box">
                        <div class="label">Deskripsi &amp; Catatan Lapangan</div>
                        <p>{{ $event->keterangan ?? 'Tidak ada keterangan tambahan.' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Peta lokasi --}}
        <div class="col-lg-6">
            <div class="topo-card h-100">
                <div class="topo-card-head">
                    <i class="bi bi-geo-alt-fill"></i> Lokasi Aktual Spasial
                </div>
                <div class="topo-card-body">
                    <div id="detailMap"></div>
                    <p class="mt-2 mb-0" style="font-size:.75rem; color:var(--text-muted); font-style:italic;">
                        <i class="bi bi-info-circle"></i>
                        Titik ditentukan dari centroid geometri wilayah terpilih.
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
const map = L.map('detailMap').setView([-7.150975, 110.140259], 9);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);

// Ambil centroid wilayah dari API lalu tampilkan marker
@if($event->region_id)
fetch('/api/region/{{ $event->region_id }}')
    .then(r => r.json())
    .then(data => {
        if (data && data.lat && data.lng) {
            const coords = [data.lat, data.lng];
            map.flyTo(coords, 11);

            // Custom icon ala field marker
            const icon = L.divIcon({
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

            L.marker(coords, { icon })
                .addTo(map)
                .bindPopup(`
                    <div style="font-family:'Source Sans 3',sans-serif; min-width:180px;">
                        <div style="font-weight:700; font-size:.9rem; color:#2c1a0e; margin-bottom:.3rem;">
                            {{ $event->region?->nama_wilayah ?? '' }}
                        </div>
                        <div style="font-size:.78rem; color:#7a6552;">
                            {{ $event->disasterTypes?->nama_bencana ?? '' }} ·
                            {{ $event->tanggal_kejadian?->format('d M Y') ?? '' }}
                        </div>
                        <div style="font-size:.78rem; margin-top:.3rem; color:#c4501a; font-weight:700;">
                            {{ number_format($event->jumlah_kejadian) }} kejadian ·
                            {{ number_format($event->jumlah_korban) }} korban
                        </div>
                    </div>
                `)
                .openPopup();
        }
    })
    .catch(() => {
        // Fallback: tengah Jawa Tengah jika API gagal
        L.marker([-7.150975, 110.140259])
            .addTo(map)
            .bindPopup('{{ $event->region?->nama_wilayah ?? "Lokasi tidak diketahui" }}')
            .openPopup();
    });
@endif
</script>
@endpush
