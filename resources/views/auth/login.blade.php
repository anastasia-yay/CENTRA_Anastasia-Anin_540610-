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

/* ── Remember / Forgot ────────────────────────────── */
.field-meta {
    display: flex; align-items: center;
    justify-content: space-between;
    margin: .75rem 0 1.1rem;
}
.topo-checkbox-label {
    display: inline-flex; align-items: center; gap: .4rem;
    font-size: .76rem; color: var(--text-muted);
    cursor: pointer; user-select: none;
}
.topo-checkbox {
    width: 13px; height: 13px;
    accent-color: var(--earth); cursor: pointer; flex-shrink: 0;
}
.topo-forgot {
    font-size: .72rem; font-weight: 600;
    color: var(--text-muted); text-decoration: none;
    transition: color .15s;
}
.topo-forgot:hover { color: var(--earth); }

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
    box-sizing: border-box;
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
                <div class="topo-auth-title">Masuk ke Akun</div>
                <div class="topo-auth-sub">Masukkan kredensial Anda untuk melanjutkan.</div>
            </div>

            {{-- Body --}}
            <div class="topo-auth-body">

                @if (session('status'))
                    <div class="topo-status">
                        <i class="bi bi-check-circle me-1"></i>{{ session('status') }}
                    </div>
                @endif

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

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="field-group">
                        <label class="field-label" for="email">Alamat Email</label>
                        <div class="field-wrap">
                            <i class="bi bi-envelope field-icon"></i>
                            <input id="email" type="email" name="email"
                                   class="field-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}"
                                   placeholder="nama@instansi.go.id"
                                   required autofocus autocomplete="username">
                        </div>
                        @error('email')
                            <div class="field-error"><i class="bi bi-x-circle-fill"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="field-group">
                        <label class="field-label" for="password">Password</label>
                        <div class="field-wrap">
                            <i class="bi bi-lock field-icon"></i>
                            <input id="password" type="password" name="password"
                                   class="field-control has-toggle @error('password') is-invalid @enderror"
                                   placeholder="••••••••"
                                   required autocomplete="current-password">
                            <button type="button" class="field-toggle" id="togglePassword">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="field-error"><i class="bi bi-x-circle-fill"></i>{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Remember & Forgot --}}
                    <div class="field-meta">
                        <label class="topo-checkbox-label">
                            <input type="checkbox" name="remember" class="topo-checkbox" id="remember_me">
                            Ingat saya
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="topo-forgot">Lupa password?</a>
                        @endif
                    </div>

                    <button type="submit" class="topo-btn topo-btn-primary">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk
                    </button>
                </form>

                @if (Route::has('register'))
                    <div class="topo-divider"><span>atau</span></div>
                    <a href="{{ route('register') }}" class="topo-btn topo-btn-outline">
                        <i class="bi bi-person-plus"></i> Belum punya akun? Daftar
                    </a>
                @endif

            </div>{{-- /.topo-auth-body --}}

            <div class="topo-auth-footer">
                &copy; {{ date('Y') }} CENTRA &mdash; Central Java Disaster Analytics &mdash; BPBD
            </div>

        </div>{{-- /.topo-auth-card --}}
    </div>{{-- /.auth-wrap --}}
</div>{{-- /.auth-scene --}}
@endsection

@push('scripts')
<script>
    const pwd    = document.getElementById('password');
    const toggle = document.getElementById('togglePassword');
    const icon   = document.getElementById('toggleIcon');
    toggle.addEventListener('click', () => {
        const show = pwd.type === 'password';
        pwd.type = show ? 'text' : 'password';
        icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
</script>
@endpush
@endsection
{{-- </x-guest-layout> --}}
