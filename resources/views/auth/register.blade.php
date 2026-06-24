{{-- <x-guest-layout> --}}
@extends('layouts.guest')
@section('content')
@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
<style>
:root {
    --cream     : #faf7f2;
    --cream2    : #f3ede4;
    --brown-dark: #2c1a0e;
    --brown-mid : #5a3a22;
    --earth     : #c4501a;
    --amber     : #b7770d;
    --moss      : #3d6b3f;
    --border    : #d9cfc3;
    --text-main : #2c1a0e;
    --text-muted: #7a6552;
    --glass-bg    : rgba(255,255,255,0.55);
    --glass-border: rgba(255,255,255,0.70);
    --glass-shadow: rgba(44,26,14,0.18);
}

/* ── Override layout: full-bleed background behind main content ── */
#main-content,
.content-wrapper,
.wrapper {
    background: none !important;
    padding: 0 !important;
}

.auth-scene {
    min-height: calc(100vh - 56px); /* 56px = typical navbar height */
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
    background:
        linear-gradient(160deg, rgba(44,26,14,.58) 0%, rgba(61,107,63,.46) 50%, rgba(26,82,118,.52) 100%),
        url('https://images.unsplash.com/photo-1596401100919-3e06e7b02e56?w=1600&q=80')
        center center / cover no-repeat;
    font-family: 'Source Sans 3', sans-serif;
}

/* ── Constrain card width ─────────────────────────── */
.auth-wrap {
    width: 100%;
    max-width: 400px;
}

/* ── Card glassmorphism ───────────────────────────── */
.topo-auth-card {
    background: var(--glass-bg);
    backdrop-filter: blur(20px) saturate(1.7);
    -webkit-backdrop-filter: blur(20px) saturate(1.7);
    border: 1px solid var(--glass-border);
    border-radius: 18px;
    box-shadow:
        0 8px 40px var(--glass-shadow),
        inset 0 1px 0 rgba(255,255,255,0.85);
    overflow: hidden;
}
.topo-auth-card::before {
    content: '';
    display: block;
    height: 4px;
    background: linear-gradient(90deg, var(--earth) 0%, var(--amber) 100%);
}

/* ── Brand ────────────────────────────────────────── */
.topo-auth-brand {
    padding: 1.6rem 1.75rem 0;
    text-align: center;
}
.brand-logo-wrap {
    display: inline-flex;
    align-items: center; justify-content: center;
    width: 50px; height: 50px;
    background: linear-gradient(135deg, var(--earth), var(--amber));
    border-radius: 13px;
    margin-bottom: .65rem;
    box-shadow: 0 4px 14px rgba(196,80,26,.38);
}
.brand-logo-wrap i { color: #fff; font-size: 1.4rem; }
.brand-name {
    font-family: 'Playfair Display', serif;
    font-size: 1.65rem; font-weight: 900;
    color: var(--brown-dark);
    letter-spacing: .04em; line-height: 1;
    margin-bottom: .12rem;
}
.brand-tagline {
    font-size: .62rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .16em;
    color: var(--earth);
}

/* ── Header ───────────────────────────────────────── */
.topo-auth-header {
    padding: 1rem 1.75rem .8rem;
    border-bottom: 1px solid rgba(217,207,195,.55);
    text-align: center;
}
.topo-auth-title {
    font-family: 'Playfair Display', serif;
    font-size: 1rem; font-weight: 700;
    color: var(--brown-dark); margin: 0;
}
.topo-auth-sub {
    font-size: .72rem; color: var(--text-muted); margin-top: .2rem;
}

/* ── Body ─────────────────────────────────────────── */
.topo-auth-body { padding: 1.35rem 1.75rem 1.2rem; }

/* ── Alerts ───────────────────────────────────────── */
.topo-alert {
    background: rgba(196,80,26,.09);
    border: 1px solid rgba(196,80,26,.25);
    border-left: 4px solid var(--earth);
    border-radius: 8px;
    padding: .6rem .9rem;
    margin-bottom: 1.1rem;
    font-size: .76rem; color: var(--brown-mid);
}
.topo-alert ul { margin: .18rem 0 0 1rem; padding: 0; }
.topo-status {
    background: rgba(61,107,63,.09);
    border: 1px solid rgba(61,107,63,.28);
    border-left: 4px solid var(--moss);
    border-radius: 8px;
    padding: .55rem .9rem;
    margin-bottom: 1.1rem;
    font-size: .76rem; color: var(--moss);
}

/* ── Fields ───────────────────────────────────────── */
.field-group { margin-bottom: .9rem; }
.field-label {
    display: block;
    font-size: .63rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .1em;
    color: var(--text-muted); margin-bottom: .32rem;
}
.field-wrap { position: relative; display: flex; align-items: center; }
.field-icon {
    position: absolute; left: .75rem;
    color: var(--text-muted); font-size: .8rem;
    pointer-events: none;
    top: 50%; transform: translateY(-50%);
}
.field-control {
    width: 100%;
    background: rgba(255,255,255,0.70);
    border: 1px solid rgba(217,207,195,0.82);
    border-radius: 8px;
    padding: .54rem .75rem .54rem 2.1rem;
    font-size: .855rem;
    font-family: 'Source Sans 3', sans-serif;
    color: var(--text-main);
    transition: border-color .15s, box-shadow .15s, background .15s;
    box-sizing: border-box;
}
.field-control::placeholder { color: #b5a595; }
.field-control:focus {
    outline: none;
    border-color: var(--earth);
    box-shadow: 0 0 0 3px rgba(196,80,26,.12);
    background: rgba(255,255,255,0.92);
}
.field-control.is-invalid { border-color: var(--earth); background: rgba(196,80,26,.04); }
.field-error {
    font-size: .7rem; color: var(--earth);
    margin-top: .24rem;
    display: flex; align-items: center; gap: .28rem;
}

/* ── Password toggle ──────────────────────────────── */
.field-toggle {
    position: absolute; right: .7rem;
    background: none; border: none; padding: 0;
    color: var(--text-muted); cursor: pointer;
    font-size: .8rem; line-height: 1;
    top: 50%; transform: translateY(-50%);
    transition: color .15s;
}
.field-toggle:hover { color: var(--earth); }
.field-control.has-toggle { padding-right: 2.1rem; }

/* ── 2-column grid ──────────────────────────────────── */
.field-row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0 .7rem;
}
@media (max-width: 500px) {
    .field-row-2 { grid-template-columns: 1fr; }
}

/* ── Password strength & hints ──────────────────────── */
.pwd-strength { margin-top: .4rem; }
.pwd-strength-bar {
    height: 3px; border-radius: 99px;
    background: rgba(217,207,195,.6);
    overflow: hidden; margin-bottom: .22rem;
}
.pwd-strength-fill {
    height: 100%; border-radius: 99px;
    width: 0%; transition: width .25s, background .25s;
}
.pwd-strength-label {
    font-size: .66rem; font-style: italic;
}
.field-hint {
    font-size: .66rem; color: var(--text-muted);
    margin-top: .26rem; font-style: italic;
}

/* ── Buttons ──────────────────────────────────────── */
.topo-btn {
    display: inline-flex; align-items: center;
    justify-content: center; gap: .38rem;
    width: 100%;
    font-size: .82rem; font-weight: 700;
    font-family: 'Source Sans 3', sans-serif;
    letter-spacing: .05em;
    padding: .6rem 1.2rem;
    border-radius: 8px;
    cursor: pointer; text-decoration: none;
    transition: all .18s; border: 1.5px solid;
    box-sizing: border-box; margin-top: .6rem;
}
.topo-btn-primary {
    background: linear-gradient(135deg, var(--earth), var(--amber));
    border-color: transparent; color: #fff;
    box-shadow: 0 3px 14px rgba(196,80,26,.35);
}
.topo-btn-primary:hover {
    box-shadow: 0 5px 20px rgba(196,80,26,.45);
    transform: translateY(-1px); color: #fff;
}
.topo-btn-outline {
    background: rgba(255,255,255,0.42);
    border-color: rgba(217,207,195,0.88);
    color: var(--text-muted);
}
.topo-btn-outline:hover { border-color: var(--earth); color: var(--earth); background: rgba(255,255,255,.6); }

/* ── Divider ──────────────────────────────────────── */
.topo-divider {
    display: flex; align-items: center; gap: .6rem;
    margin: 1.05rem 0;
}
.topo-divider::before,
.topo-divider::after { content:''; flex:1; height:1px; background:rgba(217,207,195,.65); }
.topo-divider span {
    font-size: .58rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .12em;
    color: var(--text-muted);
}

/* ── Footer ───────────────────────────────────────── */
.topo-auth-footer {
    padding: .65rem 1.75rem .85rem;
    border-top: 1px solid rgba(217,207,195,.45);
    background: rgba(243,237,228,0.48);
    text-align: center;
    font-size: .69rem; color: var(--text-muted);
}
</style>
@endpush

@section('content')
<div class="auth-scene">
    <div class="auth-wrap">
        <div class="topo-auth-card">

            {{-- Brand --}}
            <div class="topo-auth-brand">
                <div class="brand-logo-wrap">
                    <i class="bi bi-geo-alt-fill"></i>
                </div>
                <div class="brand-name">CENTRA</div>
                <div class="brand-tagline">Central Java Disaster Analytics</div>
            </div>

            {{-- Header --}}
            <div class="topo-auth-header">
                <div class="topo-auth-title">Buat Akun Baru</div>
                <div class="topo-auth-sub">Isi formulir di bawah untuk mendaftarkan diri.</div>
            </div>

            {{-- Body --}}
            <div class="topo-auth-body">

                {{-- Validation errors --}}
                @if ($errors->any())
                    <div class="topo-alert">
                        <strong><i class="bi bi-exclamation-triangle me-1"></i>
                        Terdapat {{ $errors->count() }} kesalahan:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- Nama --}}
                    <div class="field-group">
                        <label class="field-label" for="name">Nama Lengkap</label>
                        <div class="field-wrap">
                            <i class="bi bi-person field-icon"></i>
                            <input id="name" type="text" name="name"
                                   class="field-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="Nama sesuai identitas"
                                   required autofocus autocomplete="name">
                        </div>
                        @error('name')
                            <div class="field-error">
                                <i class="bi bi-x-circle-fill"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="field-group">
                        <label class="field-label" for="email">Alamat Email</label>
                        <div class="field-wrap">
                            <i class="bi bi-envelope field-icon"></i>
                            <input id="email" type="email" name="email"
                                   class="field-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   placeholder="nama@instansi.go.id"
                                   required autocomplete="username">
                        </div>
                        @error('email')
                            <div class="field-error">
                                <i class="bi bi-x-circle-fill"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Passwords Row --}}
                    <div class="field-row-2">
                        {{-- Password --}}
                        <div class="field-group">
                            <label class="field-label" for="password">Password</label>
                            <div class="field-wrap">
                                <i class="bi bi-lock field-icon"></i>
                                <input id="password" type="password" name="password"
                                       class="field-control has-toggle @error('password') is-invalid @enderror"
                                       placeholder="Min. 8"
                                       required autocomplete="new-password">
                                <button type="button" class="field-toggle" id="togglePwd" title="Tampilkan">
                                    <i class="bi bi-eye" id="togglePwdIcon"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="field-error">
                                    <i class="bi bi-x-circle-fill"></i>{{ $message }}
                                </div>
                            @enderror
                            <div class="pwd-strength" id="pwdStrengthWrap" style="display:none;">
                                <div class="pwd-strength-bar">
                                    <div class="pwd-strength-fill" id="pwdStrengthFill"></div>
                                </div>
                                <div class="pwd-strength-label" id="pwdStrengthLabel"></div>
                            </div>
                        </div>

                        {{-- Konfirmasi --}}
                        <div class="field-group">
                            <label class="field-label" for="password_confirmation">Konfirmasi</label>
                            <div class="field-wrap">
                                <i class="bi bi-lock-fill field-icon"></i>
                                <input id="password_confirmation" type="password"
                                       name="password_confirmation"
                                       class="field-control has-toggle"
                                       placeholder="Ulangi"
                                       required autocomplete="new-password">
                                <button type="button" class="field-toggle" id="toggleConfirm" title="Tampilkan">
                                    <i class="bi bi-eye" id="toggleConfirmIcon"></i>
                                </button>
                            </div>
                            <div class="field-hint" id="matchHint"></div>
                        </div>
                    </div>{{-- /.field-row-2 --}}

                    {{-- Submit --}}
                    <button type="submit" class="topo-btn topo-btn-primary">
                        <i class="bi bi-person-check"></i> Buat Akun
                    </button>

                </form>

                {{-- Login link --}}
                <div class="topo-divider"><span>atau</span></div>
                <a href="{{ route('login') }}" class="topo-btn topo-btn-outline">
                    <i class="bi bi-box-arrow-in-right"></i> Sudah punya akun? Masuk
                </a>

            </div>{{-- /.topo-auth-body --}}

            {{-- Footer --}}
            <div class="topo-auth-footer">
                &copy; {{ date('Y') }} CENTRA &mdash; Central Java Disaster Analytics &mdash; BPBD
            </div>

        </div>{{-- /.topo-auth-card --}}
    </div>{{-- /.auth-wrap --}}
</div>{{-- /.auth-scene --}}
@endsection

@push('scripts')
<script>
/* ── Password toggle ──────────────────────────── */
function makeToggle(inputId, btnId, iconId) {
    const input = document.getElementById(inputId);
    const btn   = document.getElementById(btnId);
    const icon  = document.getElementById(iconId);
    if (!input || !btn) return;
    btn.addEventListener('click', () => {
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
}
makeToggle('password',              'togglePwd',     'togglePwdIcon');
makeToggle('password_confirmation', 'toggleConfirm', 'toggleConfirmIcon');

/* ── Password strength ────────────────────────── */
const pwdInput = document.getElementById('password');
const wrap     = document.getElementById('pwdStrengthWrap');
const fill     = document.getElementById('pwdStrengthFill');
const lbl      = document.getElementById('pwdStrengthLabel');
const levels   = [
    { min:0,  pct:0,   color:'var(--border)',   text:'' },
    { min:1,  pct:25,  color:'#e05252',         text:'Terlalu lemah' },
    { min:4,  pct:50,  color:'var(--earth)',    text:'Cukup' },
    { min:6,  pct:75,  color:'var(--amber)',    text:'Baik' },
    { min:10, pct:100, color:'var(--moss)',     text:'Sangat kuat' },
];
pwdInput.addEventListener('input', () => {
    const v = pwdInput.value;
    wrap.style.display = v.length ? 'block' : 'none';
    let s = 0;
    if (v.length >= 8)           s++;
    if (v.length >= 12)          s++;
    if (/[A-Z]/.test(v))         s++;
    if (/[a-z]/.test(v))         s++;
    if (/[0-9]/.test(v))         s++;
    if (/[^A-Za-z0-9]/.test(v))  s++;
    const lvl = [...levels].reverse().find(l => s >= l.min) || levels[0];
    fill.style.width      = lvl.pct + '%';
    fill.style.background = lvl.color;
    lbl.textContent       = lvl.text;
    lbl.style.color       = lvl.color;
});

/* ── Password match ───────────────────────────── */
const confirmInput = document.getElementById('password_confirmation');
const matchHint    = document.getElementById('matchHint');
function checkMatch() {
    if (!confirmInput.value) { matchHint.textContent = ''; return; }
    const ok = pwdInput.value === confirmInput.value;
    matchHint.textContent     = ok ? '✓ Cocok' : '✗ Tidak cocok';
    matchHint.style.color     = ok ? 'var(--moss)' : 'var(--earth)';
    matchHint.style.fontStyle = 'normal';
    matchHint.style.fontWeight = '600';
}
pwdInput.addEventListener('input',     checkMatch);
confirmInput.addEventListener('input', checkMatch);
</script>
@endpush
{{-- </x-guest-layout> --}}
