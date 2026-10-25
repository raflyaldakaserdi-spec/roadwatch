<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sign In — RoadWatch</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Inter, ui-sans-serif, system-ui, sans-serif;
            background: #fdf5f9;
            color: #17213c;
        }

        .shell {
            width: 100%;
            max-width: 1800px;
            min-height: 100vh;
            min-height: 100dvh;
            margin: 0 auto;
            padding: clamp(16px, 3vw, 48px);
            display: grid;
            grid-template-columns: minmax(380px, 36%) minmax(0, 64%);
            gap: 16px;
            align-items: stretch;
        }

        /* LEFT LOGIN PANEL */

        .login {
            background: #ffffff;
            border: 1px solid #f5e7ef;
            border-radius: 30px;
            padding: clamp(28px, 3.5vw, 58px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            box-shadow: 0 15px 45px rgba(183, 113, 149, .05);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 44px;
        }

        .brand img {
            width: 66px;
            height: 66px;
            object-fit: contain;
            border-radius: 18px;
        }

        .brand-title {
            font-size: 24px;
            font-weight: 900;
            letter-spacing: -.8px;
            color: #14203b;
        }

        .brand-title span {
            color: #eb76ae;
        }

        .brand-sub {
            margin-top: 4px;
            color: #9198ad;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            width: fit-content;
            padding: 9px 14px;
            border-radius: 30px;
            background: #fff0f6;
            color: #e678a8;
            font-size: 12px;
            font-weight: 750;
            margin-bottom: 22px;
        }

        .heading {
            font-size: clamp(30px, 3vw, 43px);
            font-weight: 850;
            letter-spacing: -1.5px;
            margin: 0 0 10px;
            line-height: 1.2;
        }

        .heading span {
            color: #ec6fa8;
        }

        .desc {
            color: #9299ad;
            font-size: 14px;
            line-height: 1.7;
            margin: 0 0 35px;
        }

        .field-label {
            display: block;
            margin-bottom: 10px;
            color: #17213c;
            font-size: 13px;
            font-weight: 750;
        }

        .field {
            position: relative;
            margin-bottom: 24px;
        }

        .field input {
            width: 100%;
            height: 54px;
            padding: 0 48px;
            border: 1px solid #e9dfe8;
            border-radius: 13px;
            background: #fffcfe;
            outline: none;
            color: #17213c;
            font-size: 14px;
            transition: border-color .2s, box-shadow .2s;
        }

        .field input::placeholder {
            color: #aab0c1;
        }

        .field input:focus {
            border-color: #e6a0c1;
            box-shadow: 0 0 0 4px rgba(230, 160, 193, .12);
        }

        .field-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #8994ab;
            font-size: 17px;
            pointer-events: none;
        }

        .toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #d77ba4;
            font-size: 12px;
            font-weight: 750;
            cursor: pointer;
        }

        .options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 27px;
            font-size: 12px;
        }

        .options label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #7d859b;
            cursor: pointer;
        }

        .options input {
            accent-color: #e77eaf;
        }

        a {
            color: #e678a8;
            text-decoration: none;
            font-weight: 750;
        }

        a:hover {
            color: #d95493;
        }

        .submit {
            width: 100%;
            padding: 16px;
            border: 0;
            border-radius: 13px;
            background: linear-gradient(100deg, #f2a7cc, #e67daf);
            color: #ffffff;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 12px 25px rgba(226, 125, 173, .16);
            transition: transform .2s, box-shadow .2s;
        }

        .submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 28px rgba(226, 125, 173, .23);
        }

        .register {
            margin-top: 28px;
            text-align: center;
            color: #9299ad;
            font-size: 13px;
        }

        .bottom-note {
            margin-top: 28px;
            text-align: center;
            color: #b2b5c2;
            font-size: 11px;
        }

        .errors {
            margin: -16px 0 18px;
            color: #d44d75;
            font-size: 12px;
        }

        .status {
            margin-bottom: 18px;
            padding: 12px;
            border-radius: 12px;
            background: #edf9f0;
            color: #287a4a;
            font-size: 13px;
        }

        /* RIGHT ILLUSTRATION PANEL */

        .scene {
            position: relative;
            overflow: hidden;
            border-radius: 30px;
            border: 1px solid #f3e5ed;
            background: #fceff5;
            min-height: 620px;
        }

        .scene img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
        }

        /* RESPONSIVE */

        @media (max-width: 950px) {
            .shell {
                grid-template-columns: 1fr;
                padding: 12px;
            }

            .login {
                min-height: calc(100dvh - 24px);
                padding: 30px;
            }

            .scene {
                display: block;
                position: relative;
                width: 100%;
                min-height: 0;
                height: auto;
                aspect-ratio: 16 / 10;
                border-radius: 24px;
                overflow: hidden;
                background: #fceff5;
            }

            .scene img {
                position: relative;
                inset: auto;
                display: block;
                width: 100%;
                height: 100%;
                object-fit: contain;
                object-position: center;
            }

            .brand {
                margin-bottom: 42px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .submit {
                transition: none;
            }
        }
    
        /* ROADWATCH SMALL MOBILE LOGIN */
        @media (max-width: 480px) {
            .shell {
                gap: 12px;
                padding: 12px;
            }

            .login {
                min-height: auto;
                padding: 24px 20px;
                border-radius: 24px;
            }

            .scene {
                aspect-ratio: 4 / 3;
                border-radius: 22px;
            }

            .scene img {
                object-fit: contain;
            }
        }

    
        /* ROADWATCH GOOGLE LOGIN */
        .rw-google-login {
            width: 100%;
            margin-top: 20px;
            margin-bottom: 18px;
        }

        .rw-google-divider {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 16px;
            color: #a89aa5;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.5px;
            white-space: nowrap;
        }

        .rw-google-divider::before,
        .rw-google-divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #f0e2e9;
        }

        .rw-google-button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            min-height: 50px;
            padding: 12px 18px;
            background: #ffffff;
            color: #273047;
            border: 1px solid #ebdce5;
            border-radius: 14px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: background .2s ease, border-color .2s ease;
        }

        .rw-google-button:hover {
            background: #fff4f9;
            border-color: #e8b6ce;
        }

        .rw-google-button:focus-visible {
            outline: 3px solid #df78a6;
            outline-offset: 3px;
        }

        .rw-google-button svg {
            flex-shrink: 0;
        }


    
        
        /* RoadWatch Balanced Premium Login V4 */

        body {
            min-height: 100dvh;
            background:
                radial-gradient(circle at 8% 15%, #ffe3f0, transparent 38%),
                radial-gradient(circle at 90% 80%, #f2e7ff, transparent 40%),
                #fdf7fb;
        }

        .shell {
            width: 100%;
            max-width: 1680px;
            min-height: 100dvh;
            margin: 0 auto;
            padding: clamp(16px, 2vw, 32px);
            display: grid;
            grid-template-columns: minmax(340px, 36%) minmax(0, 1fr);
            align-items: center;
            gap: 16px;
        }

        .login {
            min-width: 0;
            min-height: 0;
            padding: clamp(24px, 2.5vw, 38px);
            border-radius: 26px;
            justify-content: center;
            box-shadow: 0 18px 55px rgba(175, 95, 145, .08);
            animation: rwBalancedEnter .75s ease-out both;
        }

        .brand {
            margin-bottom: 26px;
        }

        .brand img {
            width: 58px;
            height: 58px;
        }

        .pill {
            margin-bottom: 16px;
        }

        .heading {
            font-size: clamp(30px, 2.8vw, 40px);
        }

        .scene {
            min-width: 0;
            min-height: 0;
            height: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 14px;
            padding: 16px;
            border-radius: 26px;
            overflow: hidden;
            background: linear-gradient(145deg, #fff0f7, #f4e9ff);
            box-shadow: 0 18px 55px rgba(175, 95, 145, .06);
            animation: rwBalancedEnter .9s ease-out both;
        }

        .scene img {
            position: relative;
            inset: auto;
            display: block;
            width: 100%;
            height: auto;
            max-height: none;
            aspect-ratio: 1672 / 941;
            object-fit: contain;
            object-position: center;
            border-radius: 18px;
            animation: none;
        }

        .scene::after {
            content: "SMARTER ROADS  •  SAFER TOMORROW";
            position: relative;
            inset: auto;
            display: block;
            width: 100%;
            text-align: center;
            color: #b75b8d;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .16em;
            background: none;
            pointer-events: none;
        }

        @keyframes rwBalancedEnter {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 950px) {
            .shell {
                display: flex;
                flex-direction: column;
                min-height: 100dvh;
                padding: 12px;
                gap: 14px;
            }

            .login {
                width: 100%;
                min-height: 0;
                padding: clamp(24px, 5vw, 36px);
            }

            .scene {
                width: 100%;
                min-height: 0;
                height: auto;
                aspect-ratio: auto;
                padding: 12px;
            }

            .scene img {
                width: 100%;
                height: auto;
                aspect-ratio: 1672 / 941;
            }
        }

        @media (max-width: 480px) {
            .shell {
                gap: 12px;
                padding: 12px;
            }

            .login {
                padding: 24px 20px;
                border-radius: 22px;
            }

            .brand {
                margin-bottom: 24px;
            }

            .scene {
                border-radius: 22px;
                padding: 8px;
            }

            .scene img {
                border-radius: 14px;
            }

            .scene::after {
                font-size: 9px;
                letter-spacing: .08em;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .login,
            .scene {
                animation: none !important;
            }
        }

    
        /* RoadWatch Product Showcase V5 */

        @media (min-width: 951px) {
            .shell {
                align-items: stretch;
            }
        }

        .scene {
            min-height: 0;
            height: auto;
            align-self: stretch;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 18px;
            padding: clamp(18px, 2vw, 30px);
            background:
                radial-gradient(circle at 80% 10%, rgba(255,255,255,.9), transparent 42%),
                linear-gradient(145deg, #fff0f7, #f3e8ff);
        }

        .scene::after {
            content: none;
        }

        .rw-showcase-top {
            display: flex;
            align-items: center;
            gap: 9px;
            color: #ad5d90;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .rw-showcase-indicator {
            width: 9px;
            height: 9px;
            flex-shrink: 0;
            border-radius: 50%;
            background: #e76fa8;
            box-shadow: 0 0 0 5px rgba(231,111,168,.12);
        }

        .rw-showcase-label {
            margin-left: auto;
            color: #9e91ae;
            white-space: nowrap;
        }

        .rw-showcase-image {
            width: 100%;
            overflow: hidden;
            border-radius: 18px;
            box-shadow: 0 16px 40px rgba(160,92,139,.12);
        }

        .scene .rw-showcase-image img {
            position: static;
            inset: auto;
            display: block;
            width: 100%;
            height: auto;
            aspect-ratio: 1672 / 941;
            object-fit: contain;
            border-radius: 0;
            box-shadow: none;
            animation: none;
        }

        .rw-showcase-description {
            text-align: center;
        }

        .rw-showcase-description h2 {
            margin: 0 0 8px;
            color: #202945;
            font-size: clamp(22px, 2vw, 30px);
            font-weight: 850;
            letter-spacing: -.8px;
            line-height: 1.2;
        }

        .rw-showcase-description h2 span {
            color: #e46ca7;
        }

        .rw-showcase-description p {
            max-width: 530px;
            margin: 0 auto;
            color: #8f88a0;
            font-size: 12px;
            line-height: 1.6;
        }

        .rw-showcase-cards {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .rw-showcase-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            min-width: 0;
            padding: 14px 8px;
            background: rgba(255,255,255,.78);
            border: 1px solid #f1dce9;
            border-radius: 15px;
            text-align: center;
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .rw-showcase-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(170,94,146,.1);
        }

        .rw-card-symbol {
            display: grid;
            place-items: center;
            width: 34px;
            height: 34px;
            border-radius: 11px;
            background: #ffe3f0;
            color: #d85e9b;
            font-size: 22px;
        }

        .rw-showcase-card strong {
            color: #30314e;
            font-size: 11px;
        }

        .rw-showcase-card small {
            color: #938da2;
            font-size: 10px;
        }

        @media (max-width: 950px) {
            .scene {
                align-self: auto;
                padding: 18px;
                gap: 16px;
                aspect-ratio: auto;
            }
        }

        @media (max-width: 480px) {
            .scene {
                padding: 12px;
                gap: 14px;
            }

            .rw-showcase-top {
                font-size: 8px;
                letter-spacing: .5px;
            }

            .rw-showcase-cards {
                gap: 6px;
            }

            .rw-showcase-card {
                padding: 10px 4px;
            }

            .rw-showcase-card strong {
                font-size: 9px;
            }

            .rw-showcase-card small {
                font-size: 8px;
            }

            .rw-showcase-description h2 {
                font-size: 21px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .rw-showcase-card {
                transition: none;
            }
        }

    
        /* RoadWatch Showcase Refinement V6 */

        .rw-showcase-description h2 {
            font-size: clamp(20px, 1.7vw, 27px);
            margin-bottom: 6px;
        }

        .rw-showcase-description p {
            font-size: 12px;
            line-height: 1.6;
        }

        .rw-showcase-cards {
            width: 100%;
            max-width: 680px;
            margin: 0 auto;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .rw-showcase-card {
            min-height: 98px;
            justify-content: center;
            padding: 14px 10px;
            border-radius: 16px;
        }

        .rw-showcase-card strong {
            font-size: 12px;
        }

        .rw-showcase-card small {
            font-size: 10px;
        }

        @media (max-width: 480px) {
            .rw-showcase-cards {
                gap: 6px;
            }

            .rw-showcase-card {
                min-height: 85px;
                padding: 10px 4px;
            }

            .rw-showcase-card strong {
                font-size: 10px;
            }

            .rw-showcase-card small {
                font-size: 9px;
            }
        }

    </style>
</head>

<body>

<div class="shell">

    <main class="login">

        <div class="brand">
            <img
                src="{{ asset('images/roadwatch-logo-pink.svg') }}"
                alt="RoadWatch Logo">

            <div>
                <div class="brand-title">
                    ROAD<span>WATCH</span>
                </div>
                <div class="brand-sub">
                    IOT ROAD MONITORING
                </div>
            </div>
        </div>

        <div class="pill">
            <span aria-hidden="true">✦</span>
            Secure Access
        </div>

        <h1 class="heading">
            Welcome <span>back!</span>
        </h1>

        <p class="desc">
            Sign in to access your RoadWatch dashboard.
        </p>

        @if(session('status'))
            <div class="status" role="status">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <label for="email" class="field-label">
                Email Address
            </label>

            <div class="field">
                <span class="field-icon" aria-hidden="true">✉</span>

                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    placeholder="Enter your email address"
                    autocomplete="username"
                    required
                    autofocus>
            </div>

            @error('email')
                <div class="errors" role="alert">
                    {{ $message }}
                </div>
            @enderror

            <label for="password" class="field-label">
                Password
            </label>

            <div class="field">
                <span class="field-icon" aria-hidden="true">♙</span>

                <input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="Enter your password"
                    autocomplete="current-password"
                    required>

                <button
                    type="button"
                    class="toggle"
                    id="toggle"
                    aria-label="Tampilkan password">
                    Show
                </button>
            </div>

            @error('password')
                <div class="errors" role="alert">
                    {{ $message }}
                </div>
            @enderror

            <div class="options">

                <label>
                    <input
                        type="checkbox"
                        name="remember"
                        value="1"
                        {{ old('remember') ? 'checked' : '' }}>
                    Remember me
                </label>

                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif

            </div>

            <button class="submit" type="submit">
                Sign In &nbsp; →
            </button>

        </form>

        @if(Route::has('register'))
            <div class="register">
                Don't have an account?
                <a href="{{ route('register') }}">
                    Register here
                </a>
            </div>
        @endif

        
        <!-- ROADWATCH GOOGLE LOGIN -->
        <div class="rw-google-login">
            <div class="rw-google-divider">
                <span>OR CONTINUE WITH</span>
            </div>

            <a href="{{ route('google.redirect') }}"
               class="rw-google-button">
                <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill="#4285F4" d="M21.35 12.24c0-.7-.06-1.37-.18-2.02H12v3.82h5.24a4.48 4.48 0 0 1-1.94 2.94v2.45h3.14c1.84-1.7 2.91-4.2 2.91-7.19z"/>
                    <path fill="#34A853" d="M12 21.75c2.62 0 4.82-.87 6.43-2.32l-3.14-2.45c-.87.59-1.98.94-3.29.94-2.53 0-4.67-1.71-5.43-4.01H3.34v2.53A9.75 9.75 0 0 0 12 21.75z"/>
                    <path fill="#FBBC05" d="M6.57 13.91a5.85 5.85 0 0 1 0-3.82V7.56H3.34a9.75 9.75 0 0 0 0 8.88z"/>
                    <path fill="#EA4335" d="M12 6.08c1.43 0 2.71.49 3.72 1.45l2.79-2.79A9.32 9.32 0 0 0 12 2.25a9.75 9.75 0 0 0-8.66 5.31l3.23 2.53c.76-2.3 2.9-4.01 5.43-4.01z"/>
                </svg>
                <span>Continue with Google</span>
            </a>
        </div>

<div class="bottom-note">
            Secure access to your RoadWatch dashboard
        </div>

    </main>

    <aside class="scene" aria-label="RoadWatch AIoT product showcase">

    <div class="rw-showcase-top">
        <span class="rw-showcase-indicator"></span>
        <span>INTELLIGENT ROAD MONITORING</span>
        <span class="rw-showcase-label">ROADWATCH AIoT</span>
    </div>

    <div class="rw-showcase-image">
        <img
            src="{{ asset('images/roadwatch-login-premium.webp') }}"
            alt="RoadWatch AI pothole detection, GPS tracking and monitoring dashboard">
    </div>

    <div class="rw-showcase-description">
        <h2>Smarter Roads. <span>Safer Tomorrow.</span></h2>
        <p>Intelligent pothole detection powered by Edge AI, GPS tracking and real-time monitoring.</p>
    </div>

    <div class="rw-showcase-cards">
        <div class="rw-showcase-card">
            <span class="rw-card-symbol">◎</span>
            <strong>AI Detection</strong>
            <small>Edge Intelligence</small>
        </div>

        <div class="rw-showcase-card">
            <span class="rw-card-symbol">⌖</span>
            <strong>GPS Tracking</strong>
            <small>Location Mapping</small>
        </div>

        <div class="rw-showcase-card">
            <span class="rw-card-symbol">◉</span>
            <strong>Live Monitoring</strong>
            <small>Real-Time Insights</small>
        </div>
    </div>

</aside>

</div>

<script>
    const toggle = document.getElementById('toggle');
    const password = document.getElementById('password');

    toggle.addEventListener('click', function () {
        const visible = password.type === 'text';

        password.type = visible ? 'password' : 'text';
        toggle.textContent = visible ? 'Show' : 'Hide';

        toggle.setAttribute(
            'aria-label',
            visible ? 'Tampilkan password' : 'Sembunyikan password'
        );
    });
</script>

</body>
</html>
