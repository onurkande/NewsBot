{{-- resources/views/auth/login.blade.php --}}
@extends('auth.layouts.auth')

@section('title', 'Giriş Yap')

@section('content')
<div class="auth-shell">
    <aside class="auth-aside">
        <div class="auth-brand">
            <div class="logo">
                <svg viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#fff" d="M14.747 9.125c.527-1.426 1.736-2.573 3.317-2.573c1.643 0 2.792 1.085 3.318 2.573l6.077 16.867c.186.496.248.931.248 1.147c0 1.209-.992 2.046-2.139 2.046c-1.303 0-1.954-.682-2.264-1.611l-.931-2.915h-8.62l-.93 2.884c-.31.961-.961 1.642-2.232 1.642c-1.24 0-2.294-.93-2.294-2.17c0-.496.155-.868.217-1.023l6.233-16.867zm.34 11.256h5.891l-2.883-8.992h-.062l-2.946 8.992z"/>
                </svg>
            </div>
            <div class="name">Adminator</div>
        </div>

        <div class="auth-aside-body">
            <span class="auth-aside-eyebrow">2026 · v3.1 preview</span>
            <h1>The dashboard your team actually wants to open.</h1>
            <p>Faster builds, cleaner tokens, and a design system that scales from a single chart to a 12-screen ops cockpit.</p>
            <div class="auth-quote">
                "We replaced four bespoke admin tools with one Adminator workspace. The dark mode alone earned its keep."
                <div class="auth-quote-author">
                    <div class="av">SK</div>
                    <div>Sara Kim · Head of Engineering, Northwind</div>
                </div>
            </div>
        </div>

        <div class="auth-aside-footer">
            <span>© 2026</span>
            <span>BUILT IN RIGA, LV</span>
        </div>
    </aside>

    <main class="auth-main">
        <div class="auth-main-top">
            <a href="{{ url('/') }}" style="font-size: 12.5px; color: var(--t-muted); display: inline-flex; align-items: center; gap: 6px;">
                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Back to home
            </a>
            <div class="switch-link">New here? <a href="{{ route('register') }}">Create account</a></div>
        </div>

        <div class="auth-card">
            <h2>Welcome back</h2>
            <p class="sub">Sign in to your Adminator workspace to pick up where you left off.</p>

            @if ($errors->any())
                <div style="margin-bottom: 16px; padding: 12px 14px; border-radius: 10px; background: var(--danger-soft); color: var(--danger); font-size: 13px;">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if (session('status'))
                <div style="margin-bottom: 16px; padding: 12px 14px; border-radius: 10px; background: var(--success-soft); color: var(--success); font-size: 13px;">
                    {{ session('status') }}
                </div>
            @endif

            <form class="auth-form" action="{{ route('login') }}" method="POST">
                @csrf

                <div class="field">
                    <label class="field-label" for="email">Email</label>
                    <div class="input-icon">
                        <span class="ico">
                            <svg viewBox="0 0 24 24">
                                <rect x="3" y="5" width="18" height="14" rx="2"/>
                                <path d="m3 7 9 6 9-6"/>
                            </svg>
                        </span>
                        <input
                            id="email"
                            name="email"
                            class="input"
                            type="email"
                            value="{{ old('email') }}"
                            placeholder="you@company.com"
                            autocomplete="email"
                            required
                        >
                    </div>
                    @error('email')
                        <div class="field-help" style="color: var(--danger); margin-top: 6px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <div class="field-row">
                        <label class="field-label" for="password">Password</label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}">Forgot?</a>
                        @endif
                    </div>
                    <div class="input-icon">
                        <span class="ico">
                            <svg viewBox="0 0 24 24">
                                <rect x="3" y="11" width="18" height="11" rx="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </span>
                        <input
                            id="password"
                            name="password"
                            class="input"
                            type="password"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            required
                        >
                    </div>
                    @error('password')
                        <div class="field-help" style="color: var(--danger); margin-top: 6px;">{{ $message }}</div>
                    @enderror
                </div>

                <label class="check">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span class="box"></span>
                    Keep me signed in for 30 days
                </label>

                <button class="btn btn--primary auth-submit" type="submit">
                    Sign in
                    <svg viewBox="0 0 24 24"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                </button>
            </form>

            <div class="auth-divider">or continue with</div>

            <div class="social-row">
                <a class="social-btn" href="#" aria-label="Continue with Google">Google</a>
                <a class="social-btn" href="#" aria-label="Continue with GitHub">GitHub</a>
                <a class="social-btn" href="#" aria-label="Continue with Apple">Apple</a>
            </div>
        </div>

        <div class="auth-main-bottom">
            Don’t have an account? <a href="{{ route('register') }}" style="color: var(--primary); font-weight: 600;">Sign up</a>
        </div>
    </main>
</div>
@endsection