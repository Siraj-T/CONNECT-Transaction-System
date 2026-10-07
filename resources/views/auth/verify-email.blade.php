<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'CONNECT') }} - Auth</title>
        @vite(['resources/css/app.css'])
        <style>
            .guest-wrapper { min-height: 100vh; display: flex; flex-direction: column; justify-content: center; align-items: center; background-color: var(--color-bg); background-image: radial-gradient(at 0% 0%, hsla(210, 100%, 85%, 1) 0, transparent 50%), radial-gradient(at 50% 0%, hsla(220, 100%, 90%, 1) 0, transparent 50%), radial-gradient(at 100% 0%, hsla(210, 100%, 85%, 1) 0, transparent 50%); background-size: cover; position: relative; overflow: hidden; padding: 40px 0; }
            .guest-card { background: var(--material-thick); backdrop-filter: var(--backdrop-blur); -webkit-backdrop-filter: var(--backdrop-blur); border: 1px solid var(--color-border); box-shadow: var(--shadow-hover); border-radius: var(--radius-lg); padding: 48px; width: 100%; max-width: 440px; position: relative; z-index: 10; animation: slideUp 0.6s cubic-bezier(0.25, 1, 0.5, 1) forwards; opacity: 0; transform: translateY(20px) scale(0.98); }
            @keyframes slideUp { to { opacity: 1; transform: translateY(0) scale(1); } }
            .guest-logo { text-align: center; margin-bottom: 32px; font-size: 28px; font-weight: 700; letter-spacing: -0.03em; color: var(--color-text-main); }
        </style>
    </head>
    <body>
        <div class="guest-wrapper">
            <div class="guest-card">
                <div class="guest-logo">CONNECT</div>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    {{ __('Resend Verification Email') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
            </div>
        </div>
    </body>
</html>
