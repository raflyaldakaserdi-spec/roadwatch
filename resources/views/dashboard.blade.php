<x-app-layout>
    <!-- TOPBAR SEARCH & PROFILE HEADER -->
    <div class="rw-dashboard-header relative z-[100] flex items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ auth()->user()?->role === 'masyarakat' ? 'Dashboard Monitoring Jalan' : 'Dashboard Utama' }}</h1>
            <p class="text-xs text-slate-500 mt-0.5 font-medium">{{ auth()->user()?->role === 'masyarakat' ? 'Informasi kondisi jalan dan keselamatan pengguna jalan.' : 'Ringkasan kondisi jalan dan aktivitas sensor terkini.' }}</p>
            <div id="rwLiveSync" data-public-mode="{{ auth()->user()?->role === 'masyarakat' ? 'true' : 'false' }}"
                 class="rw-live-sync"
                 role="status"
                 aria-live="polite"
                 aria-atomic="true"
                 data-sync-state="waiting">
                <span class="rw-live-sync-dot" aria-hidden="true"></span>
                <span data-sync-label>{{ auth()->user()?->role === "masyarakat" ? "Memuat informasi jalan" : "Menunggu sinkronisasi" }}</span>
                <span data-sync-time></span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <!-- Search Bar -->
            <form method="GET"
                  action="{{ route('detections.index') }}"
                  role="search"
                  class="relative min-w-[320px]">

                <input
                    id="dashboardSearch"
                    type="search"
                    name="search"
                    maxlength="255"
                    required
                    placeholder="Cari lokasi atau kode deteksi..."
                    aria-label="Cari lokasi atau kode deteksi"
                    class="w-full pl-4 pr-10 py-2 bg-white border border-rose-100/80 rounded-2xl text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-pink-300 shadow-sm transition">

                <button
                    type="submit"
                    aria-label="Cari deteksi"
                    class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-pink-500 cursor-pointer">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </button>
            </form>

            <!-- Notification Bell Icon -->
            <div id="rwNotification" class="relative z-[110]">
                <button type="button"
                        id="rwNotificationButton"
                        aria-label="Buka notifikasi deteksi"
                        aria-expanded="false"
                        aria-controls="rwNotificationPanel"
                        class="w-9 h-9 rounded-2xl bg-white border border-rose-100/80 shadow-sm flex items-center justify-center text-slate-600 hover:text-pink-500 transition cursor-pointer">
                    <i class="fa-regular fa-bell text-sm"></i>
                </button>

                <div id="rwNotificationPanel"
                     hidden
                     class="absolute right-0 top-full mt-3 w-80 max-w-[90vw] bg-white border border-rose-100 rounded-2xl shadow-xl overflow-hidden"
                     style="z-index:9999;">

                    <div class="px-4 py-3 bg-pink-50 border-b border-rose-100 flex items-center justify-between">
                        <span class="text-sm font-extrabold text-slate-800">
                            <i class="fa-regular fa-bell text-pink-500 mr-2"></i>
                            Notifikasi Deteksi
                        </span>
                        <span class="text-[10px] font-bold text-pink-600">
                            {{ $recentDetections->count() }} terbaru
                        </span>
                    </div>

                    <div class="max-h-80 overflow-y-auto">
                        @forelse($recentDetections as $notification)
                            <a href="{{ route('detections.show', $notification->id) }}"
                               class="block px-4 py-3 border-b border-slate-100 hover:bg-pink-50 transition">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-xs font-extrabold text-slate-800">
                                        {{ $notification->detection_code }}
                                    </span>
                                    <span class="text-[10px] font-bold
                                        {{ $notification->severity === 'High' ? 'text-red-600' : ($notification->severity === 'Medium' ? 'text-orange-600' : 'text-blue-600') }}">
                                        {{ $notification->severity }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1 truncate">
                                    {{ $notification->location }}
                                </p>
                                <p class="text-[10px] text-slate-400 mt-1">
                                    {{ $notification->detection_date }}
                                    • {{ $notification->detection_time }} WIB
                                </p>
                            </a>
                        @empty
                            <p class="px-4 py-6 text-center text-xs text-slate-500">
                                Belum ada deteksi terbaru.
                            </p>
                        @endforelse
                    </div>

                    <a href="{{ route('detections.index') }}"
                       class="block text-center px-4 py-3 text-xs font-bold text-pink-600 hover:bg-pink-50">
                        Lihat Semua Deteksi →
                    </a>
                </div>
            </div>

            <script>
document.addEventListener('DOMContentLoaded', function () {
    const wrapper = document.getElementById('rwNotification');
    const button = document.getElementById('rwNotificationButton');
    const panel = document.getElementById('rwNotificationPanel');

    if (!wrapper || !button || !panel) return;

    // Move dropdown outside Dashboard stacking contexts.
    document.body.appendChild(panel);

    panel.style.position = 'fixed';
    panel.style.zIndex = '2147483647';
    panel.style.marginTop = '0';

    function positionPanel() {
        const rect = button.getBoundingClientRect();
        const width = Math.min(320, window.innerWidth - 24);

        panel.style.width = width + 'px';
        panel.style.maxWidth = 'none';

        const left = Math.max(
            12,
            Math.min(rect.right - width, window.innerWidth - width - 12)
        );

        panel.style.left = left + 'px';
        panel.style.right = 'auto';
        panel.style.top = (rect.bottom + 8) + 'px';
    }

    function closePanel() {
        panel.hidden = true;
        button.setAttribute('aria-expanded', 'false');
    }

    button.addEventListener('click', function () {
        if (panel.hidden) {
            positionPanel();
            panel.hidden = false;
            button.setAttribute('aria-expanded', 'true');
        } else {
            closePanel();
        }
    });

    document.addEventListener('click', function (event) {
        if (!wrapper.contains(event.target) &&
            !panel.contains(event.target)) {
            closePanel();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closePanel();
    });

    window.addEventListener('resize', function () {
        if (!panel.hidden) positionPanel();
    });

    window.addEventListener('scroll', function () {
        if (!panel.hidden) positionPanel();
    }, true);
});
</script>

            <!-- Profile User Icon -->
            <div id="rwProfile" class="relative">
                <button type="button"
                        id="rwProfileButton"
                        aria-label="Buka menu profil"
                        aria-expanded="false"
                        aria-controls="rwProfilePanel"
                        class="w-9 h-9 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-bold text-xs shadow-sm hover:bg-pink-500 transition cursor-pointer">
                    <i class="fa-solid fa-user"></i>
                </button>

                <div id="rwProfilePanel"
                     hidden
                     class="bg-white border border-rose-100 rounded-2xl shadow-xl overflow-hidden"
                     style="position:fixed;z-index:2147483647;width:220px;">

                    <div class="px-4 py-3 bg-pink-50 border-b border-rose-100">
                        <p class="text-xs font-extrabold text-slate-800">
                            Menu Akun
                        </p>
                        <p class="text-[10px] text-slate-500 mt-1">
                            Kelola akun RoadWatch
                        </p>
                    </div>

                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-3 px-4 py-3 text-xs font-semibold text-slate-700 hover:bg-pink-50 hover:text-pink-600 transition">
                        <i class="fa-regular fa-user text-pink-500"></i>
                        Profil Saya
                    </a>

                    <div class="border-t border-slate-100"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-3 px-4 py-3 text-xs font-semibold text-red-500 hover:bg-red-50 transition text-left cursor-pointer">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function () {
                const wrapper = document.getElementById('rwProfile');
                const button = document.getElementById('rwProfileButton');
                const panel = document.getElementById('rwProfilePanel');

                if (!wrapper || !button || !panel) return;

                // Place menu outside parent stacking contexts.
                document.body.appendChild(panel);

                function positionPanel() {
                    const rect = button.getBoundingClientRect();
                    const width = 220;

                    panel.style.left = Math.max(
                        12,
                        Math.min(rect.right - width, window.innerWidth - width - 12)
                    ) + 'px';

                    panel.style.top = Math.min(
                        rect.bottom + 8,
                        window.innerHeight - panel.offsetHeight - 12
                    ) + 'px';
                }

                function closePanel() {
                    panel.hidden = true;
                    button.setAttribute('aria-expanded', 'false');
                }

                button.addEventListener('click', function () {
                    if (panel.hidden) {
                        positionPanel();
                        panel.hidden = false;
                        button.setAttribute('aria-expanded', 'true');
                    } else {
                        closePanel();
                    }
                });

                document.addEventListener('click', function (event) {
                    if (!wrapper.contains(event.target) &&
                        !panel.contains(event.target)) {
                        closePanel();
                    }
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') closePanel();
                });

                window.addEventListener('resize', function () {
                    if (!panel.hidden) positionPanel();
                });

                window.addEventListener('scroll', function () {
                    if (!panel.hidden) positionPanel();
                }, true);
            });
            </script>

            <!-- Status Indicator Badge -->
            @if(auth()->user()?->role === 'masyarakat')
            {{-- ROADWATCH_PUBLIC_UI_V1 --}}
            <a href="{{ route('map.index') }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl text-xs font-bold shadow-sm bg-pink-50 text-pink-700 border border-pink-200 hover:bg-pink-100 transition">
                <i class="fa-solid fa-map-location-dot"></i>
                Pantau Kondisi Jalan
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
            @else
            <span data-header-device-status
                  class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl text-xs font-bold shadow-sm
                  {{ $device?->is_online ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/80' : 'bg-red-50 text-red-700 border border-red-200/80' }}">
                <span data-header-indicator
                      class="w-2 h-2 rounded-full {{ $device?->is_online ? 'bg-emerald-500 animate-pulse' : 'bg-red-500' }}"></span>
                <span data-header-status-text>{{ $device?->is_online ? 'Status: Perangkat Terhubung' : 'Status: Perangkat Terputus' }}</span>
                <i class="fa-solid fa-signal text-[10px] ml-0.5"></i>
            </span>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="inline-flex items-center shrink-0">@csrf<button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold text-red-500 hover:text-white bg-red-500/10 hover:bg-red-500 border border-red-500/20 rounded-2xl transition-all duration-200 shadow-sm cursor-pointer"><i class="fa-solid fa-right-from-bracket text-xs"></i><span>Log Out</span></button></form>
        </div>
    </div>

    <!-- 1. TOP STAT CARDS (GRID 4 COLS) -->
    <div class="rw-stats-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <!-- Card 1: Total Temuan -->
        <div class="bg-white rounded-2xl p-5 border border-rose-100/70 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-pink-50 text-[#DF78A6] flex items-center justify-center">
                        <i class="fa-solid fa-chart-pie text-xs"></i>
                    </div>
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">TOTAL TEMUAN</span>
                </div>
                <span data-growth-badge
                      class="text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-1
                      {{ $monthlyGrowth === null ? 'text-slate-500 bg-slate-100' : ($monthlyGrowth > 0 ? 'text-red-600 bg-red-50' : ($monthlyGrowth < 0 ? 'text-emerald-600 bg-emerald-50' : 'text-slate-500 bg-slate-100')) }}">
                    <i data-growth-icon
                       class="fa-solid {{ $monthlyGrowth === null ? 'fa-minus' : ($monthlyGrowth > 0 ? 'fa-arrow-trend-up' : ($monthlyGrowth < 0 ? 'fa-arrow-trend-down' : 'fa-minus')) }}"></i>
                    <span data-growth-value>{{ $monthlyGrowth === null ? 'N/A' : (($monthlyGrowth > 0 ? '+' : '') . number_format($monthlyGrowth, 1) . '%') }}</span>
                    <span class="text-slate-400 font-normal">vs bulan lalu</span>
                </span>
            </div>
            <div class="flex items-end justify-between">
                <div>
                    <h3 class="text-3xl font-extrabold text-slate-900 font-mono-tech"><span data-stat-total>{{ $totalDetections ?? 6 }}</span></h3>
                    <p class="text-[11px] text-slate-400 font-medium mt-1">Kumulatif seluruh jalan berlubang</p>
                </div>
                <div class="relative shrink-0 ml-3 rw-stat-chart" style="width:140px;height:68px;">
    <canvas id="rw-chart-total" data-rw-chart="total" data-chart-color="#E995BB" role="img" aria-label="Grafik tren total" style="width:100%;height:100%;"></canvas>
</div>
            </div>
        </div>

        <!-- Card 2: Temuan Hari Ini -->
        <div class="bg-white rounded-2xl p-5 border border-rose-100/70 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center">
                        <i class="fa-regular fa-calendar-check text-xs"></i>
                    </div>
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">TEMUAN HARI INI</span>
                </div>
            </div>
            <div class="flex items-end justify-between">
                <div>
                    <h3 class="text-3xl font-extrabold text-slate-900 font-mono-tech"><span data-stat-today>{{ $detectionsToday ?? 6 }}</span></h3>
                    <p class="text-[11px] text-slate-400 font-medium mt-1">Data masuk hari ini</p>
                </div>
                <div class="relative shrink-0 ml-3 rw-stat-chart" style="width:140px;height:68px;">
    <canvas id="rw-chart-today" data-rw-chart="today" data-chart-color="#F43F5E" role="img" aria-label="Grafik tren today" style="width:100%;height:100%;"></canvas>
</div>
            </div>
        </div>

        <!-- Card 3: Minggu Ini -->
        <div class="bg-white rounded-2xl p-5 border border-rose-100/70 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i class="fa-regular fa-clock text-xs"></i>
                    </div>
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">MINGGU INI</span>
                </div>
            </div>
            <div class="flex items-end justify-between">
                <div>
                    <h3 class="text-3xl font-extrabold text-slate-900 font-mono-tech"><span data-stat-week>{{ $detectionsThisWeek ?? 6 }}</span></h3>
                    <p class="text-[11px] text-slate-400 font-medium mt-1">Akumulasi sejak awal minggu</p>
                </div>
                <div class="relative shrink-0 ml-3 rw-stat-chart" style="width:140px;height:68px;">
    <canvas id="rw-chart-week" data-rw-chart="week" data-chart-color="#3B82F6" role="img" aria-label="Grafik tren week" style="width:100%;height:100%;"></canvas>
</div>
            </div>
        </div>

        <!-- Card 4: Bulan Ini -->
        <div class="bg-white rounded-2xl p-5 border border-rose-100/70 shadow-sm flex flex-col justify-between transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i class="fa-solid fa-calendar-days text-xs"></i>
                    </div>
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">BULAN INI</span>
                </div>
            </div>
            <div class="flex items-end justify-between">
                <div>
                    <h3 class="text-3xl font-extrabold text-slate-900 font-mono-tech"><span data-stat-month>{{ $detectionsThisMonth ?? 6 }}</span></h3>
                    <p class="text-[11px] text-slate-400 font-medium mt-1">Bulan berjalan</p>
                </div>
                <div class="relative shrink-0 ml-3 rw-stat-chart" style="width:140px;height:68px;">
    <canvas id="rw-chart-month" data-rw-chart="month" data-chart-color="#10B981" role="img" aria-label="Grafik tren month" style="width:100%;height:100%;"></canvas>
</div>
            </div>
        </div>
    </div>

    
{{-- ROADWATCH_PUBLIC_EQUAL_HEIGHT_V1 --}}
@if(auth()->user()?->role === 'masyarakat')
<style>
@media (min-width: 1024px) {

    .rw-monitor-grid {
        align-items: stretch;
    }

    .rw-monitor-grid > div {
        min-height: 0;
    }

    /* Peta mengisi tinggi panel secara proporsional */
    .rw-monitor-grid #dashboardMap {
        height: 100% !important;
        min-height: 420px;
        flex: 1 1 auto;
    }

    /* Live Detection: gambar lebih besar */
    .rw-monitor-grid #roadwatchLiveDetection {
        display: flex;
        flex-direction: column;
    }

    .rw-monitor-grid #roadwatchLiveDetection .relative.rounded-xl.overflow-hidden {
        height: 320px !important;
    }

    /* Update timestamp tetap di bawah panel */
    .rw-monitor-grid .rw-panel-updated {
        margin-top: auto;
    }

}
</style>
@endif

<!-- 2. MIDDLE SECTION GRID: 6 COLS (MAP) + 3 COLS (LIVE) + 3 COLS (HARDWARE) -->
    <div class="rw-monitor-grid grid grid-cols-1 lg:grid-cols-12 gap-5 mb-6 items-stretch" data-public-grid="ROADWATCH_PUBLIC_GRID_ALIGN_V1">
        
        <!-- Peta Monitoring Jalan (Span 6) -->
        <div class="lg:col-span-6 bg-white rounded-2xl p-5 border border-rose-100/70 shadow-sm flex flex-col justify-start">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-[#DF78A6]"></i>
                    Peta Monitoring Jalan
                </h2>
                <!-- Legend Pins -->
                <div class="flex items-center gap-3 text-[10px] font-extrabold">
                    <span class="flex items-center gap-1.5 text-slate-600"><span class="w-2 h-2 rounded-full bg-rose-500"></span> High</span>
                    <span class="flex items-center gap-1.5 text-slate-600"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Medium</span>
                    <span class="flex items-center gap-1.5 text-slate-600"><span class="w-2 h-2 rounded-full bg-sky-500"></span> Low</span>
                </div>
            </div>
            
            <div id="dashboardMap" style="width: 100%; border-radius: 12px; z-index: 1;" class="border border-slate-100 h-[300px] md:h-[360px] lg:h-[420px]"></div>
        
            <!-- RoadWatch Smooth Panel Sync V2 -->
            <div class="rw-panel-updated" data-panel-updated="map">
                <i class="fa-regular fa-clock" aria-hidden="true"></i>
                <span>Menghubungkan ke server...</span>
            </div>
        </div>

        <!-- Live Detection (Span 3) -->
        @php
            $latestDetection = $recentDetections->first();
        @endphp

        <div id="roadwatchLiveDetection" class="lg:col-span-3 bg-white rounded-2xl p-5 border border-rose-100/70 shadow-sm flex flex-col justify-start">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                        Live Detection
                    </h2>
                    <span id="rwLiveDetectionBadge"
                          role="status"
                          aria-live="polite"
                          class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-600 transition-colors duration-300">
                        Terbaru
                    </span>
                </div>

                @if($latestDetection)
                    <!-- Preview Image -->
                    <div class="relative rounded-xl overflow-hidden mb-3 border border-slate-200 h-[220px] lg:h-[250px]">
                        <img data-live-image
                            src="{{ $latestDetection->image }}"
                            alt="Pothole {{ $latestDetection->detection_code }}"
                            class="w-full h-full object-cover"
                        >

                        <span data-live-severity class="absolute bottom-2 left-2 px-2 py-0.5 rounded-md text-white font-extrabold text-[10px] uppercase
                            {{ $latestDetection->severity === 'High' ? 'bg-rose-600' : ($latestDetection->severity === 'Medium' ? 'bg-amber-500' : 'bg-sky-500') }}">
                            {{ $latestDetection->severity }}
                        </span>
                    </div>

                    <h3 data-live-location class="font-extrabold text-slate-900 text-sm mb-3 break-words">
                        {{ $latestDetection->location }}
                    </h3>

                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between items-center text-slate-500 gap-2">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-bullseye text-[10px]"></i>
                                Confidence
                            </span>
                            <span class="font-mono-tech font-bold text-slate-800">N/A</span>
                        </div>

                        <div class="flex justify-between items-center text-slate-500 gap-2">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-location-crosshairs text-[10px]"></i>
                                GPS
                            </span>
                            <span class="font-mono-tech font-bold text-slate-800 text-right">
                                <span data-live-gps>{{ $latestDetection->latitude }}, {{ $latestDetection->longitude }}</span>
                            </span>
                        </div>

                        <div class="flex justify-between items-center text-slate-500 gap-2">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-regular fa-clock text-[10px]"></i>
                                Deteksi pada
                            </span>
                            <span class="font-mono-tech font-bold text-slate-800">
                                <span data-live-time>{{ $latestDetection->detection_time }}</span>
                            </span>
                        </div>
                    </div>
                @else
                    <div class="aspect-video rounded-xl bg-slate-50 flex items-center justify-center text-xs text-slate-400 mb-3">
                        Belum ada data deteksi
                    </div>
                @endif
            </div>

            @if($latestDetection)
                <a data-live-detail href="{{ route('detections.show', $latestDetection->id) }}"
                   class="mt-4 w-full py-2 bg-violet-50 hover:bg-violet-600 text-[#DF78A6] hover:text-white rounded-xl text-xs font-extrabold text-center transition flex items-center justify-center gap-1.5">
                    <span>Lihat Detail</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            @endif
        
            <div class="rw-panel-updated" data-panel-updated="detection">
                <i class="fa-regular fa-clock" aria-hidden="true"></i>
                <span>Menghubungkan ke server...</span>
            </div>
        </div>

        @if(auth()->user()?->role === 'masyarakat')

<!-- ROADWATCH_PUBLIC_DASHBOARD_V2 -->
<div class="lg:col-span-3 bg-white rounded-2xl p-5 border border-rose-100/70 shadow-sm flex flex-col">

    <h2 class="font-extrabold text-slate-900 text-sm mb-4 flex items-center gap-2">
        <i class="fa-solid fa-shield-heart text-[#DF78A6]"></i>
        Informasi Keselamatan Jalan
    </h2>

    <div class="space-y-3">

        <a href="{{ route('map.index') }}"
           class="flex items-center gap-3 p-3 rounded-xl bg-pink-50 hover:bg-pink-100 transition">
            <i class="fa-solid fa-map-location-dot text-pink-500"></i>
            <div>
                <h3 class="font-bold text-xs text-slate-900">Peta Kerusakan Jalan</h3>
                <p class="text-[10px] text-slate-500">Pantau lokasi jalan yang terdeteksi rusak.</p>
            </div>
        </a>

        <a href="{{ route('detections.index') }}"
           class="flex items-center gap-3 p-3 rounded-xl bg-blue-50 hover:bg-blue-100 transition">
            <i class="fa-solid fa-triangle-exclamation text-blue-500"></i>
            <div>
                <h3 class="font-bold text-xs text-slate-900">Informasi Tingkat Bahaya</h3>
                <p class="text-[10px] text-slate-500">Kenali kategori High, Medium, dan Low.</p>
            </div>
        </a>

        <div class="rounded-xl bg-slate-50 p-4 mt-3">
            <h3 class="font-extrabold text-xs text-slate-800 mb-3">
                <i class="fa-solid fa-circle-info text-[#DF78A6] mr-1"></i>
                Panduan Keselamatan
            </h3>

            <div class="space-y-3 text-[11px] text-slate-600">
                <p><span class="font-bold text-red-500">High</span> — Kurangi kecepatan dan tingkatkan kewaspadaan.</p>
                <p><span class="font-bold text-amber-500">Medium</span> — Perhatikan permukaan jalan dan jaga jarak aman.</p>
                <p><span class="font-bold text-blue-500">Low</span> — Tetap waspada terhadap perubahan kondisi jalan.</p>
            </div>
        </div>

    </div>

    
<!-- ROADWATCH_PUBLIC_SAFETY_V3 -->
<div class="mt-5 pt-4 border-t border-rose-100">

    <h3 class="text-xs font-extrabold text-slate-800 mb-3">
        <i class="fa-solid fa-car-side text-[#DF78A6] mr-1"></i>
        Tips Berkendara Aman
    </h3>

    <div class="space-y-2">

        <div class="flex items-start gap-2 rounded-xl bg-rose-50/70 p-3">
            <i class="fa-solid fa-gauge text-[#DF78A6] mt-0.5"></i>
            <div>
                <p class="text-[11px] font-bold text-slate-800">
                    Sesuaikan Kecepatan
                </p>
                <p class="text-[10px] text-slate-500 mt-1">
                    Kurangi kecepatan ketika melewati jalan yang rusak.
                </p>
            </div>
        </div>

        <div class="flex items-start gap-2 rounded-xl bg-blue-50/70 p-3">
            <i class="fa-solid fa-route text-blue-500 mt-0.5"></i>
            <div>
                <p class="text-[11px] font-bold text-slate-800">
                    Periksa Rute Perjalanan
                </p>
                <p class="text-[10px] text-slate-500 mt-1">
                    Gunakan peta monitoring untuk mengetahui lokasi kerusakan.
                </p>
            </div>
        </div>

    </div>

    <a href="{{ route('map.index') }}"
       class="mt-4 flex items-center justify-center gap-2 rounded-xl bg-[#DF78A6] px-4 py-3 text-white text-xs font-bold hover:opacity-90 transition">
        <i class="fa-solid fa-map-location-dot"></i>
        Jelajahi Peta Monitoring
        <i class="fa-solid fa-arrow-right text-[10px]"></i>
    </a>

</div>

<div class="mt-auto pt-4 text-[10px] text-slate-400">
        <i class="fa-solid fa-shield-halved text-[#DF78A6] mr-1"></i>
        Informasi berdasarkan pemantauan RoadWatch.
    </div>
</div>

@else
<!-- Status Perangkat & Sensor (Span 3) -->
        <div class="lg:col-span-3 bg-white rounded-2xl p-5 border border-rose-100/70 shadow-sm flex flex-col justify-start">
            <h2 class="font-extrabold text-slate-900 text-sm mb-3 flex items-center gap-2">
                <i class="fa-solid fa-sliders text-[#DF78A6]"></i>
                Status Perangkat & Sensor
            </h2>

            <div class="space-y-2.5">
                <!-- Hardware 1 -->
                <div class="flex items-start gap-2.5 p-1.5 rounded-xl hover:bg-slate-50 transition">
                    <div class="p-2 bg-pink-50 text-[#DF78A6] rounded-lg shrink-0">
                        <i class="fa-solid fa-microchip text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-xs text-slate-900 truncate">{{ $device?->mcu ?? 'ESP32-S3 CAM' }}</h4>
                            <span data-device-status class="text-[10px] font-bold flex items-center gap-1 {{ ($device?->is_online ?? false) ? 'text-emerald-600' : 'text-slate-400' }}">
    <span class="w-1.5 h-1.5 rounded-full {{ ($device?->is_online ?? false) ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
    {{ ($device?->is_online ?? false) ? 'Online' : 'Offline' }}
</span>
                        </div>
                        <p class="text-[10px] text-slate-400 line-clamp-1">Main controller dan komunikasi API RoadWatch.</p>
                    </div>
                </div>

                <!-- Hardware 2 -->
                <div class="flex items-start gap-2.5 p-1.5 rounded-xl hover:bg-slate-50 transition">
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg shrink-0">
                        <i class="fa-solid fa-camera text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-xs text-slate-900 truncate">Integrated Camera</h4>
                            <span class="text-[10px] font-bold text-slate-400">
    Unknown
</span>
                        </div>
                        <p class="text-[10px] text-slate-400 line-clamp-1">Kamera terintegrasi pada ESP32-S3 CAM.</p>
                    </div>
                </div>

                <!-- Hardware 3 -->
                <div class="flex items-start gap-2.5 p-1.5 rounded-xl hover:bg-slate-50 transition">
                    <div class="p-2 bg-violet-50 text-[#DF78A6] rounded-lg shrink-0">
                        <i class="fa-solid fa-brain text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-xs text-slate-900 truncate">Algoritma FOMO</h4>
                            <span class="text-[10px] font-bold text-slate-400">
    Unknown
</span>
                        </div>
                        <p class="text-[10px] text-slate-400 line-clamp-1">Model AI Computer Vision Ultra-ringan memproses deteksi objek pada Edge Device.</p>
                    </div>
                </div>

                <!-- Hardware 4 -->
                <div class="flex items-start gap-2.5 p-1.5 rounded-xl hover:bg-slate-50 transition">
                    <div class="p-2 bg-amber-50 text-amber-600 rounded-lg shrink-0">
                        <i class="fa-solid fa-location-crosshairs text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-xs text-slate-900 truncate">GPS {{ $device?->gps_module ?? 'NEO-6M' }}</h4>
                            <span data-gps-status class="text-[10px] font-bold {{ ($device?->is_online ?? false) && $device?->gps_status === 'Fix' ? 'text-emerald-600' : 'text-slate-400' }}">
    {{ ($device?->is_online ?? false) ? ($device?->gps_status ?? 'Unknown') : 'Unknown' }}
</span>
                        </div>
                        <p class="text-[10px] text-slate-400 line-clamp-1">Menangkap koordinat presisi spasial (Latitude & Longitude) lokasi jalan berlubang.</p>
                    </div>
                </div>

            </div>

            <!-- Device Health Overview -->
            <div id="rwDeviceHealth" class="mt-4 pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-extrabold text-slate-800">
                        <i class="fa-solid fa-heart-pulse text-[#DF78A6] mr-1"></i>
                        Device Health
                    </h3>
                    <span class="text-[9px] text-slate-400 font-semibold">
                        Live Monitoring
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-2">

                    <div class="rounded-xl bg-slate-50 p-3 min-w-0">
                        <div class="text-[10px] text-slate-400 mb-1">
                            <i class="fa-solid fa-wifi mr-1"></i> Koneksi
                        </div>
                        <div data-health-connection
                             class="text-xs font-extrabold {{ ($device?->is_online ?? false) ? 'text-emerald-600' : 'text-slate-500' }}">
                            {{ ($device?->is_online ?? false) ? 'Online' : 'Offline' }}
                        </div>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-3 min-w-0">
                        <div class="text-[10px] text-slate-400 mb-1">
                            <i class="fa-solid fa-location-dot mr-1"></i> GPS
                        </div>
                        <div data-health-gps class="text-xs font-extrabold text-slate-700 truncate">
                            {{ ($device?->is_online ?? false) ? ($device?->gps_status ?? 'Unknown') : 'Unknown' }}
                        </div>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-3 min-w-0">
                        <div class="text-[10px] text-slate-400 mb-1">
                            <i class="fa-regular fa-clock mr-1"></i> Last Heartbeat
                        </div>
                        <div data-health-heartbeat class="text-[11px] font-bold text-slate-700">
                            {{ $device?->last_seen_at?->copy()->timezone('Asia/Jakarta')->format('d M H:i') ?? 'Belum tersedia' }}
                        </div>
                    </div>

                    <div class="rounded-xl bg-slate-50 p-3 min-w-0">
                        <div class="text-[10px] text-slate-400 mb-1">
                            <i class="fa-solid fa-code-branch mr-1"></i> Firmware
                        </div>
                        <div data-health-firmware class="text-[11px] font-bold text-slate-700 truncate">
                            {{ $device?->firmware_version ?? 'Belum tersedia' }}
                        </div>
                    </div>

                </div>

                <p class="text-[9px] text-slate-400 mt-3 flex items-center gap-1.5">
                    <i class="fa-solid fa-arrows-rotate text-[#DF78A6]"></i>
                    Data disinkronkan otomatis setiap 10 detik
                </p>
            </div>
        
            <div class="rw-panel-updated" data-panel-updated="device">
                <i class="fa-regular fa-clock" aria-hidden="true"></i>
                <span>Menghubungkan ke server...</span>
            </div>
        </div>
@endif
    </div>


<!-- Detection Analytics -->
    <section id="rwDetectionAnalytics"
        class="bg-white rounded-2xl border border-rose-100/70 shadow-sm p-5 md:p-6 mb-6">

        <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-[#DF78A6]"></i>
                    Detection Analytics
                </h2>
                <p class="text-xs text-slate-400 mt-1">
                    Distribusi tingkat keparahan temuan jalan berlubang
                </p>
            </div>

            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-full">
                <i class="fa-solid fa-arrows-rotate mr-1"></i>
                Live Analytics
            </span>
        </div>

        @php
            $severityTotal = array_sum($severityData);
            $severityColors = [
                'High' => '#F43F5E',
                'Medium' => '#F59E0B',
                'Low' => '#0EA5E9',
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">

            <div class="relative mx-auto w-full max-w-[260px] h-[230px]">
                <canvas id="rwSeverityChart"
                    role="img"
                    aria-label="Distribusi tingkat keparahan deteksi"
                    class="w-full h-full"></canvas>
            </div>

            <div class="space-y-5">
                @foreach (['High', 'Medium', 'Low'] as $level)
                    @php
                        $count = $severityData[$level] ?? 0;
                        $percentage = $severityTotal > 0
                            ? round($count / $severityTotal * 100, 1)
                            : 0;
                    @endphp

                    <div data-severity-row="{{ $level }}">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full"
                                    style="background-color: {{ $severityColors[$level] }}"></span>
                                <span class="text-sm font-bold text-slate-700">
                                    {{ $level }}
                                </span>
                            </div>

                            <span class="text-xs font-bold text-slate-600">
                                <span data-severity-count>{{ $count }}</span>
                                temuan ·
                                <span data-severity-percent>{{ $percentage }}</span>%
                            </span>
                        </div>

                        <div class="h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div data-severity-bar
                                class="h-full rounded-full transition-all duration-500"
                                style="width: {{ $percentage }}%; background-color: {{ $severityColors[$level] }}">
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="pt-4 border-t border-slate-100 flex justify-between items-center">
                    <span class="text-sm font-semibold text-slate-500">
                        Total Deteksi
                    </span>
                    <span data-severity-total
                        class="text-2xl font-extrabold text-slate-900">
                        {{ $severityTotal }}
                    </span>
                </div>
            </div>
        </div>
    </section>


    <!-- Detection Trend Analytics -->
    <section id="rwDetectionTrend"
        class="bg-white rounded-2xl border border-rose-100/70 shadow-sm p-5 md:p-6 mb-6">

        <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-chart-line text-[#DF78A6]"></i>
                    Detection Trend
                </h2>
                <p id="rwTrendSubtitle" class="text-xs text-slate-400 mt-1">
                    Aktivitas deteksi jalan berlubang selama 7 hari terakhir
                </p>
            </div>

            <div id="rwTrendPeriodFilter"
                class="inline-flex items-center gap-1 rounded-xl bg-rose-50 p-1"
                role="group"
                aria-label="Pilih periode grafik deteksi">

                <button type="button"
                    data-trend-period="7"
                    aria-pressed="true"
                    class="rounded-lg bg-white px-3 py-1.5 text-xs font-bold text-[#DF78A6] shadow-sm transition">
                    7 Hari
                </button>

                <button type="button"
                    data-trend-period="30"
                    aria-pressed="false"
                    class="rounded-lg px-3 py-1.5 text-xs font-bold text-slate-500 transition hover:text-[#DF78A6]">
                    30 Hari
                </button>
            </div>
        </div>

        <div class="relative w-full h-[260px] md:h-[320px]">
            <canvas id="rwTrendChart"
                role="img"
                aria-label="Grafik tren jumlah deteksi tujuh hari terakhir">
            </canvas>
        </div>

        <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap justify-between items-center gap-3">
            <span class="text-xs text-slate-400">
                <i class="fa-solid fa-circle-info mr-1"></i>
                Data berdasarkan tanggal deteksi (WIB)
            </span>

            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#DF78A6]"></span>
                <span class="text-xs font-semibold text-slate-500">
                    Total 7 Hari:
                </span>
                <span data-trend-total class="text-sm font-extrabold text-slate-900">
                    {{ array_sum($trendData['values']) }}
                </span>
                <span class="text-xs text-slate-400">deteksi</span>
            </div>
        </div>
    </section>

    <!-- 3. RIWAYAT DETEKSI TERAKHIR TABLE SECTION -->
    <div id="rwHistoryPanel" class="bg-white rounded-2xl border border-rose-100/70 shadow-sm p-6 space-y-4">
        <div class="rw-history-header flex items-center justify-between">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-[#DF78A6]"></i>
                    Riwayat Deteksi Terakhir
                </h2>
                <p class="text-xs text-slate-400 font-medium">Log kejadian temuan lubang jalan terbaru dari sensor</p>
            </div>
            <a href="{{ route('detections.index') }}" class="text-xs font-extrabold text-[#DF78A6] hover:text-violet-800 flex items-center gap-1 transition">
                <span>Lihat Semua Data</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <p class="rw-history-scroll-hint" aria-hidden="true">
            <i class="fa-solid fa-arrows-left-right"></i>
            Geser tabel untuk melihat kolom lainnya
            <i class="fa-solid fa-arrow-right"></i>
        </p>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="p-3.5">KODE ID</th>
                        <th class="p-3.5">LOKASI JALAN</th>
                        <th class="p-3.5">KOORDINAT</th>
                        <th class="p-3.5">WAKTU DETEKSI</th>
                        <th class="p-3.5">TINGKAT BAHAYA</th>
                        <th class="p-3.5">STATUS PENANGANAN</th>
                        <th class="p-3.5 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody id="roadwatchHistoryBody" class="divide-y divide-slate-100 text-slate-700 font-medium">
                    @forelse ($recentDetections as $detection)
                    @php
                        $severityStyle = match ($detection->severity) {
                            'High' => 'bg-[#FFE2E2] text-[#9B1C1C]',
                            'Medium' => 'bg-[#FEF3C7] text-[#92400E]',
                            'Low' => 'bg-[#E0F2FE] text-[#0369A1]',
                            default => 'bg-slate-100 text-slate-600',
                        };

                        $statusStyle = match ($detection->status) {
                            'Detected' => 'bg-[#FEF3C7] text-[#92400E]',
                            'In Progress' => 'bg-[#E0F2FE] text-[#0369A1]',
                            'Selesai', 'Resolved' => 'bg-[#D1FAE5] text-[#065F46]',
                            default => 'bg-slate-100 text-slate-600',
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="p-3.5 font-bold font-mono-tech text-[#DF78A6]">
                            {{ $detection->detection_code }}
                        </td>
                        <td class="p-3.5 font-semibold text-slate-800">
                            {{ $detection->location ?: 'Lokasi tidak tersedia' }}
                        </td>
                        <td class="p-3.5 font-mono-tech text-slate-500 text-[11px]">
                            {{ $detection->latitude !== null && $detection->longitude !== null ? $detection->latitude . ', ' . $detection->longitude : 'N/A' }}
                        </td>
                        <td class="p-3.5 font-mono-tech text-slate-600">
                            {{ $detection->detection_date ?: 'N/A' }}
                            <span class="text-slate-400">({{ $detection->detection_time ?: 'N/A' }})</span>
                        </td>
                        <td class="p-3.5">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold {{ $severityStyle }}">
                                {{ $detection->severity ?: 'Unknown' }}
                            </span>
                        </td>
                        <td class="p-3.5">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold {{ $statusStyle }}">
                                {{ $detection->status ?: 'Unknown' }}
                            </span>
                        </td>
                        <td class="p-3.5 text-center">
                            <a href="{{ route('detections.show', $detection->id) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-violet-50 text-[#DF78A6] hover:bg-violet-600 hover:text-white rounded-xl font-bold transition">
                                <i class="fa-solid fa-eye text-[11px]"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-6 text-center text-slate-400">
                            Belum ada data deteksi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Leaflet JS Setup for Middle Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script>
window.roadwatchChartData = @json($chartData);
window.roadwatchSeverityData = @json($severityData);
window.roadwatchTrendData = @json($trendData);
window.roadwatchTrendPeriods = @json($trendPeriods);
</script>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // RoadWatch Dashboard Scroll UX Fix V3 - Mobile Interactive Mode
    const rwMobileMap = window.matchMedia('(pointer: coarse)').matches;

    const dashMap = L.map('dashboardMap', {
        scrollWheelZoom: false,
        touchZoom: !rwMobileMap,
        dragging: !rwMobileMap
    }).setView([-6.388, 106.8312], 13);

    if (rwMobileMap) {
        const mapContainer = dashMap.getContainer();

        const toggle = document.createElement('button');
        toggle.type = 'button';
        toggle.textContent = 'Aktifkan Peta';
        toggle.setAttribute('aria-pressed', 'false');

        Object.assign(toggle.style, {
            position: 'absolute',
            bottom: '12px',
            left: '12px',
            zIndex: '1000',
            background: '#FFFFFF',
            color: '#BE4B87',
            border: '1px solid #F0C8DA',
            borderRadius: '10px',
            padding: '9px 13px',
            fontSize: '12px',
            fontWeight: '700',
            boxShadow: '0 3px 12px rgba(0,0,0,.12)',
            cursor: 'pointer'
        });

        mapContainer.appendChild(toggle);

        let interactive = false;

        toggle.addEventListener('click', function (event) {
            event.stopPropagation();

            interactive = !interactive;

            if (interactive) {
                dashMap.dragging.enable();
                dashMap.touchZoom.enable();
                toggle.textContent = 'Scroll Halaman';
            } else {
                dashMap.dragging.disable();
                dashMap.touchZoom.disable();
                toggle.textContent = 'Aktifkan Peta';
            }

            toggle.setAttribute('aria-pressed', String(interactive));
        });
    }

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(dashMap);

    const markers = new Map();

    const severityColors = {
        High: '#F43F5E',
        Medium: '#F59E0B',
        Low: '#0EA5E9'
    };

    const severityRank = {
        High: 3,
        Medium: 2,
        Low: 1
    };

    function buildPopup(detections) {
        const popup = document.createElement('div');
        popup.style.minWidth = '190px';

        const heading = document.createElement('strong');
        heading.textContent = detections.length + ' Deteksi di Lokasi Ini';
        popup.appendChild(heading);

        detections.forEach(function(item) {
            const entry = document.createElement('div');
            entry.style.padding = '8px 0';
            entry.style.borderBottom = '1px solid #e2e8f0';

            const code = document.createElement('div');
            code.style.fontWeight = 'bold';
            code.textContent = item.detection_code || 'Detection';
            entry.appendChild(code);

            const location = document.createElement('div');
            location.textContent = item.location || 'Lokasi tidak tersedia';
            entry.appendChild(location);

            const severity = document.createElement('div');
            severity.textContent = 'Severity: ' + (item.severity || 'Unknown');
            entry.appendChild(severity);

            const detail = document.createElement('a');
            detail.href = @json(url('/detections')) + '/' + encodeURIComponent(item.id);
            detail.textContent = 'Lihat Detail';
            detail.style.color = '#DF78A6';
            detail.style.fontWeight = 'bold';
            entry.appendChild(detail);

            popup.appendChild(entry);
        });

        return popup;
    }

    function updateRoadwatchMap(detections, initialLoad = false) {
        if (!Array.isArray(detections)) return;

        const grouped = new Map();

        detections.forEach(function(item) {
            if (item.latitude == null || item.longitude == null ||
                item.latitude === '' || item.longitude === '') return;

            const lat = Number(item.latitude);
            const lng = Number(item.longitude);

            if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;
            if (lat < -90 || lat > 90 || lng < -180 || lng > 180) return;

            const key = lat.toFixed(6) + ',' + lng.toFixed(6);

            if (!grouped.has(key)) {
                grouped.set(key, {
                    lat: lat,
                    lng: lng,
                    detections: []
                });
            }

            grouped.get(key).detections.push(item);
        });

        markers.forEach(function(record, key) {
            if (!grouped.has(key)) {
                dashMap.removeLayer(record.marker);
                markers.delete(key);
            }
        });

        const bounds = [];

        grouped.forEach(function(group, key) {
            const highestSeverity = group.detections.reduce(function(current, item) {
                return (severityRank[item.severity] || 0) >
                       (severityRank[current] || 0)
                    ? item.severity
                    : current;
            }, 'Unknown');

            const color = severityColors[highestSeverity] || '#94A3B8';

            const signature = JSON.stringify(group.detections.map(function(item) {
                return [
                    item.id,
                    item.detection_code,
                    item.location,
                    item.severity
                ];
            }));

            const existing = markers.get(key);

            if (existing && existing.signature === signature) {
                bounds.push([group.lat, group.lng]);
                return;
            }

            if (existing) {
                const wasOpen = existing.marker.isPopupOpen();

                existing.marker.setStyle({
                    fillColor: color
                });

                existing.marker.setRadius(group.detections.length > 1 ? 10 : 7);

                existing.marker.setPopupContent(buildPopup(group.detections));

                existing.signature = signature;

                if (wasOpen && !existing.marker.isPopupOpen()) {
                    existing.marker.openPopup();
                }
            } else {
                const marker = L.circleMarker([group.lat, group.lng], {
                    radius: group.detections.length > 1 ? 10 : 7,
                    color: '#FFFFFF',
                    weight: 2,
                    fillColor: color,
                    fillOpacity: 0.95
                }).addTo(dashMap);

                marker.bindPopup(buildPopup(group.detections), {
                    maxHeight: 260,
                    maxWidth: 280
                });

                markers.set(key, {
                    marker: marker,
                    signature: signature
                });
            }

            bounds.push([group.lat, group.lng]);
        });

        if (initialLoad && bounds.length > 0) {
            dashMap.fitBounds(bounds, {
                padding: [25, 25],
                maxZoom: 14
            });
        }
    }

    window.updateRoadwatchMap = updateRoadwatchMap;

    updateRoadwatchMap(@json($mapDetections), true);

    setTimeout(function() {
        dashMap.invalidateSize();
    }, 300);
});
</script>

<!-- RoadWatch Live Detection Fetch -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const historyBody = document.getElementById('roadwatchHistoryBody');
    let lastHistorySignature = null;

    function updateHistoryTable(detections) {
        if (!historyBody || !Array.isArray(detections)) return;

        const signature = JSON.stringify(detections);
        if (signature === lastHistorySignature) return;

        const severityStyles = {
            High: 'bg-[#FFE2E2] text-[#9B1C1C]',
            Medium: 'bg-[#FEF3C7] text-[#92400E]',
            Low: 'bg-[#E0F2FE] text-[#0369A1]'
        };

        const statusStyles = {
            Detected: 'bg-[#FEF3C7] text-[#92400E]',
            'In Progress': 'bg-[#E0F2FE] text-[#0369A1]',
            Selesai: 'bg-[#D1FAE5] text-[#065F46]',
            Resolved: 'bg-[#D1FAE5] text-[#065F46]'
        };

        const fragment = document.createDocumentFragment();

        function addCell(row, value, className) {
            const cell = document.createElement('td');
            cell.className = className;
            cell.textContent = value ?? 'N/A';
            row.appendChild(cell);
            return cell;
        }

        function addBadge(row, value, styles) {
            const cell = document.createElement('td');
            cell.className = 'p-3.5';

            const badge = document.createElement('span');
            badge.className = 'px-2.5 py-1 rounded-full text-[10px] font-extrabold ' +
                (styles[value] || 'bg-slate-100 text-slate-600');
            badge.textContent = value || 'Unknown';

            cell.appendChild(badge);
            row.appendChild(cell);
        }

        detections.forEach(function(item) {
            const row = document.createElement('tr');
            row.className = 'hover:bg-slate-50/60 transition';

            addCell(row, item.detection_code,
                'p-3.5 font-bold font-mono-tech text-[#DF78A6]');

            addCell(row, item.location || 'Lokasi tidak tersedia',
                'p-3.5 font-semibold text-slate-800');

            const coordinates =
                item.latitude != null && item.longitude != null
                    ? item.latitude + ', ' + item.longitude
                    : 'N/A';

            addCell(row, coordinates,
                'p-3.5 font-mono-tech text-slate-500 text-[11px]');

            const detectionDate = item.detection_date || 'N/A';
            const detectionTime = item.detection_time || 'N/A';

            const timeCell = addCell(row, detectionDate + ' ',
                'p-3.5 font-mono-tech text-slate-600');

            const timeSpan = document.createElement('span');
            timeSpan.className = 'text-slate-400';
            timeSpan.textContent = '(' + detectionTime + ')';
            timeCell.appendChild(timeSpan);

            addBadge(row, item.severity, severityStyles);
            addBadge(row, item.status, statusStyles);

            const actionCell = document.createElement('td');
            actionCell.className = 'p-3.5 text-center';

            const detail = document.createElement('a');
            detail.href = @json(url('/detections')) + '/' +
                encodeURIComponent(item.id);
            detail.className =
                'inline-flex items-center gap-1 px-3 py-1.5 ' +
                'bg-violet-50 text-[#DF78A6] hover:bg-violet-600 ' +
                'hover:text-white rounded-xl font-bold transition';
            detail.textContent = 'Detail';

            actionCell.appendChild(detail);
            row.appendChild(actionCell);
            fragment.appendChild(row);
        });

        if (detections.length === 0) {
            const row = document.createElement('tr');
            const cell = addCell(row, 'Belum ada data deteksi.',
                'p-6 text-center text-slate-400');
            cell.colSpan = 7;
            fragment.appendChild(row);
        }

        historyBody.replaceChildren(fragment);
        lastHistorySignature = signature;
    }

    const liveContainer = document.getElementById('roadwatchLiveDetection');

    if (!liveContainer) return;

    let lastDetectionId = @json($latestDetection?->id);
    let fetchInProgress = false;

    // RoadWatch Live Sync - Polling Integration V1
    const syncIndicator = document.getElementById('rwLiveSync');
    const syncLabel = syncIndicator?.querySelector('[data-sync-label]');
    const syncTime = syncIndicator?.querySelector('[data-sync-time]');
    let lastSuccessfulSync = null;

    // RoadWatch Panel Last Updated - Realtime V1
    function updatePanelTimestamps() {
        if (!lastSuccessfulSync) return;

        const time = lastSuccessfulSync.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });

        document.querySelectorAll('[data-panel-updated]').forEach(panel => {
            const label = panel.querySelector('span');
            if (label) {
                label.textContent = 'Updated ' + time;
            }
            panel.title = 'Data terakhir diterima dari server pada ' + time;
        });
    }

    function setSyncState(state) {
        if (!syncIndicator) return;

        syncIndicator.dataset.syncState = state;

        // RoadWatch Panel Sync Error State V1
        if (state === 'success') {
            syncIndicator.dataset.stale = 'false';

            document.querySelectorAll('[data-panel-updated]').forEach(panel => {
                panel.dataset.stale = 'false';
            });

            updatePanelTimestamps();
        }

        document.querySelectorAll('[data-panel-updated]').forEach(panel => {
            const label = panel.querySelector('span');

            if (!label) return;

            if (state === 'error') {
                panel.dataset.syncError = 'true';
                label.textContent = lastSuccessfulSync
                    ? 'Gagal sinkronisasi · Terakhir ' +
                      lastSuccessfulSync.toLocaleTimeString('id-ID', {
                          hour: '2-digit',
                          minute: '2-digit',
                          second: '2-digit'
                      })
                    : 'Gagal sinkronisasi · Belum ada pembaruan';
            } else if (state === 'success') {
                panel.dataset.syncError = 'false';
            }
        });

        if (syncLabel) {
            syncLabel.textContent = {
                waiting: 'Menunggu sinkronisasi',
                loading: 'Syncing...',
                success: syncIndicator?.dataset.publicMode === 'true' ? 'Pembaruan Data' : 'Live Sync',
                error: 'Connection Lost'
            }[state] || 'Menunggu sinkronisasi';
        }

        if (syncTime) {
            syncTime.textContent = lastSuccessfulSync
                ? '· ' + lastSuccessfulSync.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                })
                : '';
        }
    }

    // RoadWatch Stale Data Warning V1
    const STALE_THRESHOLD_MS = 60000;

    function checkStaleData() {
        if (!lastSuccessfulSync || !syncIndicator) return;

        const ageMs = Date.now() - lastSuccessfulSync.getTime();
        const isStale = ageMs >= STALE_THRESHOLD_MS;

        syncIndicator.dataset.stale = String(isStale);

        if (!isStale) return;

        const minutes = Math.floor(ageMs / 60000);
        const message = 'Data belum diperbarui ' + minutes + ' menit';

        if (syncLabel) {
            syncLabel.textContent = message;
        }

        document.querySelectorAll('[data-panel-updated]').forEach(panel => {
            const label = panel.querySelector('span');

            panel.dataset.stale = 'true';

            if (label) {
                label.textContent = message;
            }
        });
    }

    async function updateLiveDetection() {
        if (document.visibilityState !== 'visible' || fetchInProgress) return;

        fetchInProgress = true;

        // RoadWatch Smooth Live Sync V2
        // Loading hanya ditampilkan sebelum sinkronisasi pertama berhasil.
        // Polling berikutnya berjalan tanpa mengubah indikator Live Sync.
        if (!lastSuccessfulSync) {
            setSyncState('loading');
        }

        let syncSucceeded = false;

        try {
            const response = await fetch(@json(route('dashboard.live-data')), {
                headers: {
                    'Accept': 'application/json'
                },
                credentials: 'same-origin',
                cache: 'no-store'
            });

            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }

            const data = await response.json();
            syncSucceeded = true;
            lastSuccessfulSync = new Date();

            updateHistoryTable(data.recent_detections);

            if (data.stats) {
                const growthBadge = document.querySelector('[data-growth-badge]');
                const growthIcon = document.querySelector('[data-growth-icon]');
                const growthValue = document.querySelector('[data-growth-value]');

                if (growthBadge && growthIcon && growthValue) {
                    const growth = data.stats.monthly_growth;
                    const valid = growth !== null &&
                        growth !== undefined &&
                        Number.isFinite(Number(growth));

                    const value = valid ? Number(growth) : null;

                    growthValue.textContent = value === null
                        ? 'N/A'
                        : (value > 0 ? '+' : '') + value.toFixed(1) + '%';

                    growthBadge.classList.remove(
                        'text-red-600', 'bg-red-50',
                        'text-emerald-600', 'bg-emerald-50',
                        'text-slate-500', 'bg-slate-100'
                    );

                    growthIcon.classList.remove(
                        'fa-arrow-trend-up',
                        'fa-arrow-trend-down',
                        'fa-minus'
                    );

                    if (value === null || value === 0) {
                        growthBadge.classList.add('text-slate-500', 'bg-slate-100');
                        growthIcon.classList.add('fa-minus');
                    } else if (value > 0) {
                        growthBadge.classList.add('text-red-600', 'bg-red-50');
                        growthIcon.classList.add('fa-arrow-trend-up');
                    } else {
                        growthBadge.classList.add('text-emerald-600', 'bg-emerald-50');
                        growthIcon.classList.add('fa-arrow-trend-down');
                    }
                }

                const statElements = {
                    total: document.querySelector('[data-stat-total]'),
                    today: document.querySelector('[data-stat-today]'),
                    week: document.querySelector('[data-stat-week]'),
                    month: document.querySelector('[data-stat-month]')
                };

                Object.entries(statElements).forEach(function([key, element]) {
                    if (element && Number.isFinite(Number(data.stats[key]))
                        && data.stats[key] !== null) {
                        element.textContent = Number(data.stats[key]).toLocaleString('id-ID');
                    }
                });
            }

            if (typeof window.updateRoadwatchCharts === 'function' && data.charts) {
                window.updateRoadwatchCharts(data.charts);
            }

            // Update Detection Analytics using existing realtime polling
            if (typeof window.updateRoadwatchSeverity === 'function' && data.severity) {
                window.updateRoadwatchSeverity(data.severity);
            }

            // Update Detection Trend using existing realtime polling
            if (typeof window.updateRoadwatchTrend === 'function' && data.trend) {
                window.updateRoadwatchTrend(data.trend, data.trend_periods);
            }

            if (typeof window.updateRoadwatchMap === 'function') {
                window.updateRoadwatchMap(data.map_detections);
            }

            const device = data.device;

            // RoadWatch Smart Device Monitor V1
            if (device) {
                const sidebarDevice = document.getElementById('rwSidebarDevice');

                if (sidebarDevice) {
                    const online = device.online === true;
                    sidebarDevice.dataset.online = String(online);

                    const setText = (selector, value) => {
                        const el = sidebarDevice.querySelector(selector);
                        if (el) el.textContent = value;
                    };

                    setText('[data-sd-badge]', online ? 'Online' : 'Offline');
                    setText('[data-sd-mcu]', device.mcu || 'ESP32-S3 CAM');
                    setText('[data-sd-connection]',
                        online ? 'Connected' : 'Disconnected');
                    setText('[data-sd-gps]',
                        online ? (device.gps_status || 'Unknown') : 'Unknown');

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
                }
            }



            if (device) {
                const deviceStatus = document.querySelector('[data-device-status]');
                const gpsStatus = document.querySelector('[data-gps-status]');

                const headerStatus = document.querySelector('[data-header-device-status]');
                const headerIndicator = document.querySelector('[data-header-indicator]');
                const headerText = document.querySelector('[data-header-status-text]');

                const healthConnection = document.querySelector('[data-health-connection]');
                const healthGps = document.querySelector('[data-health-gps]');
                const healthHeartbeat = document.querySelector('[data-health-heartbeat]');
                const healthFirmware = document.querySelector('[data-health-firmware]');

                if (healthConnection) {
                    const online = device.online === true;
                    healthConnection.textContent = online ? 'Online' : 'Offline';
                    healthConnection.classList.toggle('text-emerald-600', online);
                    healthConnection.classList.toggle('text-slate-500', !online);
                }

                if (healthGps) {
                    healthGps.textContent = device.online === true
                        ? (device.gps_status || 'Unknown')
                        : 'Unknown';
                }

                if (healthHeartbeat) {
                    if (device.last_seen_at) {
                        const date = new Date(device.last_seen_at);

                        healthHeartbeat.textContent = Number.isNaN(date.getTime())
                            ? 'Belum tersedia'
                            : new Intl.DateTimeFormat('id-ID', {
                                timeZone: 'Asia/Jakarta',
                                day: '2-digit',
                                month: 'short',
                                hour: '2-digit',
                                minute: '2-digit',
                                hour12: false
                            }).format(date) + ' WIB';
                    } else {
                        healthHeartbeat.textContent = 'Belum tersedia';
                    }
                }

                if (healthFirmware) {
                    healthFirmware.textContent = device.firmware_version || 'Belum tersedia';
                }

                const isOnline = device.online === true;

                if (headerStatus && headerIndicator && headerText) {
                    headerStatus.classList.toggle('bg-emerald-50', isOnline);
                    headerStatus.classList.toggle('text-emerald-700', isOnline);
                    headerStatus.classList.toggle('border-emerald-200/80', isOnline);

                    headerStatus.classList.toggle('bg-red-50', !isOnline);
                    headerStatus.classList.toggle('text-red-700', !isOnline);
                    headerStatus.classList.toggle('border-red-200/80', !isOnline);

                    headerIndicator.classList.toggle('bg-emerald-500', isOnline);
                    headerIndicator.classList.toggle('animate-pulse', isOnline);
                    headerIndicator.classList.toggle('bg-red-500', !isOnline);

                    headerText.textContent = isOnline
                        ? 'Status: Perangkat Terhubung'
                        : 'Status: Perangkat Terputus';
                }

                if (deviceStatus) {
                    const online = device.online === true;

                    deviceStatus.classList.toggle('text-emerald-600', online);
                    deviceStatus.classList.toggle('text-slate-400', !online);

                    const indicator = deviceStatus.querySelector('span');

                    if (indicator) {
                        indicator.classList.toggle('bg-emerald-500', online);
                        indicator.classList.toggle('bg-slate-400', !online);
                    }

                    Array.from(deviceStatus.childNodes)
                        .filter(node => node.nodeType === Node.TEXT_NODE)
                        .forEach(node => node.remove());

                    deviceStatus.appendChild(
                        document.createTextNode(online ? ' Online' : ' Offline')
                    );
                }

                if (gpsStatus) {
                    const gps = device.online
                        ? (device.gps_status || 'Unknown')
                        : 'Unknown';

                    gpsStatus.textContent = gps;
                    gpsStatus.classList.toggle('text-emerald-600', gps === 'Fix');
                    gpsStatus.classList.toggle('text-slate-400', gps !== 'Fix');
                }
            }

            const detection = data.latest_detection;

            if (!detection || detection.id === lastDetectionId) return;

            const image = liveContainer.querySelector('[data-live-image]');
            const severity = liveContainer.querySelector('[data-live-severity]');
            const location = liveContainer.querySelector('[data-live-location]');
            const gps = liveContainer.querySelector('[data-live-gps]');
            const time = liveContainer.querySelector('[data-live-time]');
            const detail = liveContainer.querySelector('[data-live-detail]');

            if (!image || !severity || !location || !gps || !time || !detail) {
                console.warn('RoadWatch: Elemen Live Detection tidak lengkap.');
                return;
            }

            if (detection.image) {
                image.src = detection.image;
                image.alt = 'Pothole ' + (detection.detection_code || '');
            } else {
                image.removeAttribute('src');
            }

            const level = ['High', 'Medium', 'Low'].includes(detection.severity)
                ? detection.severity
                : 'Unknown';

            const severityColors = {
                High: 'bg-rose-600',
                Medium: 'bg-amber-500',
                Low: 'bg-sky-500',
                Unknown: 'bg-slate-500'
            };

            severity.classList.remove(
                'bg-rose-600',
                'bg-amber-500',
                'bg-sky-500',
                'bg-slate-500'
            );

            severity.classList.add(severityColors[level]);
            severity.textContent = level;

            location.textContent = detection.location || 'Lokasi tidak tersedia';

            gps.textContent =
                detection.latitude != null && detection.longitude != null
                    ? detection.latitude + ', ' + detection.longitude
                    : 'N/A';

            time.textContent = detection.detection_time || 'N/A';

            if (typeof detection.detail_url === 'string'
                && detection.detail_url.startsWith(window.location.origin + '/detections/')) {
                detail.href = detection.detail_url;
            }

            lastDetectionId = detection.id;

            // Animate only when a new detection arrives.
            liveContainer.classList.remove('rw-live-new-detection');

            // Restart the CSS animation for consecutive new detections.
            void liveContainer.offsetWidth;

            liveContainer.classList.add('rw-live-new-detection');

            // Show the badge only after receiving a new detection.
            const liveBadge = document.getElementById('rwLiveDetectionBadge');

            if (liveBadge) {
                if (window.rwLiveBadgeTimer) {
                    clearTimeout(window.rwLiveBadgeTimer);
                }

                liveBadge.textContent = '✦ NEW DETECTION';
                liveBadge.classList.remove('bg-rose-50', 'text-rose-600');
                liveBadge.classList.add('bg-emerald-100', 'text-emerald-700');

                window.rwLiveBadgeTimer = setTimeout(() => {
                    liveBadge.textContent = 'Terbaru';
                    liveBadge.classList.remove(
                        'bg-emerald-100',
                        'text-emerald-700'
                    );
                    liveBadge.classList.add('bg-rose-50', 'text-rose-600');
                    window.rwLiveBadgeTimer = null;
                }, 5000);
            }

            console.log(
                'RoadWatch Live Detection updated:',
                detection.detection_code
            );

        } catch (error) {
            // RoadWatch Sync Status Reliability Fix V1
            syncSucceeded = false;
            setSyncState('error');
            console.error('RoadWatch Live Detection fetch failed:', error);
        } finally {
            if (syncSucceeded) {
                setSyncState('success');
            }
            fetchInProgress = false;
        }
    }

    // RoadWatch Initial Live Sync Fix V2
    // Sinkronisasi langsung ketika Dashboard dibuka.
    updateLiveDetection();

    // Pertahankan polling setiap 10 detik.
    setInterval(updateLiveDetection, 10000);
    setInterval(checkStaleData, 5000);
});
</script>

</x-app-layout>











