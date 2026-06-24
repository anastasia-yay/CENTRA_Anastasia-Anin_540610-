@extends('layouts.app')

@section('page-title', 'Manajemen Kejadian Bencana')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
<style>
:root {
    --cream     : #faf7f2;
    --cream2    : #f3ede4;
    --brown-dark: #2c1a0e;
    --brown-mid : #5a3a22;
    --brown-lt  : #8b6348;
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

/* ── Page wrapper ─────────────────────────────────── */
.topo-page {
    background: var(--cream);
    background-image:
        radial-gradient(ellipse at 20% 50%, rgba(196,80,26,.04) 0%, transparent 60%),
        radial-gradient(ellipse at 80% 20%, rgba(26,82,118,.04) 0%, transparent 60%);
    min-height: 100vh;
    padding: 1.5rem 1.75rem 2.5rem;
}

/* ── Header ────────────────────────────────────────── */
.page-eyebrow {
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .15em;
    color: var(--earth);
    margin-bottom: .2rem;
}
.page-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.6rem;
    font-weight: 900;
    color: var(--brown-dark);
    margin-bottom: .1rem;
}
.page-sub { font-size: .82rem; color: var(--text-muted); font-style: italic; }

/* ── Buttons ───────────────────────────────────────── */
.topo-btn {
    display: inline-flex; align-items: center; gap: .35rem;
    font-size: .78rem; font-weight: 700;
    padding: .42rem .9rem;
    border-radius: 6px;
    text-decoration: none; cursor: pointer;
    transition: all .15s;
}
.topo-btn-outline {
    border: 1.5px solid var(--border);
    background: transparent;
    color: var(--text-muted);
}
.topo-btn-outline:hover { border-color: var(--earth); color: var(--earth); }
.topo-btn-outline.green { border-color: #b7ddb4; color: var(--moss); }
.topo-btn-outline.green:hover { background: rgba(61,107,63,.07); }
.topo-btn-outline.red { border-color: #f0c4b4; color: var(--earth); }
.topo-btn-outline.red:hover { background: rgba(196,80,26,.07); }
.topo-btn-primary {
    border: 1.5px solid var(--earth);
    background: var(--earth);
    color: #fff;
}
.topo-btn-primary:hover { background: var(--earth-lt); border-color: var(--earth-lt); color: #fff; }

/* ── Alert ─────────────────────────────────────────── */
.topo-alert {
    background: #fff;
    border: 1px solid #b7ddb4;
    border-left: 4px solid var(--moss);
    border-radius: 8px;
    padding: .75rem 1rem;
    color: var(--moss);
    font-size: .85rem;
    font-weight: 600;
    display: flex; align-items: center; gap: .6rem;
    margin-bottom: 1.25rem;
}

/* ── Filter card ───────────────────────────────────── */
.topo-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 10px;
    box-shadow: 0 1px 6px rgba(44,26,14,.05);
    margin-bottom: 1.25rem;
}
.topo-card-head {
    padding: .8rem 1.25rem;
    border-bottom: 1px solid var(--border);
    font-weight: 700;
    font-size: .82rem;
    color: var(--brown-dark);
    display: flex; align-items: center; gap: .4rem;
}
.topo-card-head i { color: var(--earth); }
.topo-card-body { padding: 1.1rem 1.25rem; }

.topo-label {
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: var(--text-muted);
    margin-bottom: .35rem;
    display: block;
}
.topo-select {
    width: 100%;
    padding: .42rem .75rem;
    font-size: .85rem;
    border: 1.5px solid var(--border);
    border-radius: 6px;
    background: var(--cream);
    color: var(--brown-dark);
    font-family: 'Source Sans 3', sans-serif;
    transition: border-color .15s;
}
.topo-select:focus { outline: none; border-color: var(--earth); box-shadow: 0 0 0 3px rgba(196,80,26,.1); }

/* ── Table ─────────────────────────────────────────── */
.topo-table-wrap {
    overflow-x: auto;
}
.topo-table {
    width: 100%;
    border-collapse: collapse;
}
.topo-table thead th {
    font-size: .65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .09em;
    color: var(--text-muted);
    padding: .6rem 1rem;
    border-bottom: 2px solid var(--border);
    white-space: nowrap;
    background: var(--cream);
}
.topo-table thead th:first-child { border-radius: 0; padding-left: 1.25rem; }
.topo-table thead th:last-child  { padding-right: 1.25rem; }
.topo-table tbody td {
    font-size: .84rem;
    padding: .7rem 1rem;
    border-bottom: 1px solid var(--cream2);
    vertical-align: middle;
    color: var(--text-main);
}
.topo-table tbody td:first-child { padding-left: 1.25rem; }
.topo-table tbody td:last-child  { padding-right: 1.25rem; }
.topo-table tbody tr:last-child td { border-bottom: none; }
.topo-table tbody tr:hover td { background: #fdf9f5; }
.topo-table tbody tr { cursor: pointer; }

.badge-bencana {
    font-size: .69rem; font-weight: 700;
    padding: .18rem .55rem;
    border-radius: 99px;
    background: rgba(196,80,26,.1);
    color: var(--earth);
    border: 1px solid rgba(196,80,26,.2);
}
.badge-status {
    font-size: .69rem; font-weight: 700;
    padding: .18rem .55rem;
    border-radius: 99px;
}

/* ── Action button in table ────────────────────────── */
.btn-topo-edit {
    display: inline-flex; align-items: center; gap: .2rem;
    font-size: .75rem; font-weight: 700;
    padding: .3rem .65rem;
    border-radius: 6px;
    border: 1.5px solid var(--amber);
    color: var(--amber);
    background: transparent;
    text-decoration: none;
    transition: all .15s;
}
.btn-topo-edit:hover { background: rgba(183,119,13,.1); color: var(--amber); }
.btn-topo-del {
    display: inline-flex; align-items: center; gap: .2rem;
    font-size: .75rem; font-weight: 700;
    padding: .3rem .65rem;
    border-radius: 6px;
    border: 1.5px solid rgba(196,80,26,.4);
    color: var(--earth);
    background: transparent;
    cursor: pointer;
    transition: all .15s;
}
.btn-topo-del:hover { background: rgba(196,80,26,.08); }

/* ── Divider rule ──────────────────────────────────── */
.topo-rule { border: none; border-top: 2px solid var(--border); margin: 1.25rem 0; }
</style>
@endpush

@section('content')
<div class="topo-page">

    {{-- ── Header ────────────────────── --}}
    <div class="d-flex align-items-flex-start justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <div class="page-eyebrow">Sistem Informasi Kebencanaan</div>
            <div class="page-title">Kejadian Bencana</div>
            <div class="page-sub">Kelola, filter, import, dan export data kejadian bencana Provinsi Jawa Tengah</div>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <a href="{{ route('disaster-events.import.form') }}" class="topo-btn topo-btn-outline">
                <i class="bi bi-upload"></i> Import Excel
            </a>
            <a href="{{ route('disaster-events.export.excel') }}" class="topo-btn topo-btn-outline green">
                <i class="bi bi-file-earmark-excel"></i> Export Excel
            </a>
            <a href="{{ route('disaster-events.create') }}" class="topo-btn topo-btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Kejadian
            </a>
        </div>
    </div>

    {{-- ── Alert ───────────────────────── --}}
    @if(session('success'))
    <div class="topo-alert">
        <i class="bi bi-check-circle-fill"></i>
        {{ session('success') }}
    </div>
    @endif

    {{-- ── Filter ──────────────────────── --}}
    <div class="topo-card">
        <div class="topo-card-head">
            <i class="bi bi-funnel-fill"></i> Filter Data
        </div>
        <div class="topo-card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="topo-label">Jenis Bencana</label>
                    <select id="filterBencana" class="topo-select">
                        <option value="">— Semua Jenis Bencana —</option>
                        @foreach($disasterTypes as $type)
                        <option value="{{ $type->nama_bencana }}">{{ $type->nama_bencana }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="topo-label">Wilayah / Kabupaten</label>
                    <select id="filterWilayah" class="topo-select">
                        <option value="">— Semua Wilayah —</option>
                        @foreach($region as $reg)
                        <option value="{{ $reg->nama_wilayah }}">{{ $reg->nama_wilayah }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button id="btnFilter" class="topo-btn topo-btn-primary">
                        <i class="bi bi-funnel"></i> Terapkan
                    </button>
                    <button id="btnReset" class="topo-btn topo-btn-outline">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Tabel ───────────────────────── --}}
    <div class="topo-card">
        <div class="topo-table-wrap">
            <table class="topo-table" id="tableKejadian">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Wilayah</th>
                        <th>Jenis Bencana</th>
                        <th style="text-align:center;">Kejadian</th>
                        <th style="text-align:center;">Korban</th>
                        <th>Status</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($events as $event)
                    <tr class="row-detail"
                        data-url="{{ route('disaster-events.show', $event->id) }}">
                        <td style="color:var(--text-muted); font-size:.78rem;">
                            {{ $event->tanggal_kejadian?->format('d M Y') }}
                        </td>
                        <td style="font-weight:700;">{{ $event->region?->nama_wilayah }}</td>
                        <td>
                            <span class="badge-bencana">{{ $event->disasterTypes?->nama_bencana }}</span>
                        </td>
                        <td style="text-align:center; font-weight:700; color:var(--earth);">
                            {{ number_format($event->jumlah_kejadian) }}
                        </td>
                        <td style="text-align:center;">{{ number_format($event->jumlah_korban) }}</td>
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
                        <td style="text-align:right;">
                            <div class="d-flex justify-content-end gap-1" onclick="event.stopPropagation()">
                                <a href="{{ route('disaster-events.edit', $event->id) }}"
                                   class="btn-topo-edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('disaster-events.destroy', $event->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-topo-del">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnFilter = document.getElementById('btnFilter');
    const btnReset  = document.getElementById('btnReset');

    btnFilter.addEventListener('click', function () {
        const bencana = document.getElementById('filterBencana').value.toLowerCase();
        const wilayah = document.getElementById('filterWilayah').value.toLowerCase();
        document.querySelectorAll('#tableKejadian tbody tr').forEach(row => {
            const namaWilayah = row.cells[1].textContent.toLowerCase();
            const namaBencana = row.cells[2].textContent.toLowerCase();
            const tampil = (bencana === '' || namaBencana.includes(bencana)) &&
                           (wilayah === '' || namaWilayah.includes(wilayah));
            row.style.display = tampil ? '' : 'none';
        });
    });

    btnReset.addEventListener('click', function () {
        document.getElementById('filterBencana').value = '';
        document.getElementById('filterWilayah').value = '';
        document.querySelectorAll('#tableKejadian tbody tr').forEach(row => row.style.display = '');
    });

    // Double-click baris → buka detail
    document.querySelectorAll('.row-detail').forEach(row => {
        row.addEventListener('dblclick', function (e) {
            if (e.target.closest('a') || e.target.closest('button') || e.target.closest('form')) return;
            window.location.href = this.dataset.url;
        });
    });
});
</script>
@endpush
