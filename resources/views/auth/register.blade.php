<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Financial Transaction System') }} - Register</title>
        @vite(['resources/css/app.css'])
        <style>
            .guest-wrapper {
                min-height: 100vh; display: flex; flex-direction: column; justify-content: center; align-items: center;
                background-color: var(--color-bg);
                background-image: radial-gradient(at 0% 0%, hsla(210, 100%, 85%, 1) 0, transparent 50%), radial-gradient(at 50% 0%, hsla(220, 100%, 90%, 1) 0, transparent 50%), radial-gradient(at 100% 0%, hsla(210, 100%, 85%, 1) 0, transparent 50%);
                background-size: cover; position: relative; overflow: hidden; padding: 40px 0;
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
                <div class="guest-logo">FINANCIAL TRANSACTIONS</div>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div class="form-group mb-4">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <input id="name" class="form-input" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-danger text-sm" />
        </div>

        <!-- Email Address -->
        <div class="form-group mb-4">
            <label for="email" class="form-label">{{ __('Email Address') }}</label>
            <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger text-sm" />
        </div>

        <!-- Password -->
        <div class="form-group mb-4">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input id="password" class="form-input" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger text-sm" />
        </div>

        <!-- Confirm Password -->
        <div class="form-group mb-6">
            <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
            <input id="password_confirmation" class="form-input" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-danger text-sm" />
        </div>

        <div>
            <button type="submit" class="btn btn-primary w-full" style="width: 100%; padding: 14px; font-size: 16px; font-weight: 600;">
                {{ __('Register') }}
            </button>
        </div>

        <div style="text-align: center; margin-top: 24px; font-size: 14px; color: var(--color-text-muted);">
            Already have an account? <a href="{{ route('login') }}" style="font-weight: 600; color: var(--color-text-main);">Log in</a>
        </div>
    </form>
            </div>
        </div>
    </body>
</html>
