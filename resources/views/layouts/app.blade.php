<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full w-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ROADWATCH') }}</title>

    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow-x: hidden;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #FFF8FB;
        }
        .font-mono-tech {
            font-family: 'JetBrains Mono', monospace;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

<style>
/* RoadWatch sidebar — scoped styles */
.rw-nav {
    display:flex;
    align-items:center;
    gap:13px;
    width:100%;
    min-height:44px;
    padding:0 15px;
    border-radius:13px;
    color:#718096;
    font-size:12px;
    font-weight:700;
    text-decoration:none;
    transition:background .2s,color .2s;
}
.rw-nav i {
    width:18px;
    text-align:center;
    flex-shrink:0;
    font-size:13px;
}
.rw-nav:hover {
    background:#FFF0F6;
    color:#C76B99;
}
.rw-nav.rw-nav-active,
.rw-nav.rw-nav-active:hover {
    background:#FBE4EF;
    color:#B94F87;
    box-shadow:inset 3px 0 0 #E78AB5;
}
.rw-nav.rw-nav-active i {
    color:#D870A4;
}
</style>


@if(request()->routeIs('detections.*'))
<style>
/* RoadWatch Soft Pink Detection Filters */
input[type="search"]:focus,
input[type="text"]:focus,
select:focus {
    border-color:#E9A4C5 !important;
    box-shadow:0 0 0 3px rgba(232,121,170,.10) !important;
    outline:none !important;
}
</style>
@endif

<link rel="stylesheet" href="{{ asset('css/roadwatch-ambient.css') }}?v=2">
<link rel="stylesheet" href="{{ asset('css/roadwatch-mobile.css') }}?v=8">

<style>
/* RoadWatch Premium Sidebar Navigation V1 */

#rw-mobile-sidebar .rw-premium-nav {
    gap: 7px !important;
}

#rw-mobile-sidebar .rw-nav-section-label {
    padding: 0 13px;
    margin: 3px 0 9px;
    color: #B2A2AE;
    font-size: 9px;
    font-weight: 800;
    letter-spacing: 1.5px;
}

#rw-mobile-sidebar .rw-nav-preferences {
    margin-top: 18px;
}

#rw-mobile-sidebar .rw-nav {
    min-height: 48px;
    padding: 5px 11px;
    gap: 11px;
    border: 1px solid transparent;
    border-radius: 14px;
    transition:
        background .2s ease,
        color .2s ease,
        box-shadow .2s ease,
        transform .2s ease;
}

#rw-mobile-sidebar .rw-nav i {
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border-radius: 10px;
    background: #F8F2F6;
    color: #A89AA7;
    font-size: 13px;
    transition: background .2s ease, color .2s ease;
}

#rw-mobile-sidebar .rw-nav:hover {
    background: #FFF2F8;
    color: #C2548D;
    transform: translateX(2px);
}

#rw-mobile-sidebar .rw-nav:hover i {
    background: #FBE1EE;
    color: #CF6599;
}

#rw-mobile-sidebar .rw-nav.rw-nav-active {
    background: linear-gradient(110deg, #FCE4F0, #FFF0F7);
    color: #B94F87;
    border-color: #F6D4E5;
    box-shadow: 0 5px 15px rgba(214, 108, 157, .10);
    transform: none;
}

#rw-mobile-sidebar .rw-nav.rw-nav-active i {
    background: #FFFFFF;
    color: #D86A9E;
    box-shadow: 0 2px 7px rgba(214, 108, 157, .10);
}

#rw-mobile-sidebar .rw-nav:focus-visible {
    outline: 2px solid #D86A9E;
    outline-offset: 2px;
}

@media (prefers-reduced-motion: reduce) {
    #rw-mobile-sidebar .rw-nav,
    #rw-mobile-sidebar .rw-nav i {
        transition: none;
        transform: none;
    }
}
</style>


<style>
/* RoadWatch Smart Device Monitor V1 */
.rw-sidebar-device {
    background:linear-gradient(145deg,#FFF0F6,#FFF8FB);
    border:1px solid #F4D8E6;
    border-radius:18px;
    padding:14px;
    box-shadow:0 7px 20px rgba(210,110,160,.055);
}
.rw-sd-heading,.rw-sd-row {
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:6px;
}
.rw-sd-heading > span:first-child {
    font-size:11px;
    font-weight:800;
    color:#AC3973;
}
.rw-sd-heading i { margin-right:4px; }
.rw-sd-badge {
    border-radius:999px;
    padding:4px 7px;
    background:#F1F5F9;
    color:#64748B;
    font-size:9px;
    font-weight:800;
}
.rw-sidebar-device[data-online="true"] .rw-sd-badge {
    background:#DCFCE7;
    color:#15803D;
}
.rw-sidebar-device[data-online="false"] .rw-sd-badge {
    background:#FEE2E2;
    color:#B91C1C;
}
.rw-sd-device {
    display:flex;
    align-items:center;
    gap:9px;
    margin-top:16px;
}
.rw-sd-icon {
    width:35px;
    height:35px;
    flex-shrink:0;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:11px;
    background:#FBE0EC;
    color:#D76A9F;
}
.rw-sd-device strong {
    display:block;
    font-size:11px;
    color:#263047;
}
.rw-sd-device small {
    display:block;
    margin-top:3px;
    font-size:9px;
    color:#9A7A8A;
}
.rw-sd-divider {
    height:1px;
    background:#F1D5E3;
    margin:14px 0;
}
.rw-sd-row {
    margin-top:11px;
    font-size:10px;
    color:#806A78;
}
.rw-sd-row strong {
    font-size:10px;
    color:#475569;
    font-weight:800;
    text-align:right;
}
.rw-sd-link {
    display:flex;
    align-items:center;
    justify-content:center;
    gap:7px;
    margin-top:16px;
    padding:10px 7px;
    border-radius:11px;
    background:white;
    color:#B94F87;
    font-size:10px;
    font-weight:800;
    text-decoration:none;
    transition:background .2s ease;
}
.rw-sd-link:hover { background:#FCE4EF; }
.rw-sd-link:focus-visible {
    outline:2px solid #D76A9F;
    outline-offset:2px;
}
</style>


<style>
/* RoadWatch Sticky Sidebar Desktop V1 */

@media (min-width: 768px) {
    #rw-mobile-sidebar {
        position: sticky;
        top: 0;
        height: 100vh;
        height: 100dvh;
        min-height: 0 !important;
        max-height: 100vh;
        max-height: 100dvh;
        overflow-y: auto;
        overscroll-behavior: contain;
        scrollbar-width: thin;
        scrollbar-color: #F0C8DB transparent;
    }

    #rw-mobile-sidebar::-webkit-scrollbar {
        width: 4px;
    }

    #rw-mobile-sidebar::-webkit-scrollbar-thumb {
        background: #F0C8DB;
        border-radius: 999px;
    }
}
</style>


<style>
/* RoadWatch Premium Interactive Sidebar V1 */

/* Device Monitor: elevated interactive card */
#rwSidebarDevice {
    position: relative;
    overflow: hidden;
    transition:
        box-shadow 220ms ease,
        border-color 220ms ease,
        transform 220ms ease;
}

#rwSidebarDevice::after {
    content: "";
    position: absolute;
    width: 110px;
    height: 110px;
    top: -72px;
    right: -62px;
    border-radius: 50%;
    background: rgba(231, 138, 181, .11);
    pointer-events: none;
}

@media (hover: hover) and (pointer: fine) {
    #rwSidebarDevice:hover {
        transform: translateY(-2px);
        border-color: #EDBDD5;
        box-shadow: 0 12px 28px rgba(199, 93, 144, .12);
    }

    #rw-mobile-sidebar .rw-nav:hover i {
        transform: scale(1.06);
    }

    #rwSidebarDevice .rw-sd-link:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(199, 93, 144, .10);
    }
}

/* Status indicator */
#rwSidebarDevice .rw-sd-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}

#rwSidebarDevice .rw-sd-badge::after {
    content: "";
    width: 6px;
    height: 6px;
    flex-shrink: 0;
    border-radius: 50%;
    background: #94A3B8;
}

#rwSidebarDevice[data-online="true"]
.rw-sd-badge::after {
    background: #16A34A;
    box-shadow: 0 0 0 3px rgba(22, 163, 74, .12);
    animation: rwDevicePulse 2.4s ease-in-out infinite;
}

#rwSidebarDevice[data-online="false"]
.rw-sd-badge::after {
    background: #EF4444;
}

/* Sync error takes precedence over device status */
#rwSidebarDevice[data-sync-error="true"]
.rw-sd-badge::after {
    background: #EF4444;
    animation: none;
    box-shadow: none;
}

#rwSidebarDevice[data-online="true"]
[data-sd-connection] {
    color: #15803D;
}

#rwSidebarDevice[data-online="false"]
[data-sd-connection] {
    color: #B91C1C;
}

#rwSidebarDevice .rw-sd-link {
    position: relative;
    z-index: 1;
    transition:
        background 200ms ease,
        transform 200ms ease,
        box-shadow 200ms ease;
}

@keyframes rwDevicePulse {
    0%, 100% {
        box-shadow: 0 0 0 3px rgba(22, 163, 74, .12);
    }
    50% {
        box-shadow: 0 0 0 6px rgba(22, 163, 74, 0);
    }
}

@media (prefers-reduced-motion: reduce) {
    #rwSidebarDevice,
    #rwSidebarDevice .rw-sd-link,
    #rwSidebarDevice .rw-sd-badge::after,
    #rw-mobile-sidebar .rw-nav i {
        animation: none !important;
        transition: none !important;
        transform: none !important;
    }
}
</style>

</head>
<body class="antialiased text-slate-800 bg-[#FFF8FB] relative min-h-screen" x-data="{ sidebarOpen: false }">

<button id="rw-mobile-toggle"
        type="button"
        aria-label="Buka menu navigasi"
        aria-expanded="false"
        aria-controls="rw-mobile-sidebar"
        style="display:none">
    <i class="fa-solid fa-bars" aria-hidden="true"></i>
</button>

<div id="rw-mobile-overlay" aria-hidden="true"></div>


    <!-- 1. LAYER GAMBAR BACKGROUND LOKAL -->
    <div class="fixed inset-0 w-full h-full -z-10 bg-cover bg-center bg-no-repeat opacity-35 pointer-events-none filter blur-[0.5px]"
         style="background-image: url('{{ asset('images/bg-dashboard.jpg') }}');">
    </div>

    <!-- 2. LAYER OVERLAY GRADASI LEMBUT -->
    <div class="fixed inset-0 w-full h-full -z-10 bg-gradient-to-br from-[#FFF8FB]/70 via-[#FFF8FB]/80 to-[#FCEEF5]/85 pointer-events-none"></div>

    <!-- KONTEN UTAMA WEBSITE -->
    <div class="rw-app-shell min-h-screen flex relative w-full">
        
        <!-- Sidebar Navigation (Deep Navy #0F172A) -->
        
<!-- RoadWatch Soft Pink Sidebar -->
<aside id="rw-mobile-sidebar" class="w-64 shrink-0 min-h-screen flex flex-col justify-between z-30"
       style="background:#FFFCFE;border-right:1px solid #F2E4EB;box-shadow:2px 0 15px rgba(80,40,65,.025);">

    <div>
        <!-- Logo -->
        <div class="flex items-center gap-3 px-5"
             style="height:96px;min-height:96px;padding:20px;display:flex;align-items:center;box-sizing:border-box;border-bottom:1px solid #F5E9EF;">

            @if(file_exists(public_path('images/roadwatch-logo-pink.svg')))
                <img src="{{ asset('images/roadwatch-logo-pink.svg') }}"
                     alt="RoadWatch Logo"
                     style="width:48px;height:48px;object-fit:contain;flex-shrink:0;border-radius:13px;">
            @else
                <div style="width:48px;height:48px;flex-shrink:0;border-radius:13px;background:linear-gradient(135deg,#F5A6CA,#E67EAE);display:flex;align-items:center;justify-content:center;color:white;">
                    <i class="fa-solid fa-road"></i>
                </div>
            @endif

            <div style="min-width:0;">
                <div style="font-size:16px;font-weight:900;letter-spacing:-.5px;color:#17213C;white-space:nowrap;">
                    ROAD<span style="color:#DF78A6;">WATCH</span>
                </div>
                <div style="font-size:9px;font-weight:700;letter-spacing:.5px;color:#9A8A9A;white-space:nowrap;">
                    IoT ROAD MONITORING
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="rw-premium-nav" style="padding:24px 14px;display:flex;flex-direction:column;gap:7px;">
            <div class="rw-nav-section-label">MAIN MENU</div>

            <a href="{{ route('dashboard') }}"
               class="rw-nav {{ request()->routeIs('dashboard') ? 'rw-nav-active' : '' }}">
                <i class="fa-solid fa-house"></i>
                <span>Dashboard</span>
            </a>

            {{-- ROADWATCH_WORKER_SIDEBAR_V5 --}}
            @if(auth()->user()?->role === 'pekerja')
            <a href="{{ route('my-assignments.index') }}"
               class="rw-nav {{ request()->routeIs('my-assignments.*') ? 'rw-nav-active' : '' }}">
                <i class="fa-solid fa-clipboard-list"></i>
                <span>Penugasan Saya</span>
            </a>
            @endif

            <a href="{{ route('detections.index') }}"
               class="rw-nav {{ request()->routeIs('detections.*') ? 'rw-nav-active' : '' }}">
                <i class="fa-solid fa-table-cells"></i>
                <span>Detection Data</span>
            </a>

            <a href="{{ route('map.index') }}"
               class="rw-nav {{ request()->routeIs('map.*') ? 'rw-nav-active' : '' }}">
                <i class="fa-solid fa-map-location-dot"></i>
                <span>Map Monitoring</span>
            </a>

            @if(in_array(auth()->user()?->role, ['admin', 'pekerja'], true))
            <!-- ROADWATCH_ROLE_SIDEBAR_V3 -->
            <a href="{{ route('device.info') }}"
               class="rw-nav {{ request()->routeIs('device.info') ? 'rw-nav-active' : '' }}">
                <i class="fa-solid fa-microchip"></i>
                <span>Device Info</span>
            </a>
            @endif

            @can('viewActivityLogs')
            <a href="{{ route('activity-logs.index') }}"
               class="rw-nav {{ request()->routeIs('activity-logs.*') ? 'rw-nav-active' : '' }}">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Log History</span>
            </a>
            @endcan

            <!-- RoadWatch Approval Center Sidebar V1 -->
            @can('manageRoleRequests')
            <a href="{{ route('approval-center.index') }}"
               class="rw-nav {{ request()->routeIs('approval-center.*') ? 'rw-nav-active' : '' }}">
                <i class="fa-solid fa-user-shield"></i>
                <span>Approval Center</span>
            </a>
            @endcan

            <!-- ROADWATCH_ASSIGNMENT_SIDEBAR_V6 -->
            @can('manageRoleRequests')
            <a href="{{ route('assignments.index') }}"
               class="rw-nav {{ request()->routeIs('assignments.*') ? 'rw-nav-active' : '' }}">
                <i class="fa-solid fa-clipboard-list"></i>
                <span>Assignment Center</span>
            </a>
            @endcan

            <!-- RoadWatch User Management Sidebar V1 -->
            @can('manageRoleRequests')
            <a href="{{ route('user-management.index') }}"
               class="rw-nav {{ request()->routeIs('user-management.*') ? 'rw-nav-active' : '' }}">
                <i class="fa-solid fa-users-gear"></i>
                <span>User Management</span>
            </a>
            @endcan

            <div class="rw-nav-section-label rw-nav-preferences">PREFERENCES</div>

            <a href="{{ route('profile.edit') }}"
               class="rw-nav {{ request()->routeIs('profile.*') ? 'rw-nav-active' : '' }}">
                <i class="fa-solid fa-gear"></i>
                <span>Settings</span>
            </a>

        </nav>
    </div>

    <!-- Bottom -->
    <div style="padding:14px;display:flex;flex-direction:column;gap:12px;">

        
        {{-- ROADWATCH_PUBLIC_UI_V1 --}}
        @if(auth()->user()?->role === 'masyarakat')
        <div class="rw-sidebar-device">
            <div class="rw-sd-heading">
                <span>
                    <i class="fa-solid fa-shield-heart"></i>
                    Panduan Pengguna Jalan
                </span>
                <span class="rw-sd-badge">Info</span>
            </div>

            <div class="rw-sd-device">
                <div class="rw-sd-icon">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <div>
                    <strong>Pantau Kondisi Jalan</strong>
                    <small>RoadWatch Road Monitoring</small>
                </div>
            </div>

            <div class="rw-sd-divider"></div>

            <p style="font-size:10px;color:#64748B;line-height:1.7;margin-bottom:10px;">
                Ketahui lokasi kerusakan jalan sebelum melakukan perjalanan.
            </p>

            <div class="rw-sd-row">
                <span><i class="fa-solid fa-circle-exclamation text-rose-400"></i> High</span>
                <strong style="color:#E11D48;">Bahaya Tinggi</strong>
            </div>

            <div class="rw-sd-row">
                <span><i class="fa-solid fa-circle-exclamation text-amber-400"></i> Medium</span>
                <strong style="color:#D97706;">Waspada</strong>
            </div>

            <div class="rw-sd-row">
                <span><i class="fa-solid fa-circle-info text-sky-400"></i> Low</span>
                <strong style="color:#0284C7;">Bahaya Rendah</strong>
            </div>

            <a class="rw-sd-link" href="{{ route('map.index') }}">
                Lihat Peta Monitoring
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </a>
        </div>
        @else
<!-- RoadWatch Smart Device Monitor V1 -->
        <div id="rwSidebarDevice" class="rw-sidebar-device">
            <div class="rw-sd-heading">
                <span><i class="fa-solid fa-wave-square"></i> Device Monitor</span>
                <span class="rw-sd-badge" data-sd-badge>Checking</span>
            </div>

            <div class="rw-sd-device">
                <div class="rw-sd-icon"><i class="fa-solid fa-microchip"></i></div>
                <div>
                    <strong data-sd-mcu>ESP32-S3 CAM</strong>
                    <small>RoadWatch Edge Device</small>
                </div>
            </div>

            <div class="rw-sd-divider"></div>

            <div class="rw-sd-row">
                <span>Connection</span>
                <strong data-sd-connection>Checking...</strong>
            </div>

            <div class="rw-sd-row">
                <span>GPS Status</span>
                <strong data-sd-gps>Unknown</strong>
            </div>

            <div class="rw-sd-row">
                <span>Last Heartbeat</span>
                <strong data-sd-heartbeat>—</strong>
            </div>

            <a class="rw-sd-link" href="{{ route('device.info') }}">
                View Device Details
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </a>
        </div>


                @endif

        <div style="background:#FFFFFF;border:1px solid #F3E4EB;border-radius:15px;padding:12px;display:flex;align-items:center;gap:9px;">
            <span style="width:8px;height:8px;border-radius:50%;background:#77C9A1;flex-shrink:0;"></span>
            <div>
                <p style="font-size:10px;font-weight:800;color:#17213C;">RoadWatch Platform</p>
                <p style="font-size:9px;color:#A0A5B5;">Monitoring Dashboard</p>
            </div>
        </div>

    </div>

<!-- RW MOBILE LOGOUT -->
<div class="rw-mobile-logout">
    <form method="POST" action="{{ route('logout') }}">
        @csrf

        <button type="submit" class="rw-mobile-logout-btn">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Log Out</span>
        </button>
    </form>
</div>

</aside>


        <!-- Main Content Area -->
        <div class="rw-ambient-main flex-1 min-w-0 overflow-y-auto p-8">
            {{ $slot }}
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('rw-mobile-toggle');
    const overlay = document.getElementById('rw-mobile-overlay');
    const sidebar = document.getElementById('rw-mobile-sidebar');

    if (!toggle || !overlay || !sidebar) return;

    function setMenu(open) {
        document.body.classList.toggle('rw-menu-open', open);

        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute(
            'aria-label',
            open ? 'Tutup menu navigasi' : 'Buka menu navigasi'
        );

        toggle.innerHTML = open
            ? '<i class="fa-solid fa-xmark" aria-hidden="true"></i>'
            : '<i class="fa-solid fa-bars" aria-hidden="true"></i>';
    }

    toggle.addEventListener('click', function () {
        setMenu(!document.body.classList.contains('rw-menu-open'));
    });

    overlay.addEventListener('click', function () {
        setMenu(false);
    });

    sidebar.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            setMenu(false);
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') setMenu(false);
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth >= 768) setMenu(false);
    });
});
</script>


<script>
// RoadWatch Global Device Sync V1
document.addEventListener('DOMContentLoaded', function () {
    const card = document.getElementById('rwSidebarDevice');
    if (!card) return;

    const isDashboard = {{ request()->routeIs('dashboard') ? 'true' : 'false' }};

    // Dashboard sudah memperbarui sidebar melalui polling sendiri.
    if (isDashboard) return;

    const endpoint = @json(route('device-status.live'));
    let requestInProgress = false;

    function setText(selector, value) {
        const element = card.querySelector(selector);
        if (element) element.textContent = value;
    }

    function updateCard(device) {
        const online = device.online === true;

        card.dataset.online = String(online);
        card.dataset.syncError = 'false';

        setText('[data-sd-badge]', online ? 'Online' : 'Offline');
        setText('[data-sd-mcu]', device.mcu || 'ESP32-S3 CAM');
        setText(
            '[data-sd-connection]',
            online ? 'Connected' : 'Disconnected'
        );
        setText(
            '[data-sd-gps]',
            online ? (device.gps_status || 'Unknown') : 'Unknown'
        );

        let heartbeat = 'Belum tersedia';

        if (device.last_seen_at) {
            const date = new Date(device.last_seen_at);

            if (!Number.isNaN(date.getTime())) {
                heartbeat = new Intl.DateTimeFormat('id-ID', {
                    timeZone: 'Asia/Jakarta',
                    day: '2-digit',
                    month: 'short',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                }).format(date) + ' WIB';
            }
        }

        setText('[data-sd-heartbeat]', heartbeat);
        card.title = 'Status perangkat berhasil disinkronisasi pada ' +
            new Intl.DateTimeFormat('id-ID', {
                timeZone: 'Asia/Jakarta',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            }).format(new Date()) + ' WIB';
    }

    async function refreshDeviceStatus() {
        if (document.visibilityState !== 'visible' || requestInProgress) {
            return;
        }

        requestInProgress = true;

        try {
            const response = await fetch(endpoint, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
                cache: 'no-store'
            });

            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }

            const device = await response.json();

            if (!device || typeof device.online !== 'boolean') {
                throw new Error('Invalid device status response');
            }

            updateCard(device);
        } catch (error) {
            card.dataset.syncError = 'true';
            card.title = 'Gagal memperbarui status perangkat';

            console.error('RoadWatch Device Sync failed:', error);
        } finally {
            requestInProgress = false;
        }
    }

    refreshDeviceStatus();
    setInterval(refreshDeviceStatus, 10000);

    document.addEventListener('visibilitychange', function () {
        if (document.visibilityState === 'visible') {
            refreshDeviceStatus();
        }
    });
});
</script>

<style>
/* RoadWatch Global Device Sync V1 */
.rw-sidebar-device[data-sync-error="true"] {
    border-color: #FCA5A5;
}

.rw-sidebar-device[data-sync-error="true"] .rw-sd-badge {
    background: #FEF2F2;
    color: #B91C1C;
}

.rw-sidebar-device[data-sync-error="true"] .rw-sd-badge::before {
    content: "!";
    margin-right: 4px;
}
</style>

</body>
</html>
