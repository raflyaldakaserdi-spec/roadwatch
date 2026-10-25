<x-app-layout>
    <!-- RoadWatch Device Info Premium V2 -->

    <div class="rw-device-page">

        <!-- Page Header -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                    IoT Device Control Center
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Monitoring perangkat Edge AI, GPS, dan integrasi REST API RoadWatch
                </p>
            </div>

            <span id="rwLiveBadge" class="rw-device-badge {{ $deviceStatus['status'] === 'Online' ? 'rw-online' : 'rw-offline' }}">
                <span class="rw-device-dot"></span>
                {{ $deviceStatus['status'] }}
            </span>
        </div>

        <!-- Hardware Overview -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6">

            <div class="rw-device-card">
                <div class="rw-device-card-head">
                    <span>Main Controller</span>
                    <i class="fa-solid fa-microchip rw-icon-pink"></i>
                </div>
                <h3>{{ $deviceStatus['mcu'] }}</h3>
                <p>Edge Computing Unit</p>
            </div>

            <div class="rw-device-card">
                <div class="rw-device-card-head">
                    <span>AI Vision Model</span>
                    <i class="fa-solid fa-eye rw-icon-blue"></i>
                </div>
                <h3>{{ $deviceStatus['model'] }}</h3>
                <p>On-device AI Detection</p>
            </div>

            <div class="rw-device-card">
                <div class="rw-device-card-head">
                    <span>GPS Module</span>
                    <i class="fa-solid fa-location-crosshairs rw-icon-orange"></i>
                </div>
                <h3>{{ $deviceStatus['gps_module'] }}</h3>
                <p>Geo-positioning Hardware</p>
            </div>

            <div class="rw-device-card">
                <div class="rw-device-card-head">
                    <span>Firmware Version</span>
                    <i class="fa-solid fa-code-branch rw-icon-green"></i>
                </div>
                <h3><span id="rwLiveFirmware">{{ $deviceStatus['firmware_version'] }}</span></h3>
                <p>Reported by Device</p>
            </div>

        </div>

        <!-- Device Status & Telemetry -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">

            <!-- Device Identity -->
            <div class="rw-device-panel">
                <div class="rw-panel-title">
                    <i class="fa-solid fa-circle-info text-pink-500"></i>
                    Device Information
                </div>

                <div class="rw-info-row">
                    <span>Device Name</span>
                    <strong>{{ $deviceStatus['device_name'] }}</strong>
                </div>

                <div class="rw-info-row">
                    <span>Device Code</span>
                    <strong class="font-mono">{{ $deviceStatus['device_code'] }}</strong>
                </div>

                <div class="rw-info-row">
                    <span>Camera Module</span>
                    <strong>{{ $deviceStatus['camera_module'] }}</strong>
                </div>

                <div class="rw-info-row">
                    <span>Last Heartbeat</span>
                    <strong><span id="rwLiveHeartbeat">{{ $deviceStatus['last_ping'] }}{{ $deviceStatus['last_ping'] !== 'Never' ? ' WIB' : '' }}</span></strong>
                </div>

                <div class="rw-info-row">
                    <span>Connection</span>
                    <strong id="rwLiveConnection" class="{{ $deviceStatus['status'] === 'Online' ? 'text-emerald-600' : 'text-red-500' }}">
                        {{ $deviceStatus['status'] }}
                    </strong>
                </div>
            </div>

            <!-- Sensor Telemetry -->
            <div class="rw-device-panel">
                <div class="rw-panel-title">
                    <i class="fa-solid fa-satellite-dish text-blue-500"></i>
                    Sensor Health & GPS Telemetry
                </div>

                <div class="rw-info-row">
                    <span>Last Reported GPS Status</span>
                    <strong id="rwLiveGps">{{ $deviceStatus['gps_status'] }}</strong>
                </div>

                <div class="rw-info-row">
                    <span>Latitude</span>
                    <strong id="rwLiveLatitude" class="font-mono">{{ $deviceStatus['latitude'] ?? 'Not Available' }}</strong>
                </div>

                <div class="rw-info-row">
                    <span>Longitude</span>
                    <strong id="rwLiveLongitude" class="font-mono">{{ $deviceStatus['longitude'] ?? 'Not Available' }}</strong>
                </div>

                <div class="rw-info-row">
                    <span>Camera Hardware</span>
                    <strong>{{ $deviceStatus['camera_module'] }}</strong>
                </div>

                <div class="rw-info-row">
                    <span>Camera Health</span>
                    <strong class="text-slate-400">Not Reported</strong>
                </div>

                <p class="text-xs text-slate-400 mt-4 leading-relaxed">
                    GPS dan koordinat berasal dari telemetry terakhir yang dilaporkan perangkat.
                    Status kamera belum memiliki mekanisme heartbeat tersendiri.
                </p>
            </div>

        </div>

        <!-- API Integration -->
        <div class="rw-device-panel">
            <div class="rw-panel-title mb-2">
                <i class="fa-solid fa-code text-pink-500"></i>
                REST API Integration Center
            </div>

            <p class="text-xs text-slate-500 mb-5">
                Endpoint pengiriman data deteksi dari perangkat ESP32 ke backend RoadWatch.
            </p>

            <label class="rw-api-label">Detection API Endpoint</label>

            <div class="flex flex-col sm:flex-row gap-2 mb-5">
                <input
                    id="rwDeviceEndpoint"
                    type="text"
                    readonly
                    value="{{ $deviceStatus['api_endpoint'] }}"
                    class="flex-1 min-w-0 bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-xs font-mono text-blue-600"
                >

                <div class="flex gap-2">
                    <span class="rw-method-badge">POST</span>

                    <button type="button"
                            onclick="rwCopyDeviceText('rwDeviceEndpoint', this)"
                            class="rw-copy-button">
                        <i class="fa-regular fa-copy"></i>
                        Copy URL
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between gap-3 mb-2">
                <label class="rw-api-label mb-0">JSON Payload Example</label>

                <button type="button"
                        onclick="rwCopyDeviceText('rwDeviceJson', this)"
                        class="rw-copy-button">
                    <i class="fa-regular fa-copy"></i>
                    Copy JSON
                </button>
            </div>

            <pre id="rwDeviceJson" class="rw-code-block">{
  "location": "Jl. Margonda Raya, Depok",
  "latitude": -6.37210000,
  "longitude": 106.83120000,
  "severity": "High",
  "image_base64": "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQ..."
}</pre>

            <div class="rw-api-note">
                <i class="fa-solid fa-circle-info"></i>
                <span>
                    Contoh JSON di atas merupakan referensi format payload,
                    bukan data aktual dari perangkat.
                    Pengiriman data menggunakan HTTPS POST.
                </span>
            </div>
        </div>
    </div>

<style>
/* RoadWatch Device Info Premium V2 */

.rw-device-card,
.rw-device-panel {
    background: #fff;
    border: 1px solid #f3e5ee;
    border-radius: 20px;
    box-shadow: 0 8px 24px rgba(158,92,134,.045);
}

.rw-device-card {
    padding: clamp(15px,1.8vw,23px);
    min-width: 0;
    transition: transform .2s ease, box-shadow .2s ease;
}

.rw-device-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 14px 32px rgba(158,92,134,.1);
}

.rw-device-card-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    color: #7d8195;
    font-size: 11px;
    font-weight: 800;
}

.rw-device-card-head i {
    display: grid;
    place-items: center;
    width: 35px;
    height: 35px;
    border-radius: 12px;
    flex-shrink: 0;
    font-size: 15px;
}

.rw-icon-pink { background:#fff0f7; color:#dc639f; }
.rw-icon-blue { background:#edf4ff; color:#648fdb; }
.rw-icon-orange { background:#fff6e8; color:#e6a044; }
.rw-icon-green { background:#eaf9f3; color:#39a581; }

.rw-device-card h3 {
    margin-top: 15px;
    font-size: 15px;
    font-weight: 800;
    line-height: 1.5;
    color: #252b43;
    overflow-wrap: anywhere;
}

.rw-device-card p {
    margin-top: 6px;
    font-size: 11px;
    color: #9795a6;
}

.rw-device-panel {
    padding: clamp(18px,2vw,25px);
    min-width: 0;
}

.rw-panel-title {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 18px;
    font-size: 15px;
    font-weight: 800;
    color: #252b43;
}

.rw-info-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 15px;
    padding: 13px 0;
    border-bottom: 1px solid #f3f1f5;
    font-size: 12px;
}

.rw-info-row:last-of-type {
    border-bottom: none;
}

.rw-info-row span {
    color: #8a8da0;
    flex-shrink: 0;
}

.rw-info-row strong {
    color: #30364e;
    font-weight: 700;
    text-align: right;
    overflow-wrap: anywhere;
}

.rw-device-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    align-self: flex-start;
    padding: 9px 14px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 800;
}

.rw-device-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: currentColor;
}

.rw-online {
    background: #e8f8f1;
    color: #169a70;
}

.rw-offline {
    background: #fff0f1;
    color: #df5966;
}

.rw-api-label {
    display: block;
    margin-bottom: 8px;
    font-size: 11px;
    font-weight: 800;
    color: #6d7287;
}

.rw-method-badge {
    display: inline-flex;
    align-items: center;
    padding: 0 15px;
    background: #e7f9f0;
    color: #17865e;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 800;
}

.rw-copy-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 15px;
    border-radius: 12px;
    background: #fff0f7;
    color: #cf5693;
    font-size: 11px;
    font-weight: 800;
    white-space: nowrap;
    transition: background .2s;
}

.rw-copy-button:hover {
    background: #fce0ef;
}

.rw-code-block {
    background: #10182e;
    color: #e2e8f0;
    padding: 20px;
    border-radius: 14px;
    font-size: 12px;
    line-height: 1.8;
    overflow-x: auto;
}

.rw-api-note {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-top: 18px;
    padding: 14px;
    border-radius: 12px;
    background: #f8f9fd;
    color: #737b91;
    font-size: 11px;
    line-height: 1.7;
}

.rw-api-note i {
    margin-top: 3px;
    color: #d96caa;
}

@media (max-width: 480px) {
    .rw-device-card {
        padding: 14px;
        border-radius: 16px;
    }

    .rw-device-card-head {
        font-size: 10px;
    }

    .rw-device-card-head i {
        width: 29px;
        height: 29px;
    }

    .rw-info-row {
        flex-direction: column;
        gap: 5px;
    }

    .rw-info-row strong {
        text-align: left;
    }
}

@media (prefers-reduced-motion: reduce) {
    .rw-device-card {
        transition: none;
    }
}
</style>

<script>
async function rwCopyDeviceText(id, button) {
    const element = document.getElementById(id);
    if (!element) return;

    const value = element.value ?? element.textContent;
    const original = button.innerHTML;

    try {
        await navigator.clipboard.writeText(value.trim());

        button.textContent = 'Copied!';
        setTimeout(() => {
            button.innerHTML = original;
        }, 1800);
    } catch (error) {
        console.error('Copy failed:', error);
        button.textContent = 'Copy Failed';
        setTimeout(() => {
            button.innerHTML = original;
        }, 1800);
    }
}
</script>


<script>
/* RoadWatch Device Info Live Refresh V2 */
document.addEventListener('DOMContentLoaded', function () {
    const endpoint = @json(route('device-status.live'));

    const badge = document.getElementById('rwLiveBadge');
    const connection = document.getElementById('rwLiveConnection');

    if (!badge || !connection) return;

    let loading = false;

    function setText(id, value) {
        const element = document.getElementById(id);
        if (element) element.textContent = value;
    }

    function formatWib(iso) {
        if (!iso) return 'Never';

        const date = new Date(iso);
        if (Number.isNaN(date.getTime())) return 'Not Available';

        const parts = new Intl.DateTimeFormat('en-GB', {
            timeZone: 'Asia/Jakarta',
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hourCycle: 'h23'
        }).formatToParts(date);

        const values = Object.fromEntries(
            parts.filter(part => part.type !== 'literal')
                 .map(part => [part.type, part.value])
        );

        return `${values.year}-${values.month}-${values.day} ${values.hour}:${values.minute}:${values.second} WIB`;
    }

    async function refreshDeviceInfo() {
        if (loading || document.hidden) return;

        loading = true;

        try {
            const response = await fetch(endpoint, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
                cache: 'no-store'
            });

            if (!response.ok) throw new Error('HTTP ' + response.status);

            const device = await response.json();
            const online = device.online === true;
            const status = online ? 'Online' : 'Offline';

            badge.classList.toggle('rw-online', online);
            badge.classList.toggle('rw-offline', !online);

            const badgeText = badge.childNodes;
            for (const node of badgeText) {
                if (node.nodeType === Node.TEXT_NODE && node.textContent.trim()) {
                    node.textContent = ' ' + status;
                }
            }

            connection.textContent = status;
            connection.classList.toggle('text-emerald-600', online);
            connection.classList.toggle('text-red-500', !online);

            setText('rwLiveHeartbeat', formatWib(device.last_seen_at));

            setText(
                'rwLiveGps',
                device.last_reported_gps_status ?? 'Unknown'
            );

            setText(
                'rwLiveLatitude',
                device.latitude ?? 'Not Available'
            );

            setText(
                'rwLiveLongitude',
                device.longitude ?? 'Not Available'
            );

            setText(
                'rwLiveFirmware',
                device.firmware_version ?? 'Not Reported'
            );

        } catch (error) {
            console.error('Device Info Live Refresh:', error);
        } finally {
            loading = false;
        }
    }

    refreshDeviceInfo();
    setInterval(refreshDeviceInfo, 10000);

    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) refreshDeviceInfo();
    });
});
</script>

</x-app-layout>
