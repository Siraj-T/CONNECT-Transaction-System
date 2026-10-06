<x-guest-layout>
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
</x-guest-layout>
