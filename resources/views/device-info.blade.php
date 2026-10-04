<x-app-layout>
    <!-- Header Page -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">IoT Device Information & Status</h1>
        <p class="text-sm text-slate-500 mt-1">Status konektivitas dan panduan integrasi REST API perangkat keras ESP32</p>
    </div>

    <!-- System Status Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Connection Status</p>
                <div class="flex items-center gap-2 mt-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                    <h3 class="text-lg font-bold text-slate-800">{{ $deviceStatus['status'] }}</h3>
                </div>
            </div>
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                <i class="fa-solid fa-wifi text-xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Main MCU Module</p>
                <h3 class="text-sm font-bold text-slate-800 mt-2">{{ $deviceStatus['mcu'] }}</h3>
            </div>
            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                <i class="fa-solid fa-microchip text-xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Vision Sensor</p>
                <h3 class="text-sm font-bold text-slate-800 mt-2">{{ $deviceStatus['camera_module'] }}</h3>
            </div>
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                <i class="fa-solid fa-camera text-xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Geo-Positioning</p>
                <h3 class="text-sm font-bold text-slate-800 mt-2">{{ $deviceStatus['gps_module'] }}</h3>
            </div>
            <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                <i class="fa-solid fa-location-crosshairs text-xl"></i>
            </div>
        </div>
    </div>

    <!-- API Integration Specification Card -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <h2 class="text-lg font-bold text-slate-900 mb-2 flex items-center gap-2">
            <i class="fa-solid fa-code text-blue-600"></i>
            Hardware REST API Endpoint Specification
        </h2>
        <p class="text-xs text-slate-500 mb-6">Gunakan format HTTP POST JSON berikut dari ESP32 untuk mengirimkan data hasil deteksi ke sistem ini:</p>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Target Endpoint URL</label>
                <div class="flex items-center gap-2">
                    <input type="text" readonly value="{{ $deviceStatus['api_endpoint'] }}" class="w-full font-mono text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 text-blue-600 font-bold">
                    <span class="px-3 py-2 bg-emerald-100 text-emerald-800 font-bold text-xs rounded-xl">POST</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">JSON Payload Format (ESP32 Send)</label>
                <pre class="bg-slate-900 text-slate-100 p-4 rounded-xl text-xs font-mono overflow-x-auto">
{
  "location": "Jl. Margonda Raya, Depok",
  "latitude": -6.37210000,
  "longitude": 106.83120000,
  "severity": "High",
  "image_base64": "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQ..."
}
                </pre>
            </div>
        </div>
    </div>
</x-app-layout>