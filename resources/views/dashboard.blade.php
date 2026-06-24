@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@push('styles')
{{--
    VARIASI 2: "TOPOGRAPHIC FIELD"
    ────────────────────────────────
    Tema: peta topografi + lapangan, terinspirasi dari warna kontur
    peta bencana alam: tanah, air, api, dan vegetasi.
    Palette: krem hangat #faf7f2, coklat tua #2c1a0e, oranye tanah #c4501a,
             hijau lumut #3d6b3f, biru sungai #1a5276.
    Typography: Playfair Display untuk judul (terasa seperti atlas/buku lapangan),
                Source Sans Pro untuk body.
    Signature: KPI cards dengan "counter bar" di sisi kiri berwarna per kategori
               + background krem bertekstur kertas.
    Layout: cards melebar penuh dengan border kiri tebal berwarna.
--}}
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

.topo-wrap {
    background: var(--cream);
    /* kertas bertekstur halus */
    background-image:
        radial-gradient(ellipse at 20% 50%, rgba(196,80,26,.04) 0%, transparent 60%),
        radial-gradient(ellipse at 80% 20%, rgba(26,82,118,.04) 0%, transparent 60%),
        url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='4' height='4'%3E%3Crect width='4' height='4' fill='%23faf7f2'/%3E%3Crect width='1' height='1' x='0' y='0' fill='rgba(90,58,34,.04)'/%3E%3C/svg%3E");
    min-height: 100vh;
    padding: 1.5rem 1.75rem 2.5rem;
    color: var(--text-main);
    font-family: 'Source Sans 3', sans-serif;
}

/* ── Header ─────────────────────────────────────────── */
.topo-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    margin-bottom: 1.75rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--border);
    flex-wrap: wrap;
    gap: 1rem;
}
.topo-title-group {}
.topo-eyebrow {
    font-size: .7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .15em;
    color: var(--earth);
    margin-bottom: .3rem;
}
.topo-title {
    font-family: 'Playfair Display', serif;
    font-size: 1.85rem;
    font-weight: 900;
    color: var(--brown-dark);
    line-height: 1.1;
    margin-bottom: .2rem;
}
.topo-source {
    font-size: .78rem;
    color: var(--text-muted);
    font-style: italic;
}
.topo-actions { display: flex; gap: .6rem; align-items: center; }
.topo-btn {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    font-size: .8rem;
    font-weight: 700;
    padding: .45rem 1rem;
    border-radius: 6px;
    text-decoration: none;
    cursor: pointer;
    border: 1.5px solid var(--border);
    background: transparent;
    color: var(--text-muted);
    transition: all .15s;
}
.topo-btn:hover { border-color: var(--earth); color: var(--earth); }
.topo-btn.primary {
    background: var(--earth);
    border-color: var(--earth);
    color: #fff;
}
.topo-btn.primary:hover { background: var(--earth-lt); border-color: var(--earth-lt); color: #fff; }

/* ── KPI Cards — border-kiri tebal ──────────────────── */
.kpi-row {
    display: grid;
    grid-template-columns: repeat(4,1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}
@media (max-width: 900px) { .kpi-row { grid-template-columns: repeat(2,1fr); } }

.kpi-tile {
    background: #fff;
    border-radius: 8px;
    border: 1px solid var(--border);
    border-left: 5px solid var(--border);
    padding: 1.1rem 1.1rem 1.1rem 1.25rem;
    position: relative;
    box-shadow: 0 1px 6px rgba(44,26,14,.06);
    transition: box-shadow .2s, transform .2s;
}
.kpi-tile:hover { box-shadow: 0 4px 18px rgba(44,26,14,.1); transform: translateY(-2px); }
.kpi-tile.ke  { border-left-color: var(--earth); }
.kpi-tile.kr  { border-left-color: var(--river); }
.kpi-tile.km  { border-left-color: var(--amber); }
.kpi-tile.kmoss { border-left-color: var(--moss); }

.kpi-tile-label {
    font-size: .72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: var(--text-muted);
    margin-bottom: .55rem;
    display: flex;
    align-items: center;
    gap: .5rem;
}
.kpi-tile-label i { font-size: .85rem; }
.ke .kpi-tile-label i   { color: var(--earth); }
.kr .kpi-tile-label i   { color: var(--river); }
.km .kpi-tile-label i   { color: var(--amber); }
.kmoss .kpi-tile-label i{ color: var(--moss); }

.kpi-tile-val {
    font-family: 'Playfair Display', serif;
    font-size: 2.4rem;
    font-weight: 900;
    color: var(--brown-dark);
    line-height: 1;
}

/* ── Extreme cards ───────────────────────────────────── */
.ext-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1.5rem;
}
@media (max-width: 700px) { .ext-row { grid-template-columns: 1fr; } }

.ext-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 1rem 1.25rem;
    box-shadow: 0 1px 6px rgba(44,26,14,.05);
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}
.ext-icon {
    width: 42px; height: 42px;
    border-radius: 8px;
    flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
}
.ext-tag {
    font-size: .65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: var(--text-muted);
    margin-bottom: .3rem;
}
.ext-name {
    font-family: 'Playfair Display', serif;
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--brown-dark);
    line-height: 1.2;
}
.ext-sub { font-size: .75rem; color: var(--text-muted); }
.ext-dom-pill {
    display: inline-flex; align-items: center; gap: .25rem;
    font-size: .68rem; font-weight: 600;
    padding: .12rem .45rem;
    border-radius: 99px;
    background: var(--cream2);
    color: var(--brown-mid);
    margin: .18rem .1rem 0 0;
}
.ext-num {
    margin-left: auto;
    text-align: right;
    flex-shrink: 0;
}
.ext-num .n {
    font-family: 'Playfair Display', serif;
    font-size: 1.9rem;
    font-weight: 900;
    line-height: 1;
}
.ext-num .u { font-size: .67rem; color: var(--text-muted); }

/* ── Chart section ───────────────────────────────────── */
.chart-row {
    display: grid;
    grid-template-columns: 3fr 2fr;
    gap: 1rem;
    margin-bottom: 1.5rem;
}
@media (max-width: 860px) { .chart-row { grid-template-columns: 1fr; } }

.field-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 6px rgba(44,26,14,.05);
}
.field-card-head {
    padding: .7rem 1.1rem;
    border-bottom: 1px solid var(--border);
    font-weight: 700;
    font-size: .82rem;
    color: var(--brown-dark);
    display: flex; align-items: center; justify-content: space-between;
}
.field-card-head i { color: var(--earth); margin-right: .4rem; }
.field-card-body { padding: 1rem 1.1rem; }

/* ── Table ───────────────────────────────────────────── */
.topo-table { width: 100%; border-collapse: collapse; }
.topo-table thead th {
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--text-muted);
    padding: .5rem 1rem;
    border-bottom: 2px solid var(--border);
    white-space: nowrap;
}
.topo-table tbody td {
    font-size: .82rem;
    padding: .65rem 1rem;
    border-bottom: 1px solid var(--cream2);
    vertical-align: middle;
    color: var(--text-main);
}
.topo-table tbody tr:last-child td { border-bottom: none; }
.topo-table tbody tr:hover td { background: var(--cream); }
.bencana-badge {
    font-size: .68rem; font-weight: 700;
    padding: .15rem .55rem;
    border-radius: 99px;
    background: rgba(196,80,26,.1);
    color: var(--earth);
}
.status-badge {
    font-size: .68rem; font-weight: 700;
    padding: .15rem .55rem;
    border-radius: 99px;
}
</style>
@endpush

@section('content')
<div class="topo-wrap">

    {{-- ── Header ────────────────────────── --}}
    <div class="topo-header">
        <div class="topo-title-group">
            <div class="topo-eyebrow">Sistem Informasi Kebencanaan</div>
            <div class="topo-title">Dashboard Bencana<br>Jawa Tengah</div>
            <div class="topo-source">Sumber: BPBD Jateng / CEVADIS</div>
        </div>
        <div class="topo-actions">
            <button id="btnExportDashboard" class="topo-btn">
                <i class="bi bi-download"></i> Export PNG
            </button>
            <a href="{{ route('map.index') }}" class="topo-btn primary">
                <i class="bi bi-map-fill"></i> Lihat Peta
            </a>
        </div>
    </div>

    <div id="dashboardExportArea">

    {{-- ── KPI ─────────────────────────── --}}
    <div class="kpi-row">
        <div class="kpi-tile ke">
            <div class="kpi-tile-label"><i class="bi bi-exclamation-triangle-fill"></i> Total Kejadian</div>
            <div class="kpi-tile-val">{{ number_format($totalKejadian) }}</div>
        </div>
        <div class="kpi-tile kr">
            <div class="kpi-tile-label"><i class="bi bi-tags-fill"></i> Jenis Bencana</div>
            <div class="kpi-tile-val">{{ number_format($totalJenisBencana) }}</div>
        </div>
        <div class="kpi-tile km">
            <div class="kpi-tile-label"><i class="bi bi-pin-map-fill"></i> Wilayah</div>
            <div class="kpi-tile-val">{{ number_format($totalWilayah) }}</div>
        </div>
        <div class="kpi-tile kmoss">
            <div class="kpi-tile-label"><i class="bi bi-person-fill-exclamation"></i> Total Korban</div>
            <div class="kpi-tile-val">{{ number_format($totalKorban) }}</div>
        </div>
    </div>

    {{-- ── Extremes ──────────────────────── --}}
    <div class="ext-row">
        <div class="ext-card">
            <div class="ext-icon" style="background:rgba(196,80,26,.1); color:var(--earth);">
                <i class="bi bi-arrow-up-circle-fill fs-5"></i>
            </div>
            <div class="flex-grow-1">
                <div class="ext-tag">Wilayah Kejadian Tertinggi</div>
                <div class="ext-name">{{ $wilayahTertinggi?->region?->nama_wilayah ?? '—' }}</div>
                <div class="ext-sub">{{ $wilayahTertinggi?->region?->jenis_wilayah ?? '' }}</div>
                @if($topDisasterWilayahTertinggi->isNotEmpty())
                <div class="mt-1">
                    @foreach($topDisasterWilayahTertinggi as $d)
                    <span class="ext-dom-pill">{{ $d->disasterTypes?->nama_bencana ?? '—' }} · {{ number_format($d->total) }}</span>
                    @endforeach
                </div>
                @endif
            </div>
            <div class="ext-num">
                <div class="n" style="color:var(--earth);">{{ number_format($wilayahTertinggi?->total ?? 0) }}</div>
                <div class="u">kejadian</div>
            </div>
        </div>
        <div class="ext-card">
            <div class="ext-icon" style="background:rgba(61,107,63,.1); color:var(--moss);">
                <i class="bi bi-arrow-down-circle-fill fs-5"></i>
            </div>
            <div class="flex-grow-1">
                <div class="ext-tag">Wilayah Kejadian Terendah</div>
                <div class="ext-name">{{ $wilayahTerendah?->region?->nama_wilayah ?? '—' }}</div>
                <div class="ext-sub">{{ $wilayahTerendah?->region?->jenis_wilayah ?? '' }}</div>
                @if($topDisasterWilayahTerendah->isNotEmpty())
                <div class="mt-1">
                    @foreach($topDisasterWilayahTerendah as $d)
                    <span class="ext-dom-pill">{{ $d->disasterTypes?->nama_bencana ?? '—' }} · {{ number_format($d->total) }}</span>
                    @endforeach
                </div>
                @endif
            </div>
            <div class="ext-num">
                <div class="n" style="color:var(--moss);">{{ number_format($wilayahTerendah?->total ?? 0) }}</div>
                <div class="u">kejadian</div>
            </div>
        </div>
    </div>

    {{-- ── Charts ────────────────────────── --}}
    <div class="chart-row">
        <div class="field-card">
            <div class="field-card-head">
                <span><i class="bi bi-bar-chart-fill"></i>10 Wilayah Kejadian Terbanyak</span>
                <a href="{{ route('disaster-events.index') }}" style="font-size:.75rem; color:var(--earth); text-decoration:none; font-weight:600;">Lihat semua →</a>
            </div>
            <div class="field-card-body">
                <canvas id="chartTopWilayah" style="max-height:260px;"></canvas>
            </div>
        </div>
        <div class="field-card">
            <div class="field-card-head">
                <span><i class="bi bi-pie-chart-fill"></i>Distribusi Jenis Bencana</span>
            </div>
            <div class="field-card-body" style="display:flex; align-items:center;">
                <canvas id="chartJenis" style="max-height:260px;"></canvas>
            </div>
        </div>
    </div>

    {{-- ── Recent Table ─────────────────── --}}
    <div class="field-card">
        <div class="field-card-head">
            <span><i class="bi bi-clock-history"></i>Kejadian Terbaru</span>
            <a href="{{ route('disaster-events.index') }}" style="font-size:.75rem; color:var(--text-muted); text-decoration:none;">Semua Kejadian →</a>
        </div>
        <div style="overflow-x:auto;">
            <table class="topo-table">
                <thead>
                    <tr>
                        <th>Tanggal</th><th>Wilayah</th><th>Jenis Bencana</th>
                        <th style="text-align:center;">Kejadian</th>
                        <th style="text-align:center;">Korban</th>
                        <th style="text-align:center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentEvents as $ev)
                    <tr>
                        <td style="color:var(--text-muted); font-size:.78rem;">{{ $ev->tanggal_kejadian?->format('d M Y') ?? '—' }}</td>
                        <td style="font-weight:700;">{{ $ev->region?->nama_wilayah ?? '—' }}</td>
                        <td><span class="bencana-badge">{{ $ev->disasterTypes?->nama_bencana ?? '—' }}</span></td>
                        <td style="text-align:center; font-weight:700; color:var(--earth);">{{ number_format($ev->jumlah_kejadian) }}</td>
                        <td style="text-align:center;">{{ number_format($ev->jumlah_korban) }}</td>
                        <td style="text-align:center;">
                            @php
                                $s = $ev->status;
                                $color = match(strtoupper($s ?? '')) {
                                    'ACC'     => ['bg' => 'rgba(61,107,63,.12)', 'txt' => '#3d6b3f'],
                                    'PENDING' => ['bg' => 'rgba(183,119,13,.12)', 'txt' => '#b7770d'],
                                    default   => ['bg' => 'rgba(122,101,82,.1)', 'txt' => '#7a6552'],
                                };
                            @endphp
                            <span class="status-badge" style="background:{{ $color['bg'] }}; color:{{ $color['txt'] }};">{{ $s ?? '—' }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center; padding:2rem; color:var(--text-muted); font-style:italic;">Belum ada data kejadian bencana.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    </div>{{-- end #dashboardExportArea --}}
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
const topWilayah     = @json($topWilayah);
const distribusi     = @json($distribusiJenis);
const riskDistribusi = @json($riskDistribution);

const PALETTE = ['#c4501a','#1a5276','#b7770d','#3d6b3f','#7b3f6e','#1a7a6e','#8b4513','#2874a6','#d4ac0d','#1e8449'];

new Chart(document.getElementById('chartTopWilayah'), {
    type: 'bar',
    data: {
        labels  : topWilayah.map(w => w.nama),
        datasets: [{
            label          : 'Jumlah Kejadian',
            data           : topWilayah.map(w => w.total),
            backgroundColor: topWilayah.map((_, i) => PALETTE[i % PALETTE.length] + 'bb'),
            borderColor    : topWilayah.map((_, i) => PALETTE[i % PALETTE.length]),
            borderWidth    : 1,
            borderRadius   : 4,
        }],
    },
    options: {
        indexAxis: 'y', responsive: true, maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.x.toLocaleString('id-ID')} kejadian` } },
        },
        scales: {
            x: { beginAtZero: true, grid: { color: '#f3ede4' }, ticks: { font: { size: 11 } } },
            y: { grid: { display: false }, ticks: { font: { size: 11 } } },
        },
    },
});

new Chart(document.getElementById('chartJenis'), {
    type: 'doughnut',
    data: {
        labels: distribusi.map(d => d.nama),
        datasets: [{
            data: distribusi.map(d => d.total),
            backgroundColor: distribusi.map((_, i) => PALETTE[i % PALETTE.length] + 'cc'),
            borderColor: '#fff',
            borderWidth: 3,
            hoverOffset: 6,
        }],
    },
    options: {
        responsive: true, cutout: '60%',
        plugins: {
            legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 10, boxWidth: 11 } },
            tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed.toLocaleString('id-ID')}` } },
        },
    },
});

document.getElementById('btnExportDashboard').addEventListener('click', function () {
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Mengekspor...';
    html2canvas(document.getElementById('dashboardExportArea'), {
        scale: 2, useCORS: true, backgroundColor: '#faf7f2',
    }).then(canvas => {
        const link = document.createElement('a');
        link.download = 'dashboard-kebencanaan.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-download"></i> Export PNG';
    });
});
</script>
@endpush
