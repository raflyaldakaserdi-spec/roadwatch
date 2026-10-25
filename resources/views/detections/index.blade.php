<x-app-layout>
    <!-- Header Page -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Data Deteksi Jalan Berlubang</h1>
            <p class="text-sm text-slate-500 mt-1 font-medium">Daftar rekaman deteksi jalan berlubang secara real-time</p>
        </div>
    </div>


    <!-- RoadWatch Detection Analytics Cards V2 -->
    <section id="rwDetectionStats"
             class="grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4 mb-6"
             aria-label="Ringkasan statistik deteksi">

        <div class="rw-stat-card rw-stat-pink">
            <div class="rw-stat-top">
                <span>Total Deteksi</span>
                <i class="fa-solid fa-chart-column" aria-hidden="true"></i>
            </div>
            <div class="rw-stat-value" data-rw-stat="total">{{ number_format($stats['total']) }}</div>
            <div class="rw-stat-desc">Seluruh rekaman deteksi</div>
        </div>

        <div class="rw-stat-card rw-stat-red">
            <div class="rw-stat-top">
                <span>High Risk</span>
                <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
            </div>
            <div class="rw-stat-value" data-rw-stat="high">{{ number_format($stats['high']) }}</div>
            <div class="rw-stat-desc">Prioritas penanganan</div>
        </div>

        <div class="rw-stat-card rw-stat-blue">
            <div class="rw-stat-top">
                <span>In Progress</span>
                <i class="fa-solid fa-clock" aria-hidden="true"></i>
            </div>
            <div class="rw-stat-value" data-rw-stat="progress">{{ number_format($stats['progress']) }}</div>
            <div class="rw-stat-desc">Sedang ditangani</div>
        </div>

        <div class="rw-stat-card rw-stat-green">
            <div class="rw-stat-top">
                <span>Repaired</span>
                <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
            </div>
            <div class="rw-stat-value" data-rw-stat="repaired">{{ number_format($stats['repaired']) }}</div>
            <div class="rw-stat-desc">Penanganan selesai</div>
        </div>
    </section>

    <!-- Filter & Search Card -->
    <div class="bg-white rounded-2xl p-5 border border-purple-100/60 shadow-sm mb-6">
        <form method="GET" action="{{ route('detections.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Cari Lokasi / Kode ID</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari jalan atau ID..." 
                           class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-violet-500 focus:bg-white transition">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
                </div>
            </div>

            <!-- Filter Status -->
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Filter Status</label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-violet-500 focus:bg-white transition">
                    <option value="">Semua Status</option>
                    <option value="Detected" {{ request('status') == 'Detected' ? 'selected' : '' }}>Detected</option>
                    <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="Repaired" {{ request('status') == 'Repaired' ? 'selected' : '' }}>Repaired (Selesai)</option>
                </select>
            </div>

            <!-- Filter Severity -->
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1.5">Tingkat Bahaya</label>
                <select name="severity" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-violet-500 focus:bg-white transition">
                    <option value="">Semua Tingkat</option>
                    <option value="High" {{ request('severity') == 'High' ? 'selected' : '' }}>High</option>
                    <option value="Medium" {{ request('severity') == 'Medium' ? 'selected' : '' }}>Medium</option>
                    <option value="Low" {{ request('severity') == 'Low' ? 'selected' : '' }}>Low</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end gap-2">
                <button style="background:linear-gradient(105deg,#F3A7C9,#E879AA);color:white;border-color:#F0A4C6;box-shadow:0 5px 15px rgba(232,121,170,.12)" type="submit" class="flex-1 py-2 px-4  hover: text-white font-bold text-xs rounded-xl transition shadow-sm">
                    Filter
                </button>
                <a href="{{ route('detections.index') }}" class="py-2 px-4 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs rounded-xl transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div role="status" class="mb-4 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm font-semibold text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div role="alert" class="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm font-semibold text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl border border-purple-100/60 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="rw-detection-table w-full text-left text-xs">
                <thead class="bg-slate-50/80 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="p-4">No</th>
                        <th class="p-4">Gambar</th>
                        <th class="p-4">Kode ID</th>
                        <th class="p-4">Pengunggah / Sumber</th>
                        <th class="p-4">Lokasi Jalan</th>
                        <th class="p-4">Koordinat</th>
                        <th class="p-4">Tanggal & Hari</th>
                        <th class="p-4">Waktu</th>
                        <th class="p-4">Tingkat Bahaya</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="roadwatchDetectionTable" class="divide-y divide-slate-100 text-slate-700 font-medium">
                    @forelse($detections as $index => $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-4 font-bold text-slate-400">{{ $detections->firstItem() + $index }}</td>
                            <td class="p-4">
                                <img src="{{ $item->image }}" alt="Pothole" class="w-12 h-12 object-cover rounded-xl border border-slate-200 shadow-sm">
                            </td>
                            <td class="p-4 font-bold font-mono-tech text-violet-600">{{ $item->detection_code }}</td>
                            
                            <!-- Pengunggah / Sumber Data -->
                            <td class="p-4">
                                @if($item->user)
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold 
                                            {{ $item->user->role == 'admin' ? 'bg-purple-100 text-purple-700' : '' }}
                                            {{ $item->user->role == 'pekerja' ? 'bg-indigo-100 text-indigo-700' : '' }}
                                            {{ $item->user->role == 'masyarakat' ? 'bg-emerald-100 text-emerald-700' : '' }}">
                                            <i class="fa-solid 
                                                {{ $item->user->role == 'admin' ? 'fa-user-shield' : '' }}
                                                {{ $item->user->role == 'pekerja' ? 'fa-helmet-safety' : '' }}
                                                {{ $item->user->role == 'masyarakat' ? 'fa-users' : '' }} mr-1"></i>
                                            {{ ucfirst($item->user->role) }}
                                        </span>
                                        <span class="text-xs font-semibold text-slate-700 truncate max-w-[120px]">{{ $item->user->name }}</span>
                                    </div>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200">
                                        <i class="fa-solid fa-microchip mr-1"></i> Sensor ESP32
                                    </span>
                                @endif
                            </td>

                            <td class="p-4 font-semibold text-slate-800 max-w-[180px] truncate" title="{{ $item->location }}">{{ $item->location }}</td>
                            <td class="p-4 font-mono-tech text-[11px] text-slate-500 leading-snug">
                                {{ $item->latitude }}<br>{{ $item->longitude }}
                            </td>
                            <td class="p-4">
                                <span class="font-bold text-slate-800">{{ $item->detection_date }}</span>
                                <p class="text-[10px] text-slate-400 font-medium">{{ $item->detection_day }}</p>
                            </td>
                            <td class="p-4 font-mono-tech text-slate-600">{{ $item->detection_time }}</td>
                            
                            <!-- Tingkat Bahaya Badge -->
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold tracking-wide
                                    {{ $item->severity == 'High' ? 'bg-[#FFE2E2] text-[#9B1C1C]' : '' }}
                                    {{ $item->severity == 'Medium' ? 'bg-[#FEF3C7] text-[#92400E]' : '' }}
                                    {{ $item->severity == 'Low' ? 'bg-[#E0F2FE] text-[#0369A1]' : '' }}">
                                    {{ $item->severity }}
                                </span>
                            </td>

                            <!-- Status: Admin Editable -->
                            <td class="p-4">
                                @if(auth()->user()->role === 'admin')
                                    <form method="POST"
                                          action="{{ route('detections.update-status', $item->id) }}"
                                          class="rw-admin-status-form flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')

                                        <select name="status"
                                                aria-label="Status {{ $item->detection_code }}"
                                                class="rw-status-select text-xs font-bold rounded-xl border px-3 py-2 focus:ring-2"
                                                data-current-status="{{ $item->status }}"
                                                onchange="rwUpdateStatusColor(this); this.form.requestSubmit();">
                                            <option value="Detected" {{ $item->status === 'Detected' ? 'selected' : '' }}>Detected</option>
                                            <option value="In Progress" {{ $item->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="Repaired" {{ $item->status === 'Repaired' ? 'selected' : '' }}>Selesai</option>
                                        </select>

                                        <button type="submit"
                                                class="px-3 py-2 rounded-xl text-white text-xs font-bold bg-pink-500 hover:bg-pink-600">
                                            Simpan
                                        </button>
                                    </form>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold tracking-wide
                                        {{ $item->status == 'Detected' ? 'bg-[#FEF3C7] text-[#92400E]' : '' }}
                                        {{ $item->status == 'In Progress' ? 'bg-[#E0F2FE] text-[#0369A1]' : '' }}
                                        {{ $item->status == 'Repaired' ? 'bg-[#D1FAE5] text-[#065F46]' : '' }}">
                                        {{ $item->status == 'Repaired' ? 'Selesai' : $item->status }}
                                    </span>
                                @endif
                            </td>

                            <td class="p-4 text-center">
                                <a href="{{ route('detections.show', $item->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-violet-50 text-violet-600 hover:bg-violet-600 hover:text-white rounded-xl font-bold transition" title="Lihat Detail">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="p-8 text-center text-slate-400 font-medium">
                                <i class="fa-solid fa-folder-open text-3xl mb-2 text-slate-300"></i>
                                <p>Tidak ada data deteksi yang ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($detections->hasPages())
            <div id="roadwatchPagination" class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $detections->links() }}
            </div>
        @endif
    </div>

<script>
function rwUpdateStatusColor(select) {
    const colors = {
        'Detected': {
            background: '#FEF3C7',
            color: '#92400E',
            border: '#FCD34D'
        },
        'In Progress': {
            background: '#DBEAFE',
            color: '#1D4ED8',
            border: '#93C5FD'
        },
        'Repaired': {
            background: '#D1FAE5',
            color: '#065F46',
            border: '#6EE7B7'
        }
    };

    const selected = colors[select.value] || colors['Detected'];

    select.style.backgroundColor = selected.background;
    select.style.color = selected.color;
    select.style.borderColor = selected.border;
}

document.querySelectorAll('.rw-status-select').forEach(select => {
    rwUpdateStatusColor(select);
});
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('roadwatchDetectionTable');

    if (!table) return;

    let loading = false;
    let editingStatus = false;

    document.addEventListener('focusin', function (event) {
        if (event.target.closest('.rw-admin-status-form')) {
            editingStatus = true;
        }
    });

    document.addEventListener('focusout', function (event) {
        if (event.target.closest('.rw-admin-status-form')) {
            setTimeout(function () {
                editingStatus = Boolean(
                    document.activeElement?.closest('.rw-admin-status-form')
                );
            }, 100);
        }
    });

    async function refreshDetectionTable() {
        if (loading || editingStatus || document.hidden) return;

        loading = true;

        try {
            const url = new URL(
                @json(route('detections.live-table')),
                window.location.origin
            );

            url.search = window.location.search;

            const response = await fetch(url.toString(), {
                headers: { Accept: 'text/html' },
                credentials: 'same-origin',
                cache: 'no-store'
            });

            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }

            const html = await response.text();
            const documentData = new DOMParser().parseFromString(
                html,
                'text/html'
            );

            // Refresh RoadWatch Analytics Cards V2
            const currentStats = document.getElementById('rwDetectionStats');
            const incomingStats = documentData.getElementById('rwDetectionStats');

            if (currentStats && incomingStats) {
                currentStats.querySelectorAll('[data-rw-stat]').forEach(function (el) {
                    const key = el.getAttribute('data-rw-stat');
                    const updated = incomingStats.querySelector(
                        '[data-rw-stat="' + key + '"]'
                    );

                    if (updated && el.textContent !== updated.textContent) {
                        el.textContent = updated.textContent;
                    }
                });
            }

            const newTable = documentData.getElementById(
                'roadwatchDetectionTable'
            );

            if (!newTable) {
                throw new Error('Tabel tidak ditemukan');
            }

            if (editingStatus || document.hidden) return;

            if (table.innerHTML !== newTable.innerHTML) {
                table.innerHTML = newTable.innerHTML;

                if (typeof rwUpdateStatusColor === 'function') {
                    table.querySelectorAll('.rw-status-select')
                        .forEach(rwUpdateStatusColor);
                }
            }

            const currentPagination =
                document.getElementById('roadwatchPagination');

            const newPagination =
                documentData.getElementById('roadwatchPagination');

            if (currentPagination && newPagination) {
                if (currentPagination.innerHTML !== newPagination.innerHTML) {
                    currentPagination.innerHTML = newPagination.innerHTML;
                }
            } else if (!currentPagination && newPagination) {
                const tableCard = table.closest('.bg-white.rounded-2xl');

                if (tableCard) {
                    tableCard.appendChild(newPagination.cloneNode(true));
                }
            } else if (currentPagination && !newPagination) {
                currentPagination.remove();
            }
        } catch (error) {
            console.error('Detection Auto Update:', error);
        } finally {
            loading = false;
        }
    }

    setInterval(refreshDetectionTable, 10000);

    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) refreshDetectionTable();
    });
});
</script>


<style>
/* RoadWatch Premium Detection Table V1 */

.rw-detection-table {
    border-collapse: separate;
    border-spacing: 0;
}

.rw-detection-table thead th {
    background: #fff6fa;
    color: #a56888;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: .07em;
    white-space: nowrap;
    border-bottom: 1px solid #f4dce9;
}

.rw-detection-table tbody tr {
    transition: background-color .2s ease;
}

.rw-detection-table tbody tr:hover {
    background: #fff7fb;
}

.rw-detection-table tbody td {
    padding-top: 18px;
    padding-bottom: 18px;
    border-bottom: 1px solid #f8edf3;
    vertical-align: middle;
}

.rw-detection-table tbody img {
    width: 68px;
    height: 58px;
    border-radius: 14px;
    object-fit: cover;
    border: 2px solid #fff;
    box-shadow: 0 4px 14px rgba(211, 111, 158, .14);
    transition: transform .2s ease;
}

.rw-detection-table tbody tr:hover img {
    transform: scale(1.04);
}

.rw-detection-table tbody a[title="Lihat Detail"] {
    background: #fff0f7;
    color: #d74c91;
    border: 1px solid #f6d5e5;
    padding: 9px 13px;
    white-space: nowrap;
}

.rw-detection-table tbody a[title="Lihat Detail"]:hover {
    background: #e879aa;
    border-color: #e879aa;
    color: #fff;
}

@media (prefers-reduced-motion: reduce) {
    .rw-detection-table tbody tr,
    .rw-detection-table tbody img {
        transition: none;
    }
}
</style>


<style>
/* RoadWatch Detection Analytics Cards V2 */

.rw-stat-card {
    min-width: 0;
    padding: clamp(15px, 1.8vw, 23px);
    border-radius: 20px;
    background: #fff;
    border: 1px solid #f3e5ee;
    box-shadow: 0 8px 24px rgba(158, 92, 134, .045);
    transition: transform .2s ease, box-shadow .2s ease;
}

.rw-stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 14px 32px rgba(158, 92, 134, .1);
}

.rw-stat-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    font-weight: 800;
    color: #6d7287;
}

.rw-stat-top i {
    display: grid;
    place-items: center;
    width: 35px;
    height: 35px;
    border-radius: 12px;
    font-size: 15px;
    flex-shrink: 0;
}

.rw-stat-value {
    margin-top: 14px;
    font-size: clamp(25px, 2.6vw, 35px);
    line-height: 1.1;
    font-weight: 900;
    letter-spacing: -1px;
    color: #252b43;
    font-variant-numeric: tabular-nums;
}

.rw-stat-desc {
    margin-top: 8px;
    font-size: 11px;
    font-weight: 500;
    color: #9795a6;
}

.rw-stat-pink .rw-stat-top i {
    background: #fff0f7;
    color: #dc639f;
}

.rw-stat-red .rw-stat-top i {
    background: #fff0f1;
    color: #df6a76;
}

.rw-stat-blue .rw-stat-top i {
    background: #edf4ff;
    color: #648fdb;
}

.rw-stat-green .rw-stat-top i {
    background: #eaf9f3;
    color: #39a581;
}

@media (max-width: 480px) {
    .rw-stat-card {
        border-radius: 16px;
        padding: 14px;
    }

    .rw-stat-top {
        font-size: 10px;
    }

    .rw-stat-top i {
        width: 29px;
        height: 29px;
        font-size: 13px;
    }

    .rw-stat-desc {
        font-size: 10px;
    }
}

@media (prefers-reduced-motion: reduce) {
    .rw-stat-card {
        transition: none;
    }
}
</style>

</x-app-layout>