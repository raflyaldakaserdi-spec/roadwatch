<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full w-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
            background-color: #FAF8FF;
        }
        .font-mono-tech {
            font-family: 'JetBrains Mono', monospace;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased text-slate-800 bg-[#FAF8FF] relative min-h-screen" x-data="{ sidebarOpen: false }">

    <!-- 1. LAYER GAMBAR BACKGROUND LOKAL (PAS 100% SCREEN TANPA CELAH/POTONGAN) -->
    <div class="fixed inset-0 w-full h-full -z-10 bg-cover bg-center bg-no-repeat opacity-35 pointer-events-none filter blur-[0.5px]"
         style="background-image: url('{{ asset('images/bg-dashboard.jpg') }}');">
    </div>

    <!-- 2. LAYER OVERLAY GRADASI LEMBUT (SOFT BLEND) -->
    <div class="fixed inset-0 w-full h-full -z-10 bg-gradient-to-br from-[#FAF8FF]/70 via-[#FAF8FF]/80 to-[#F3EEFF]/85 pointer-events-none"></div>

    <!-- KONTEN UTAMA WEBSITE -->
    <div class="min-h-screen flex relative z-10 w-full">
        
        <!-- Sidebar Navigation (Deep Navy #0F172A) -->
        <aside class="w-64 bg-[#0F172A] text-white flex flex-col justify-between shrink-0 min-h-screen shadow-2xl z-30">
            <div>
                <!-- Brand Logo ROADWATCH -->
                <div class="flex items-center gap-3 h-20 px-6 border-b border-slate-800/80">
                    <div class="p-2.5 bg-gradient-to-tr from-violet-600 to-indigo-500 rounded-xl text-white shadow-lg shadow-indigo-500/30">
                        <i class="fa-solid fa-road-lock text-lg"></i>
                    </div>
                    <div>
                        <h1 class="font-extrabold text-base tracking-wider text-white">ROAD<span class="text-violet-400">WATCH</span></h1>
                        <p class="text-[9px] text-slate-400 font-semibold uppercase tracking-widest">IoT ROAD MONITORING</p>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="px-4 py-6 space-y-2">
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center gap-3.5 px-4 py-3 text-xs font-bold rounded-2xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-violet-600 to-indigo-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                        <i class="fa-solid fa-house w-4 text-center"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('detections.index') }}" 
                       class="flex items-center gap-3.5 px-4 py-3 text-xs font-bold rounded-2xl transition-all duration-200 {{ request()->routeIs('detections.*') ? 'bg-gradient-to-r from-violet-600 to-indigo-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                        <i class="fa-solid fa-table-cells w-4 text-center"></i>
                        <span>Detection Data</span>
                    </a>

                    <a href="{{ route('map.index') }}" 
                       class="flex items-center gap-3.5 px-4 py-3 text-xs font-bold rounded-2xl transition-all duration-200 {{ request()->routeIs('map.*') ? 'bg-gradient-to-r from-violet-600 to-indigo-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                        <i class="fa-solid fa-map-location-dot w-4 text-center"></i>
                        <span>Map Monitoring</span>
                    </a>

                    <a href="{{ route('device.info') }}" 
                       class="flex items-center gap-3.5 px-4 py-3 text-xs font-bold rounded-2xl transition-all duration-200 {{ request()->routeIs('device.info') ? 'bg-gradient-to-r from-violet-600 to-indigo-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                        <i class="fa-solid fa-microchip w-4 text-center"></i>
                        <span>Device Info</span>
                    </a>

                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center gap-3.5 px-4 py-3 text-xs font-bold rounded-2xl transition-all duration-200 {{ request()->routeIs('profile.edit') ? 'bg-gradient-to-r from-violet-600 to-indigo-500 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                        <i class="fa-solid fa-gear w-4 text-center"></i>
                        <span>Settings</span>
                    </a>
                </nav>
            </div>

            <!-- Sidebar Bottom Illustration & System Status Widget -->
            <div class="p-4 space-y-3 mt-auto">
                <div class="relative rounded-2xl bg-gradient-to-b from-slate-900 to-slate-950 border border-slate-800/80 p-4 text-center overflow-hidden">
                    <div class="mb-2">
                        <i class="fa-solid fa-car-side text-2xl text-violet-400"></i>
                    </div>
                    <p class="text-[11px] font-bold text-slate-300 leading-tight">Smarter Roads</p>
                    <p class="text-[11px] font-bold text-violet-400 leading-tight">Safer Tomorrow</p>
                </div>

                <div class="p-3 bg-slate-900/90 border border-slate-800 rounded-2xl flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <div>
                            <p class="text-[10px] font-bold text-slate-200">System Status</p>
                            <p class="text-[9px] text-slate-400">All Systems Operational</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-slate-500 text-[10px]"></i>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 min-w-0 overflow-y-auto p-8">
            {{ $slot }}
        </div>
    </div>
</body>
</html>