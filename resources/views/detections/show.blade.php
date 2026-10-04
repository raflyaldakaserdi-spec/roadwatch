<x-app-layout>
    <!-- Header Page & Back Button -->
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('detections.index') }}" class="p-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 transition shadow-sm">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Detection Detail - {{ $detection->detection_code }}</h1>
                <p class="text-sm text-slate-500">Informasi spasial dan visual kondisi jalan berlubang</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <!-- Tombol Buka di Google Maps -->
            <a href="https://www.google.com/maps/search/?api=1&query={{ $detection->latitude }},{{$detection->longitude }}" 
               target="_blank" 
               class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs rounded-xl transition shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-location-arrow"></i>
                <span>Open in Google Maps</span>
            </a>

            <span class="px-3 py-2 rounded-xl text-xs font-semibold
                {{ $detection->status == 'Detected' ? 'bg-amber-100 text-amber-800 border border-amber-200' : '' }}
                {{ $detection->status == 'In Progress' ? 'bg-blue-100 text-blue-800 border border-blue-200' : '' }}                 {{$detection->status == 'Repaired' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : '' }}">
                Status: {{ $detection->status }}
            </span>
        </div>
    </div>

    <!-- Detail Grid Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Image & Metadata Card -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-5">
            <div class="aspect-video w-full rounded-xl overflow-hidden bg-slate-100 border border-slate-200">
                <img src="{{ $detection->image }}" alt="Pothole Visual" class="w-full h-full object-cover">
            </div>

            <div class="space-y-3 pt-2">
                <div class="flex justify-between items-center py-2 border-b border-slate-100">
                    <span class="text-xs text-slate-400 font-medium">Detection ID</span>
                    <span class="text-xs font-bold text-blue-600 font-mono">{{ $detection->detection_code }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-100">
                    <span class="text-xs text-slate-400 font-medium">Location Name</span>
                    <span class="text-xs font-semibold text-slate-800 truncate max-w-[180px]">{{ $detection->location }}</span>
                </div>

                <!-- Link Klik Koordinat ke Google Maps -->
                <div class="flex justify-between items-center py-2 border-b border-slate-100">
                    <span class="text-xs text-slate-400 font-medium">Coordinates</span>
                    <a href="https://www.google.com/maps/search/?api=1&query={{ $detection->latitude }},{{$detection->longitude }}" 
                       target="_blank" 
                       class="text-xs font-mono text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-1 font-semibold" 
                       title="Klik untuk membuka di Google Maps">
                        <span>{{ $detection->latitude }}, {{$detection->longitude }}</span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                    </a>
                </div>

                <div class="flex justify-between items-center py-2 border-b border-slate-100">
                    <span class="text-xs text-slate-400 font-medium">Date & Day</span>
                    <span class="text-xs font-medium text-slate-800">{{ $detection->detection_date }} ({{$detection->detection_day }})</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-slate-100">
                    <span class="text-xs text-slate-400 font-medium">Time</span>
                    <span class="text-xs font-mono text-slate-600">{{ $detection->detection_time }}</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-xs text-slate-400 font-medium">Severity</span>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold
                        {{ $detection->severity == 'High' ? 'bg-rose-50 text-rose-600 border border-rose-200' : '' }}
                        {{ $detection->severity == 'Medium' ? 'bg-amber-50 text-amber-600 border border-amber-200' : '' }}                         {{$detection->severity == 'Low' ? 'bg-blue-50 text-blue-600 border border-blue-200' : '' }}">
                        {{ $detection->severity }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Interactive Map Location (Leaflet.js) -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-blue-600"></i>
                    Geospatial Location Map
                </h2>
                <!-- Link Buka Peta Full Screen di Website -->
                <a href="{{ route('map.index') }}" class="text-xs text-blue-600 hover:underline font-medium flex items-center gap-1">
                    <span>View All Devices Map</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>
            <div id="singleMap" style="height: 380px; width: 100%; border-radius: 12px; z-index: 1;" class="border border-slate-200 overflow-hidden"></div>
        </div>
    </div>

    <!-- Leaflet JS & CSS Setup -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var lat = parseFloat({{ $detection->latitude }});
            var lng = parseFloat({{ $detection->longitude }});
            
            var map = L.map('singleMap').setView([lat, lng], 16);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            var popupContent = `
                <div style="min-width: 150px; text-align: left;">
                    <div style="font-weight: bold; color: #2563eb; font-size: 11px;">{{ $detection->detection_code }}</div>
                    <div style="font-semibold; font-size: 12px; color: #1e293b; margin-bottom: 6px;">{{ $detection->location }}</div>
                    <a href="https://www.google.com/maps/search/?api=1&query=${lat},${lng}" target="_blank" style="color: #059669; font-size: 11px; font-weight: bold; text-decoration: none;">Open in Google Maps &rarr;</a>
                </div>
            `;

            L.marker([lat, lng]).addTo(map)
                .bindPopup(popupContent)
                .openPopup();

            setTimeout(function() {
                map.invalidateSize();
            }, 300);
        });
    </script>
</x-app-layout>