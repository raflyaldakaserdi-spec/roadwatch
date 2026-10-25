<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RoadWatch — Account</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* RoadWatch Guest Layout V1 */

        body.rw-auth-body {
            margin: 0;
            min-height: 100vh;
            min-height: 100dvh;
            font-family: Inter, ui-sans-serif, system-ui, sans-serif;
            color: #19243f;
            background:
                radial-gradient(ellipse at 8% 12%, #ffe1ef 0%, transparent 38%),
                radial-gradient(ellipse at 92% 85%, #eee1ff 0%, transparent 42%),
                #fffafd;
        }

        .rw-auth-shell {
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 18px;
        }

        .rw-auth-content {
            width: 100%;
            max-width: 560px;
        }

        .rw-auth-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 24px;
            text-decoration: none;
        }

        .rw-auth-brand img {
            width: 58px;
            height: 58px;
            object-fit: contain;
        }

        .rw-auth-brand-name {
            font-size: 25px;
            font-weight: 900;
            letter-spacing: -.9px;
            color: #17213c;
        }

        .rw-auth-brand-name span {
            color: #e974ab;
        }

        .rw-auth-brand-sub {
            margin-top: 3px;
            font-size: 10px;
            font-weight: 750;
            letter-spacing: 2px;
            color: #9299af;
        }

        .rw-auth-card {
            padding: clamp(24px, 4vw, 42px);
            background: rgba(255,255,255,.96);
            border: 1px solid #f5e3ee;
            border-radius: 28px;
            box-shadow: 0 24px 70px rgba(190, 112, 160, .10);
            animation: rwAuthEnter .5s ease both;
        }

        .rw-auth-card input[type="text"],
        .rw-auth-card input[type="email"],
        .rw-auth-card input[type="password"] {
            min-height: 46px;
            border-radius: 12px;
            border-color: #f0dce9;
            background: #f9f5ff;
        }

        .rw-auth-card input:focus {
            border-color: #e974ab;
            --tw-ring-color: rgba(233,116,171,.25);
        }

        .rw-auth-card button[type="submit"] {
            background: #e974ab;
            border-radius: 12px;
            padding: 12px 20px;
            transition: background .2s ease, transform .2s ease;
        }

        .rw-auth-card button[type="submit"]:hover {
            background: #d85c98;
            transform: translateY(-1px);
        }

        @keyframes rwAuthEnter {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 480px) {
            .rw-auth-shell {
                padding: 20px 12px;
            }

            .rw-auth-card {
                border-radius: 22px;
                padding: 24px 20px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .rw-auth-card {
                animation: none;
            }
        }
    </style>
</head>

<body class="rw-auth-body">
    <div class="rw-auth-shell">
        <div class="rw-auth-content">
            <a href="{{ route('login') }}" class="rw-auth-brand">
                <img
                    src="{{ asset('images/roadwatch-logo-pink.svg') }}"
                    alt="RoadWatch Logo">

                <div>
                    <div class="rw-auth-brand-name">
                        ROAD<span>WATCH</span>
                    </div>
                    <div class="rw-auth-brand-sub">
                        IOT ROAD MONITORING
                    </div>
                </div>
            </a>

            <main class="rw-auth-card">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
