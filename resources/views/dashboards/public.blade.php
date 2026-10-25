<x-app-layout>
    <div class="space-y-6">
        <div class="rounded-3xl bg-white border border-rose-100 shadow-sm p-7">
            <p class="text-xs font-bold uppercase tracking-widest text-pink-500">
                ROADWATCH / PUBLIC MONITORING
            </p>
            <h1 class="text-3xl font-extrabold text-slate-900 mt-2">
                Dashboard Masyarakat
            </h1>
            <p class="text-sm text-slate-500 mt-2">
                Informasi kondisi jalan yang terpantau melalui RoadWatch.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <a href="{{ route('map.index') }}"
               class="bg-white rounded-2xl border border-rose-100 p-7 shadow-sm hover:shadow-md transition">
                <i class="fa-solid fa-map-location-dot text-pink-500 text-2xl"></i>
                <h2 class="font-extrabold text-slate-800 mt-4">
                    Peta Kondisi Jalan
                </h2>
                <p class="text-sm text-slate-500 mt-2">
                    Lihat lokasi kerusakan jalan yang terpantau.
                </p>
            </a>

            <a href="{{ route('detections.index') }}"
               class="bg-white rounded-2xl border border-rose-100 p-7 shadow-sm hover:shadow-md transition">
                <i class="fa-solid fa-list-check text-blue-500 text-2xl"></i>
                <h2 class="font-extrabold text-slate-800 mt-4">
                    Informasi Deteksi
                </h2>
                <p class="text-sm text-slate-500 mt-2">
                    Lihat informasi dan status kondisi jalan.
                </p>
            </a>
        </div>
    </div>
</x-app-layout>
