<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'CONNECT') }} - Login</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css'])

        <style>
            /* Apple-style Gradient Mesh Background */
            .guest-wrapper {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                background-color: var(--color-bg);
                /* Beautiful soft gradient mesh */
                background-image: radial-gradient(at 0% 0%, hsla(210, 100%, 85%, 1) 0, transparent 50%), 
                                  radial-gradient(at 50% 0%, hsla(220, 100%, 90%, 1) 0, transparent 50%), 
                                  radial-gradient(at 100% 0%, hsla(210, 100%, 85%, 1) 0, transparent 50%);
                background-size: cover;
                position: relative;
                overflow: hidden;
            }

            .guest-card {
                background: var(--material-thick);
                backdrop-filter: var(--backdrop-blur);
                -webkit-backdrop-filter: var(--backdrop-blur);
                border: 1px solid var(--color-border);
                box-shadow: var(--shadow-hover);
                border-radius: var(--radius-lg);
                padding: 48px;
                width: 100%;
                max-width: 440px;
                position: relative;
                z-index: 10;
                /* Entry animation */
                animation: slideUp 0.6s cubic-bezier(0.25, 1, 0.5, 1) forwards;
                opacity: 0;
                transform: translateY(20px) scale(0.98);
            }

            @keyframes slideUp {
                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            .guest-logo {
                text-align: center;
                margin-bottom: 32px;
                font-size: 28px;
                font-weight: 700;
                letter-spacing: -0.03em;
                color: var(--color-text-main);
            }
        </style>
    </head>
    <body>
        <div class="guest-wrapper">
            <div class="guest-card">
                <div class="guest-logo">
                    CONNECT
                </div>
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
