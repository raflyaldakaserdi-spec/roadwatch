<x-app-layout>
    <!-- TOPBAR SEARCH & PROFILE HEADER -->
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Dashboard Utama</h1>
            <p class="text-xs text-slate-500 mt-0.5 font-medium">Ringkasan kondisi jalan dan aktivitas sensor terkini.</p>
        </div>

        <div class="flex items-center gap-3">
            <!-- Search Bar -->
            <div class="relative min-w-[320px]">
                <input type="text" placeholder="Cari lokasi, kode ID, atau koordinat..." 
                       class="w-full pl-4 pr-10 py-2 bg-white border border-purple-100/80 rounded-2xl text-xs text-slate-700 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-violet-500 shadow-sm transition">
                <i class="fa-solid fa-magnifying-glass absolute right-3.5 top-3 text-slate-400 text-xs"></i>
            </div>

            <!-- Notification Bell Icon -->
            <button class="w-9 h-9 rounded-2xl bg-white border border-purple-100/80 shadow-sm flex items-center justify-center text-slate-600 hover:text-violet-600 transition">
                <i class="fa-regular fa-bell text-sm"></i>
            </button>

            <!-- Profile User Icon -->
            <div class="w-9 h-9 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                <i class="fa-solid fa-user"></i>
            </div>

            <!-- Status Indicator Badge -->
            <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-2xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Status: Terhubung (Mode Pengujian)
                <i class="fa-solid fa-signal text-[10px] ml-0.5"></i>
            </span>
        </div>
    </div>

    <!-- 1. TOP STAT CARDS (GRID 4 COLS) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <!-- Card 1: Total Temuan -->
        <div class="bg-white rounded-2xl p-5 border border-purple-100/60 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                        <i class="fa-solid fa-chart-pie text-xs"></i>
                    </div>
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider">TOTAL TEMUAN</span>
                </div>
                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full flex items-center gap-1">
                    <i class="fa-solid fa-arrow-trend-up"></i> +12.4% <span class="text-slate-400 font-normal">vs bulan lalu</span>
                </span>
            </div>
            <div class="flex items-end justify-between">
                <div>
                    <h3 class="text-3xl font-extrabold text-slate-900 font-mono-tech">{{ $totalDetections ?? 6 }}</h3>
                    <p class="text-[11px] text-slate-400 font-medium mt-1">Kumulatif seluruh jalan berlubang</p>
                </div>
                <svg class="w-16 h-8 text-purple-500" viewBox="0 0 100 40" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M0 30 Q25 10 50 25 T100 5" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        <!-- Card 2: Temuan Hari Ini -->
        <div class="bg-white rounded-2xl p-5 border border-purple-100/60 shadow-sm flex flex-col justify-between">
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
                    <h3 class="text-3xl font-extrabold text-slate-900 font-mono-tech">{{ $detectionsToday ?? 6 }}</h3>
                    <p class="text-[11px] text-slate-400 font-medium mt-1">Data masuk hari ini</p>
                </div>
                <svg class="w-16 h-8 text-rose-500" viewBox="0 0 100 40" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M0 25 Q30 35 60 15 T100 10" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        <!-- Card 3: Minggu Ini -->
        <div class="bg-white rounded-2xl p-5 border border-purple-100/60 shadow-sm flex flex-col justify-between">
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
                    <h3 class="text-3xl font-extrabold text-slate-900 font-mono-tech">{{ $detectionsThisWeek ?? 6 }}</h3>
                    <p class="text-[11px] text-slate-400 font-medium mt-1">Akumulasi 7 hari terakhir</p>
                </div>
                <svg class="w-16 h-8 text-blue-500" viewBox="0 0 100 40" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M0 35 Q20 5 50 20 T100 15" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        <!-- Card 4: Bulan Ini -->
        <div class="bg-white rounded-2xl p-5 border border-purple-100/60 shadow-sm flex flex-col justify-between">
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
                    <h3 class="text-3xl font-extrabold text-slate-900 font-mono-tech">{{ $detectionsThisMonth ?? 6 }}</h3>
                    <p class="text-[11px] text-slate-400 font-medium mt-1">Bulan berjalan</p>
                </div>
                <svg class="w-16 h-8 text-emerald-500" viewBox="0 0 100 40" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M0 20 Q40 30 70 10 T100 5" stroke-linecap="round"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- 2. MIDDLE SECTION GRID: 6 COLS (MAP) + 3 COLS (LIVE) + 3 COLS (HARDWARE) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mb-6 items-stretch">
        
        <!-- Peta Monitoring Jalan (Span 6) -->
        <div class="lg:col-span-6 bg-white rounded-2xl p-5 border border-purple-100/60 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-3">
                <h2 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-violet-600"></i>
                    Peta Monitoring Jalan
                </h2>
                <!-- Legend Pins -->
                <div class="flex items-center gap-3 text-[10px] font-extrabold">
                    <span class="flex items-center gap-1.5 text-slate-600"><span class="w-2 h-2 rounded-full bg-rose-500"></span> High</span>
                    <span class="flex items-center gap-1.5 text-slate-600"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Medium</span>
                    <span class="flex items-center gap-1.5 text-slate-600"><span class="w-2 h-2 rounded-full bg-sky-500"></span> Low</span>
                </div>
            </div>
            
            <div id="dashboardMap" style="height: 290px; width: 100%; border-radius: 12px; z-index: 1;" class="border border-slate-100"></div>
        </div>

        <!-- Live Detection (Span 3) -->
        <div class="lg:col-span-3 bg-white rounded-2xl p-5 border border-purple-100/60 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-extrabold text-slate-900 text-sm flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                        Live Detection
                    </h2>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-600">Terbaru</span>
                </div>

                <!-- Preview Image -->
                <div class="relative aspect-video rounded-xl overflow-hidden mb-3 border border-slate-200">
                    <img src="https://images.unsplash.com/photo-1515162816999-a0c47dc192f7?w=600" alt="Pothole Visual" class="w-full h-full object-cover">
                    <span class="absolute bottom-2 left-2 px-2 py-0.5 rounded-md bg-rose-600 text-white font-extrabold text-[10px] uppercase">HIGH</span>
                </div>

                <h3 class="font-extrabold text-slate-900 text-sm mb-3">Jl. Raya Bogor KM 29</h3>

                <div class="space-y-2 text-xs">
                    <div class="flex justify-between items-center text-slate-500">
                        <span class="flex items-center gap-1.5"><i class="fa-solid fa-bullseye text-[10px]"></i> Confidence</span>
                        <span class="font-mono-tech font-bold text-slate-800">94.8%</span>
                    </div>
                    <div class="flex justify-between items-center text-slate-500">
                        <span class="flex items-center gap-1.5"><i class="fa-solid fa-location-crosshairs text-[10px]"></i> GPS</span>
                        <span class="font-mono-tech font-bold text-slate-800">-6.3612, 106.8451</span>
                    </div>
                    <div class="flex justify-between items-center text-slate-500">
                        <span class="flex items-center gap-1.5"><i class="fa-regular fa-clock text-[10px]"></i> Deteksi pada</span>
                        <span class="font-mono-tech font-bold text-slate-800">17:32:08</span>
                    </div>
                </div>
            </div>

            <a href="{{ route('detections.show', 3) }}" class="mt-4 w-full py-2 bg-violet-50 hover:bg-violet-600 text-violet-600 hover:text-white rounded-xl text-xs font-extrabold text-center transition flex items-center justify-center gap-1.5">
                <span>Lihat Detail</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <!-- Status Perangkat & Sensor (Span 3) -->
        <div class="lg:col-span-3 bg-white rounded-2xl p-5 border border-purple-100/60 shadow-sm flex flex-col justify-between">
            <h2 class="font-extrabold text-slate-900 text-sm mb-3 flex items-center gap-2">
                <i class="fa-solid fa-sliders text-violet-600"></i>
                Status Perangkat & Sensor
            </h2>

            <div class="space-y-2.5">
                <!-- Hardware 1 -->
                <div class="flex items-start gap-2.5 p-1.5 rounded-xl hover:bg-slate-50 transition">
                    <div class="p-2 bg-purple-50 text-purple-600 rounded-lg shrink-0">
                        <i class="fa-solid fa-microchip text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-xs text-slate-900 truncate">ESP32 Main MCU</h4>
                            <span class="text-[10px] font-bold text-emerald-600 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Online &rarr;</span>
                        </div>
                        <p class="text-[10px] text-slate-400 line-clamp-1">Mikrokontroler utama pengolah data & HTTP POST payload API.</p>
                    </div>
                </div>

                <!-- Hardware 2 -->
                <div class="flex items-start gap-2.5 p-1.5 rounded-xl hover:bg-slate-50 transition">
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg shrink-0">
                        <i class="fa-solid fa-camera text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-xs text-slate-900 truncate">ESP32-CAM</h4>
                            <span class="text-[10px] font-bold text-emerald-600 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Online &rarr;</span>
                        </div>
                        <p class="text-[10px] text-slate-400 line-clamp-1">Modul kamera visual untuk menangkap kondisi permukaan jalan secara konstan.</p>
                    </div>
                </div>

                <!-- Hardware 3 -->
                <div class="flex items-start gap-2.5 p-1.5 rounded-xl hover:bg-slate-50 transition">
                    <div class="p-2 bg-violet-50 text-violet-600 rounded-lg shrink-0">
                        <i class="fa-solid fa-brain text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-xs text-slate-900 truncate">Algoritma FOMO</h4>
                            <span class="text-[10px] font-bold text-emerald-600 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Online &rarr;</span>
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
                            <h4 class="font-bold text-xs text-slate-900 truncate">GPS Neo-6M</h4>
                            <span class="text-[10px] font-bold text-emerald-600 flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Online &rarr;</span>
                        </div>
                        <p class="text-[10px] text-slate-400 line-clamp-1">Menangkap koordinat presisi spasial (Latitude & Longitude) lokasi jalan berlubang.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. RIWAYAT DETEKSI TERAKHIR TABLE SECTION -->
    <div class="bg-white rounded-2xl border border-purple-100/60 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-violet-600"></i>
                    Riwayat Deteksi Terakhir
                </h2>
                <p class="text-xs text-slate-400 font-medium">Log kejadian temuan lubang jalan terbaru dari sensor</p>
            </div>
            <a href="{{ route('detections.index') }}" class="text-xs font-extrabold text-violet-600 hover:text-violet-800 flex items-center gap-1 transition">
                <span>Lihat Semua Data</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

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
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="p-3.5 font-bold font-mono-tech text-violet-600">PTH-0003</td>
                        <td class="p-3.5 font-semibold text-slate-800">Jl. Raya Bogor KM 29</td>
                        <td class="p-3.5 font-mono-tech text-slate-500 text-[11px]">-6.3612, 106.8451</td>
                        <td class="p-3.5 font-mono-tech text-slate-600">2026-09-30 <span class="text-slate-400">(08:40:15)</span></td>
                        <td class="p-3.5"><span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-[#FFE2E2] text-[#9B1C1C]">High</span></td>
                        <td class="p-3.5"><span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-[#FEF3C7] text-[#92400E]">Detected</span></td>
                        <td class="p-3.5 text-center">
                            <a href="{{ route('detections.show', 3) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-violet-50 text-violet-600 hover:bg-violet-600 hover:text-white rounded-xl font-bold transition">
                                <i class="fa-solid fa-eye text-[11px]"></i> Detail
                            </a>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="p-3.5 font-bold font-mono-tech text-violet-600">PTH-0004</td>
                        <td class="p-3.5 font-semibold text-slate-800">Jl. Sawangan Raya, Depok</td>
                        <td class="p-3.5 font-mono-tech text-slate-500 text-[11px]">-6.3988, 106.7925</td>
                        <td class="p-3.5 font-mono-tech text-slate-600">2026-09-30 <span class="text-slate-400">(09:15:20)</span></td>
                        <td class="p-3.5"><span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-[#E0F2FE] text-[#0369A1]">Low</span></td>
                        <td class="p-3.5"><span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-[#E0F2FE] text-[#0369A1]">In Progress</span></td>
                        <td class="p-3.5 text-center">
                            <a href="{{ route('detections.show', 4) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-violet-50 text-violet-600 hover:bg-violet-600 hover:text-white rounded-xl font-bold transition">
                                <i class="fa-solid fa-eye text-[11px]"></i> Detail
                            </a>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="p-3.5 font-bold font-mono-tech text-violet-600">PTH-0005</td>
                        <td class="p-3.5 font-semibold text-slate-800">Jl. Akses UI, Kelapa Dua</td>
                        <td class="p-3.5 font-mono-tech text-slate-500 text-[11px]">-6.3541, 106.8423</td>
                        <td class="p-3.5 font-mono-tech text-slate-600">2026-09-30 <span class="text-slate-400">(14:02:11)</span></td>
                        <td class="p-3.5"><span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-[#FEF3C7] text-[#92400E]">Medium</span></td>
                        <td class="p-3.5"><span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-[#D1FAE5] text-[#065F46]">Selesai</span></td>
                        <td class="p-3.5 text-center">
                            <a href="{{ route('detections.show', 5) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-violet-50 text-violet-600 hover:bg-violet-600 hover:text-white rounded-xl font-bold transition">
                                <i class="fa-solid fa-eye text-[11px]"></i> Detail
                            </a>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="p-3.5 font-bold font-mono-tech text-violet-600">PTH-0006</td>
                        <td class="p-3.5 font-semibold text-slate-800">Jl. Cinere Raya, Depok</td>
                        <td class="p-3.5 font-mono-tech text-slate-500 text-[11px]">-6.3271, 106.7812</td>
                        <td class="p-3.5 font-mono-tech text-slate-600">2026-09-30 <span class="text-slate-400">(15:30:00)</span></td>
                        <td class="p-3.5"><span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-[#FFE2E2] text-[#9B1C1C]">High</span></td>
                        <td class="p-3.5"><span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-[#FEF3C7] text-[#92400E]">Detected</span></td>
                        <td class="p-3.5 text-center">
                            <a href="{{ route('detections.show', 6) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-violet-50 text-violet-600 hover:bg-violet-600 hover:text-white rounded-xl font-bold transition">
                                <i class="fa-solid fa-eye text-[11px]"></i> Detail
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Leaflet JS Setup for Middle Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var dashMap = L.map('dashboardMap').setView([-6.37210000, 106.83120000], 11);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(dashMap);

            L.marker([-6.3612, 106.8451]).addTo(dashMap).bindPopup("<b>PTH-0003</b><br>Jl. Raya Bogor KM 29");
            L.marker([-6.3988, 106.7925]).addTo(dashMap).bindPopup("<b>PTH-0004</b><br>Jl. Sawangan Raya");
            L.marker([-6.3541, 106.8423]).addTo(dashMap).bindPopup("<b>PTH-0005</b><br>Jl. Akses UI");

            setTimeout(function() {
                dashMap.invalidateSize();
            }, 300);
        });
    </script>
</x-app-layout>