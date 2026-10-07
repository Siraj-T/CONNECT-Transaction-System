<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'CONNECT') }} - Login</title>
        @vite(['resources/css/app.css'])
        <style>
            .guest-wrapper {
                min-height: 100vh; display: flex; flex-direction: column; justify-content: center; align-items: center;
                background-color: var(--color-bg);
                background-image: radial-gradient(at 0% 0%, hsla(210, 100%, 85%, 1) 0, transparent 50%), radial-gradient(at 50% 0%, hsla(220, 100%, 90%, 1) 0, transparent 50%), radial-gradient(at 100% 0%, hsla(210, 100%, 85%, 1) 0, transparent 50%);
                background-size: cover; position: relative; overflow: hidden;
            }
            .guest-card {
                background: var(--material-thick); backdrop-filter: var(--backdrop-blur); -webkit-backdrop-filter: var(--backdrop-blur);
                border: 1px solid var(--color-border); box-shadow: var(--shadow-hover); border-radius: var(--radius-lg);
                padding: 48px; width: 100%; max-width: 440px; position: relative; z-index: 10;
                animation: slideUp 0.6s cubic-bezier(0.25, 1, 0.5, 1) forwards; opacity: 0; transform: translateY(20px) scale(0.98);
            }
            @keyframes slideUp { to { opacity: 1; transform: translateY(0) scale(1); } }
            .guest-logo { text-align: center; margin-bottom: 32px; font-size: 28px; font-weight: 700; letter-spacing: -0.03em; color: var(--color-text-main); }
        </style>
    </head>
    <body>
        <div class="guest-wrapper">
            <div class="guest-card">
                <div class="guest-logo">CONNECT</div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="form-group mb-4">
            <label for="email" class="form-label">{{ __('Email Address') }}</label>
            <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="admin@connect.ly" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger text-sm" />
        </div>

        <!-- Password -->
        <div class="form-group mb-4">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input id="password" class="form-input" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger text-sm" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between mb-6" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <label for="remember_me" class="inline-flex items-center" style="display: flex; align-items: center; cursor: pointer;">
                <input id="remember_me" type="checkbox" name="remember" style="width: 16px; height: 16px; border-radius: 4px; border: 1px solid var(--color-border); accent-color: var(--color-primary);">
                <span style="margin-left: 8px; font-size: 14px; font-weight: 500; color: var(--color-text-muted);">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" style="font-size: 13px; font-weight: 500; color: var(--color-primary); text-decoration: none;">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <!-- Log in Button -->
        <div>
            <button type="submit" class="btn btn-primary w-full" style="width: 100%; padding: 14px; font-size: 16px; font-weight: 600;">
                {{ __('Log In') }}
            </button>
        </div>
        
        <div style="text-align: center; margin-top: 24px; font-size: 14px; color: var(--color-text-muted);">
            Don't have an account? <a href="{{ route('register') }}" style="font-weight: 600; color: var(--color-text-main);">Sign up</a>
        </div>
    </form>
            </div>
        </div>
    </body>
</html>
