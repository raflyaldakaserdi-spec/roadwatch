<x-app-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-900">
            Geospatial Map Monitoring
        </h1>
        <p class="text-sm text-slate-500 mt-1">
            Pemetaan sebaran seluruh lokasi jalan berlubang secara real-time
        </p>
    </div>

    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <div id="severityFilters" class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                <button type="button" data-severity="All" aria-pressed="true"
                    class="severity-filter px-3 py-2 rounded-lg border border-pink-400 bg-pink-50 text-pink-700">
                    All
                </button>
                <button type="button" data-severity="High" aria-pressed="false"
                    class="severity-filter px-3 py-2 rounded-lg border border-slate-200 text-slate-600">
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-red-500 mr-1"></span>High
                </button>
                <button type="button" data-severity="Medium" aria-pressed="false"
                    class="severity-filter px-3 py-2 rounded-lg border border-slate-200 text-slate-600">
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-orange-400 mr-1"></span>Medium
                </button>
                <button type="button" data-severity="Low" aria-pressed="false"
                    class="severity-filter px-3 py-2 rounded-lg border border-slate-200 text-slate-600">
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-blue-500 mr-1"></span>Low
                </button>
            </div>
            <div class="hidden flex flex-wrap items-center gap-4 text-xs font-semibold">
                <span class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                    High
                </span>
                <span class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-orange-400"></span>
                    Medium
                </span>
                <span class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                    Low
                </span>
            </div>

            <span id="mapUpdateStatus" role="status"
                  class="text-xs font-semibold text-slate-500">
                Menghubungkan data...
            </span>
        </div>

        <div id="fullMap"
             style="height:580px;width:100%;border-radius:12px;z-index:1;">
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const map = L.map('fullMap').setView([-6.3721, 106.8312], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const markerLayer = L.layerGroup().addTo(map);
        const statusElement = document.getElementById('mapUpdateStatus');
        const endpoint = @json(route('map.live-data'));

        let initialized = false;
        let loading = false;
        let previousSignature = null;
        let activeSeverity = 'All';
        let latestDetections = [];
        const filterContainer = document.getElementById('severityFilters');

        function applySeverityFilter() {
            const filtered = activeSeverity === 'All'
                ? latestDetections
                : latestDetections.filter(item => item.severity === activeSeverity);

            const result = renderMarkers(filtered);

            statusElement.textContent =
                result.detections + ' deteksi | ' +
                result.locations + ' lokasi | ' +
                (activeSeverity === 'All' ? 'Semua severity' : activeSeverity);

            return result;
        }

        const initialColors = {
            All: ['#334155', '#f1f5f9'],
            High: ['#dc2626', '#fef2f2'],
            Medium: ['#ea580c', '#fff7ed'],
            Low: ['#2563eb', '#eff6ff']
        };

        filterContainer.querySelectorAll('button[data-severity]').forEach(function (item) {
            const selected = item.dataset.severity === activeSeverity;
            const palette = initialColors[item.dataset.severity];

            item.style.backgroundColor = selected ? palette[0] : palette[1];
            item.style.color = selected ? '#ffffff' : palette[0];
            item.style.borderColor = selected ? palette[0] : palette[1];
            item.style.transition = 'all 0.2s ease';
        });

        filterContainer.addEventListener('click', function (event) {
            const button = event.target.closest('button[data-severity]');
            if (!button || !filterContainer.contains(button)) return;

            activeSeverity = button.dataset.severity;

            filterContainer.querySelectorAll('button[data-severity]').forEach(function (item) {
                const selected = item.dataset.severity === activeSeverity;

                item.setAttribute('aria-pressed', String(selected));

                const colors = {
                    All: ['#334155', '#f1f5f9'],
                    High: ['#dc2626', '#fef2f2'],
                    Medium: ['#ea580c', '#fff7ed'],
                    Low: ['#2563eb', '#eff6ff']
                };

                const palette = colors[item.dataset.severity];

                item.style.backgroundColor = selected ? palette[0] : palette[1];
                item.style.color = selected ? '#ffffff' : palette[0];
                item.style.borderColor = selected ? palette[0] : palette[1];
                item.style.transition = 'all 0.2s ease';
            });

            applySeverityFilter();
        });

        const severityColors = {
            High: '#ef4444',
            Medium: '#fb923c',
            Low: '#3b82f6'
        };

        function createMarkerIcon(severity, count) {
            const color = severityColors[severity] || '#64748b';

            const iconHtml = document.createElement('div');
            iconHtml.style.cssText =
                'width:32px;height:32px;border-radius:50%;' +
                'background:' + color + ';border:3px solid white;' +
                'box-shadow:0 2px 8px #0004;display:flex;' +
                'align-items:center;justify-content:center;' +
                'color:white;font-size:12px;font-weight:800;';

            iconHtml.textContent = String(count);

            return L.divIcon({
                html: iconHtml.outerHTML,
                className: '',
                iconSize: [32, 32],
                iconAnchor: [16, 16],
                popupAnchor: [0, -18]
            });
        }

        function createPopup(items) {
            const container = document.createElement('div');
            container.style.cssText =
                'min-width:200px;max-width:280px;max-height:300px;' +
                'overflow-y:auto;';

            const heading = document.createElement('div');
            heading.style.cssText =
                'font-weight:800;font-size:13px;margin-bottom:8px;';

            heading.textContent =
                items.length + ' Deteksi pada lokasi ini';

            container.appendChild(heading);

            items.forEach(function (item) {
                const section = document.createElement('div');
                section.style.cssText =
                    'border-top:1px solid #e2e8f0;padding:8px 0;';

                if (item.image) {
                    const img = document.createElement('img');
                    img.src = item.image;
                    img.alt = 'Foto deteksi';
                    img.style.cssText =
                        'width:100%;height:90px;object-fit:cover;' +
                        'border-radius:8px;margin-bottom:6px;';
                    section.appendChild(img);
                }

                const code = document.createElement('div');
                code.textContent = item.detection_code || '-';
                code.style.cssText =
                    'font-weight:800;color:#2563eb;font-size:12px;';
                section.appendChild(code);

                const location = document.createElement('div');
                location.textContent = item.location || 'Lokasi tidak tersedia';
                location.style.cssText =
                    'font-size:12px;margin:4px 0;';
                section.appendChild(location);

                const info = document.createElement('div');
                info.textContent =
                    'Severity: ' + (item.severity || '-') +
                    ' | Status: ' + (item.status || '-');
                info.style.cssText =
                    'font-size:11px;color:#475569;margin-bottom:6px;';
                section.appendChild(info);

                const link = document.createElement('a');
                link.href = '/detections/' + encodeURIComponent(item.id);
                link.textContent = 'Lihat Detail →';
                link.style.cssText =
                    'font-size:11px;color:#2563eb;font-weight:700;';
                section.appendChild(link);

                container.appendChild(section);
            });

            return container;
        }

        function renderMarkers(detections) {
            const groups = new Map();

            detections.forEach(function (item) {
                const lat = Number(item.latitude);
                const lng = Number(item.longitude);

                if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;
                if (lat < -90 || lat > 90 || lng < -180 || lng > 180) return;

                const key = lat.toFixed(6) + ',' + lng.toFixed(6);

                if (!groups.has(key)) {
                    groups.set(key, {
                        lat: lat,
                        lng: lng,
                        items: []
                    });
                }

                groups.get(key).items.push(item);
            });

            markerLayer.clearLayers();

            const bounds = [];

            groups.forEach(function (group) {
                const items = group.items;

                const severity = items.some(x => x.severity === 'High')
                    ? 'High'
                    : items.some(x => x.severity === 'Medium')
                        ? 'Medium'
                        : 'Low';

                const marker = L.marker(
                    [group.lat, group.lng],
                    {
                        icon: createMarkerIcon(severity, items.length)
                    }
                );

                marker.bindPopup(createPopup(items));
                markerLayer.addLayer(marker);

                bounds.push([group.lat, group.lng]);
            });

            if (!initialized && bounds.length > 0) {
                map.fitBounds(bounds, {
                    padding: [35, 35],
                    maxZoom: 13
                });
            }

            initialized = true;

            return {
                locations: groups.size,
                detections: detections.length
            };
        }

        async function refreshMap() {
            if (loading || document.hidden) return;

            loading = true;

            try {
                const response = await fetch(endpoint, {
                    headers: {
                        Accept: 'application/json'
                    },
                    credentials: 'same-origin',
                    cache: 'no-store'
                });

                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }

                const data = await response.json();

                if (!Array.isArray(data.detections)) {
                    throw new Error('Format data tidak sesuai');
                }

                const signature = JSON.stringify(data.detections);

                if (signature !== previousSignature) {
                    latestDetections = data.detections;
                    applySeverityFilter();
                    previousSignature = signature;
                } else {
                    statusElement.textContent =
                        'Data tersinkronisasi | ' +
                        (activeSeverity === 'All' ? 'Semua severity' : activeSeverity) +
                        ' | ' + new Date().toLocaleTimeString('id-ID');
                }
            } catch (error) {
                console.error('Map Monitoring:', error);
                statusElement.textContent =
                    'Gagal memperbarui peta. Mencoba kembali...';
            } finally {
                loading = false;
            }
        }

        refreshMap();

        setInterval(refreshMap, 10000);

        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) refreshMap();
        });

        setTimeout(function () {
            map.invalidateSize();
        }, 300);
    });
    </script>
</x-app-layout>
