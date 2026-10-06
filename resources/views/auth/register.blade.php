<x-guest-layout>
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
</x-guest-layout>
