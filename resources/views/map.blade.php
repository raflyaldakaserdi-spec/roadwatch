<x-app-layout>
    <!-- Header Page -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Geospatial Map Monitoring</h1>
            <p class="text-sm text-slate-500 mt-1">Pemetaan sebaran seluruh lokasi jalan berlubang secara real-time</p>
        </div>
    </div>

    <!-- Leaflet JS & CSS Setup -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Map Container Card -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
        <div id="fullMap" style="height: 580px; width: 100%; border-radius: 12px; z-index: 1;"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Inisialisasi Peta
            var map = L.map('fullMap').setView([-6.37210000, 106.83120000], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Fetch data dari variabel PHP Laravel
            var detections = @json($detections);

            if (Array.isArray(detections) && detections.length > 0) {
                detections.forEach(function (item) {
                    if (item.latitude && item.longitude) {
                        var marker = L.marker([parseFloat(item.latitude), parseFloat(item.longitude)]).addTo(map);

                        var popupContent = `
                            <div style="min-width: 180px; text-align: left;">
                                <img src="${item.image}" style="width: 100%; height: 90px; object-fit: cover; border-radius: 8px; margin-bottom: 8px;">
                                <div style="font-weight: bold; color: #2563eb; font-size: 11px;">${item.detection_code}</div>
                                <div style="font-weight: 600; font-size: 12px; color: #1e293b; margin-bottom: 4px;">${item.location}</div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 6px;">
                                    <span style="background-color: #fef3c7; color: #92400e; font-size: 10px; padding: 2px 6px; border-radius: 4px; font-weight: bold;">${item.status}</span>
                                    <a href="/detections/${item.id}" style="color: #2563eb; font-size: 11px; font-weight: 600; text-decoration: none;">Detail &rarr;</a>
                                </div>
                            </div>
                        `;

                        marker.bindPopup(popupContent);
                    }
                });
            }

            // Memicu penyesuaian ukuran peta setelah render DOM
            setTimeout(function() {
                map.invalidateSize();
            }, 300);
        });
    </script>
</x-app-layout>