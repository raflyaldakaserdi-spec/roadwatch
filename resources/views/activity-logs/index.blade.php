
<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">
                    Log History
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Riwayat aktivitas sistem RoadWatch
                </p>
            </div>
            <div class="flex items-center gap-2 text-xs font-semibold text-emerald-600">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Auto-refresh 5 detik
            </div>
        </div>

        {{-- RoadWatch Activity Overview Premium V2 --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ([
                ['label' => 'Total Activities', 'value' => $activityStats['total'], 'icon' => 'fa-list-check', 'description' => 'Semua aktivitas', 'color' => 'text-rose-600', 'bg' => 'bg-rose-50'],
                ['label' => 'Created Events', 'value' => $activityStats['created'], 'icon' => 'fa-circle-plus', 'description' => 'Data baru', 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50'],
                ['label' => 'Status Changes', 'value' => $activityStats['status_changes'], 'icon' => 'fa-arrows-rotate', 'description' => 'Perubahan status', 'color' => 'text-blue-600', 'bg' => 'bg-blue-50'],
                ['label' => 'API Events', 'value' => $activityStats['api_events'], 'icon' => 'fa-microchip', 'description' => 'Aktivitas ESP32 API', 'color' => 'text-violet-600', 'bg' => 'bg-violet-50'],
            ] as $stat)
                <div class="bg-white border border-rose-100/70 rounded-2xl shadow-sm p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-500">{{ $stat['label'] }}</p>
                            <p class="mt-3 text-3xl font-extrabold tracking-tight text-slate-900 tabular-nums">
                                {{ number_format($stat['value']) }}
                            </p>
                            <p class="mt-1 text-[11px] text-slate-400">{{ $stat['description'] }}</p>
                        </div>
                        <div class="h-11 w-11 shrink-0 rounded-xl flex items-center justify-center {{ $stat['bg'] }}">
                            <i class="fa-solid {{ $stat['icon'] }} {{ $stat['color'] }}"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <form method="GET" action="{{ route('activity-logs.index') }}"
              class="bg-white rounded-2xl border border-slate-100 p-5 flex flex-wrap gap-3">

            <input type="text" name="search"
                   value="{{ request('search') }}"
                   placeholder="Cari aktivitas..."
                   class="flex-1 min-w-48 rounded-xl border-slate-200 text-sm">

            <select name="action" class="rounded-xl border-slate-200 text-sm">
                <option value="">Semua Aktivitas</option>
                @foreach (['created', 'updated', 'status_updated', 'deleted', 'login', 'logout'] as $action)
                    <option value="{{ $action }}"
                        @selected(request('action') === $action)>
                        {{ ucfirst($action) }}
                    </option>
                @endforeach
            </select>

            {{-- RoadWatch Premium Activity Filters V3 --}}
            <select name="source"
                    aria-label="Filter sumber aktivitas"
                    class="rounded-xl border-slate-200 text-sm">
                <option value="">Semua Sumber</option>
                <option value="web" @selected(request('source') === 'web')>
                    Web
                </option>
                <option value="esp32_api" @selected(request('source') === 'esp32_api')>
                    ESP32 API
                </option>
                <option value="console" @selected(request('source') === 'console')>
                    Console
                </option>
            </select>

            <div class="flex items-center gap-2">
                <label for="activityDateFrom" class="text-xs font-semibold text-slate-500">
                    Dari
                </label>
                <input id="activityDateFrom"
                       type="date"
                       name="date_from"
                       value="{{ request('date_from') }}"
                       class="rounded-xl border-slate-200 text-sm">
            </div>

            <div class="flex items-center gap-2">
                <label for="activityDateTo" class="text-xs font-semibold text-slate-500">
                    Sampai
                </label>
                <input id="activityDateTo"
                       type="date"
                       name="date_to"
                       value="{{ request('date_to') }}"
                       class="rounded-xl border-slate-200 text-sm">
            </div>

            <button type="submit"
                    class="px-6 py-2 bg-violet-600 text-white rounded-xl text-sm font-bold">
                Filter
            </button>

            <a href="{{ route('activity-logs.index') }}"
               class="px-5 py-2 bg-slate-100 text-slate-600 rounded-xl text-sm font-bold">
                Reset
            </a>
        </form>

        {{-- RoadWatch Premium Activity Table V2 --}}
        <div class="bg-white rounded-2xl border border-rose-100/70 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-rose-50/60 text-slate-500 text-[11px] uppercase tracking-wide">
                        <tr>
                            <th class="px-5 py-4 text-left">Waktu</th>
                            <th class="px-5 py-4 text-left">Aktivitas</th>
                            <th class="px-5 py-4 text-left">Deskripsi</th>
                            <th class="px-5 py-4 text-left">Sumber</th>
                            <th class="px-5 py-4 text-left">Pengguna</th>
                            <th class="px-5 py-4 text-left">Detail</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-rose-50">
                        @forelse ($logs as $log)
                            @php
                                $actionStyles = [
                                    'created' => ['bg-emerald-50 text-emerald-700', 'fa-circle-plus'],
                                    'updated' => ['bg-blue-50 text-blue-700', 'fa-pen'],
                                    'status_updated' => ['bg-indigo-50 text-indigo-700', 'fa-arrows-rotate'],
                                    'deleted' => ['bg-red-50 text-red-700', 'fa-trash-can'],
                                    'login' => ['bg-violet-50 text-violet-700', 'fa-right-to-bracket'],
                                    'logout' => ['bg-slate-100 text-slate-600', 'fa-right-from-bracket'],
                                ];

                                $style = $actionStyles[$log->action] ?? ['bg-slate-100 text-slate-600', 'fa-circle-info'];

                                $sourceStyles = [
                                    'web' => 'bg-sky-50 text-sky-700',
                                    'esp32_api' => 'bg-violet-50 text-violet-700',
                                    'console' => 'bg-amber-50 text-amber-700',
                                ];

                                $sourceStyle = $sourceStyles[$log->source] ?? 'bg-slate-100 text-slate-600';
                                $userName = $log->user?->name ?? 'System';
                            @endphp

                            <tr class="hover:bg-rose-50/40 transition-colors">
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="font-semibold text-slate-700">
                                        {{ $log->created_at->timezone('Asia/Jakarta')->format('d M Y') }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-1">
                                        <i class="fa-regular fa-clock mr-1"></i>
                                        {{ $log->created_at->timezone('Asia/Jakarta')->format('H:i:s') }} WIB
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-[10px] font-extrabold whitespace-nowrap {{ $style[0] }}">
                                        <i class="fa-solid {{ $style[1] }}"></i>
                                        {{ strtoupper(str_replace('_', ' ', $log->action)) }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-slate-700 font-medium min-w-[280px]">
                                    {{ $log->description }}
                                </td>

                                <td class="px-5 py-4">
                                    <span class="inline-flex px-2.5 py-1 rounded-lg text-[10px] font-bold whitespace-nowrap {{ $sourceStyle }}">
                                        {{ strtoupper(str_replace('_', ' ', $log->source)) }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 shrink-0 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center text-xs font-extrabold">
                                            {{ strtoupper(mb_substr($userName, 0, 1)) }}
                                        </div>
                                        <span class="text-xs font-semibold text-slate-600 whitespace-nowrap">
                                            {{ $userName }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    @if ($log->old_values || $log->new_values)
                                        <details class="text-xs">
                                            <summary class="cursor-pointer list-none inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-violet-50 hover:bg-violet-100 text-violet-700 font-bold transition">
                                                <i class="fa-solid fa-eye"></i>
                                                Lihat Detail
                                                <i class="fa-solid fa-chevron-down text-[9px]"></i>
                                            </summary>

                                            <div class="mt-3 space-y-2 min-w-[240px] max-w-sm">
                                                @if ($log->old_values)
                                                    <div class="bg-red-50 border border-red-100 p-3 rounded-xl">
                                                        <p class="font-extrabold text-red-700 mb-2">Sebelum</p>
                                                        <pre class="text-[11px] text-slate-700 whitespace-pre-wrap break-all">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                    </div>
                                                @endif

                                                @if ($log->new_values)
                                                    <div class="bg-emerald-50 border border-emerald-100 p-3 rounded-xl">
                                                        <p class="font-extrabold text-emerald-700 mb-2">Sesudah</p>
                                                        <pre class="text-[11px] text-slate-700 whitespace-pre-wrap break-all">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                    </div>
                                                @endif
                                            </div>
                                        </details>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <i class="fa-solid fa-clock-rotate-left text-3xl text-rose-200 mb-3"></i>
                                    <p class="font-bold text-slate-600">Belum ada aktivitas</p>
                                    <p class="text-xs text-slate-400 mt-1">
                                        Tidak ditemukan aktivitas sesuai filter.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-5 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        </div>
    </div>

    <script>
        let latestLogId = null;

        async function checkActivityLogs() {
            try {
                const response = await fetch(
                    @json(route('activity-logs.latest')),
                    { credentials: 'same-origin', cache: 'no-store' }
                );

                if (!response.ok) return;

                const data = await response.json();

                if (latestLogId !== null && data.latest_id !== latestLogId) {
                    window.location.reload();
                }

                latestLogId = data.latest_id;
            } catch (error) {
                console.error('Log refresh failed:', error);
            }
        }

        checkActivityLogs();
        setInterval(checkActivityLogs, 5000);
    </script>
</x-app-layout>
