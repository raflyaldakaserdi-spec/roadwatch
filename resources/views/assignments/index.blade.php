<x-app-layout>
@php
    $totalTasks = \App\Models\WorkAssignment::count();
    $waitingTasks = \App\Models\WorkAssignment::where('status', 'Assigned')->count();
    $activeTasks = \App\Models\WorkAssignment::where('status', 'In Progress')->count();
    $completedTasks = \App\Models\WorkAssignment::where('status', 'Completed')->count();
@endphp

<div class="space-y-6">

    {{-- ROADWATCH_ASSIGNMENT_CENTER_V1 --}}

    <div class="bg-white border border-rose-100 rounded-3xl p-6 md:p-8 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-extrabold tracking-widest uppercase text-pink-500">
                    ROADWATCH / FIELD OPERATIONS
                </p>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 mt-2">
                    Assignment Center
                </h1>
                <p class="text-sm text-slate-500 mt-2">
                    Kelola penugasan dan monitoring penanganan kerusakan jalan.
                </p>
            </div>

            <div class="w-14 h-14 rounded-2xl bg-pink-50 text-pink-500 flex items-center justify-center">
                <i class="fa-solid fa-clipboard-list text-2xl"></i>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl px-5 py-4 text-sm font-semibold">
            <i class="fa-solid fa-circle-check mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-2xl px-5 py-4 text-sm">
            <p class="font-extrabold mb-2">Penugasan belum dapat disimpan:</p>
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach([
            ['Total Tugas', $totalTasks, 'fa-clipboard-list', 'text-pink-600', 'bg-pink-50'],
            ['Menunggu', $waitingTasks, 'fa-clock', 'text-amber-600', 'bg-amber-50'],
            ['Dikerjakan', $activeTasks, 'fa-screwdriver-wrench', 'text-blue-600', 'bg-blue-50'],
            ['Selesai', $completedTasks, 'fa-circle-check', 'text-emerald-600', 'bg-emerald-50'],
        ] as [$label, $value, $icon, $color, $background])
            <div class="bg-white rounded-2xl border border-rose-100 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold text-slate-500">{{ $label }}</p>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $background }} {{ $color }}">
                        <i class="fa-solid {{ $icon }}"></i>
                    </div>
                </div>
                <p class="text-3xl font-extrabold text-slate-900 mt-3">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-5 items-start">

        <div class="xl:col-span-5 bg-white border border-rose-100 rounded-2xl shadow-sm p-6">
            <div class="mb-5">
                <h2 class="text-lg font-extrabold text-slate-900">
                    <i class="fa-solid fa-plus-circle text-pink-500 mr-2"></i>
                    Buat Penugasan Baru
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Tentukan lokasi, pekerja, prioritas, dan instruksi.
                </p>
            </div>

            <form method="POST" action="{{ route('assignments.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="detection_id" class="block text-xs font-bold text-slate-700 mb-2">
                        Deteksi Kerusakan
                    </label>
                    <select id="detection_id" name="detection_id" required
                            class="w-full rounded-xl border-slate-200 text-sm focus:border-pink-400 focus:ring-pink-200">
                        <option value="">Pilih lokasi kerusakan</option>
                        @foreach($detections as $detection)
                            <option value="{{ $detection->id }}" @selected(old('detection_id') == $detection->id)>
                                {{ $detection->detection_code }} — {{ $detection->location }}
                                ({{ $detection->severity }})
                            </option>
                        @endforeach
                    </select>
                    @if($detections->isEmpty())
                        <p class="text-xs text-amber-600 mt-2">
                            Tidak ada deteksi yang tersedia untuk penugasan baru.
                        </p>
                    @endif
                </div>

                <div>
                    <label for="worker_id" class="block text-xs font-bold text-slate-700 mb-2">
                        Pekerja
                    </label>
                    <select id="worker_id" name="worker_id" required
                            class="w-full rounded-xl border-slate-200 text-sm focus:border-pink-400 focus:ring-pink-200">
                        <option value="">Pilih pekerja</option>
                        @foreach($workers as $worker)
                            <option value="{{ $worker->id }}" @selected(old('worker_id') == $worker->id)>
                                {{ $worker->name }} — {{ $worker->email }}
                            </option>
                        @endforeach
                    </select>
                    @if($workers->isEmpty())
                        <p class="text-xs text-amber-600 mt-2">
                            Belum ada akun Pekerja aktif yang tersedia.
                        </p>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="priority" class="block text-xs font-bold text-slate-700 mb-2">
                            Prioritas
                        </label>
                        <select id="priority" name="priority" required
                                class="w-full rounded-xl border-slate-200 text-sm focus:border-pink-400 focus:ring-pink-200">
                            @foreach(['Low', 'Medium', 'High'] as $priority)
                                <option value="{{ $priority }}" @selected(old('priority', 'Medium') === $priority)>
                                    {{ $priority }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="due_date" class="block text-xs font-bold text-slate-700 mb-2">
                            Tenggat Waktu
                        </label>
                        <input type="date" id="due_date" name="due_date"
                               value="{{ old('due_date') }}"
                               min="{{ now()->toDateString() }}"
                               class="w-full rounded-xl border-slate-200 text-sm focus:border-pink-400 focus:ring-pink-200">
                    </div>
                </div>

                <div>
                    <label for="instructions" class="block text-xs font-bold text-slate-700 mb-2">
                        Instruksi Pekerjaan
                    </label>
                    <textarea id="instructions" name="instructions" rows="4" maxlength="3000"
                              placeholder="Contoh: Periksa kondisi lubang, lakukan penanganan, dan dokumentasikan hasil pekerjaan."
                              class="w-full rounded-xl border-slate-200 text-sm focus:border-pink-400 focus:ring-pink-200">{{ old('instructions') }}</textarea>
                </div>

                <button type="submit"
                        @disabled($detections->isEmpty() || $workers->isEmpty())
                        class="w-full rounded-xl bg-pink-500 hover:bg-pink-600 text-white font-extrabold text-sm px-5 py-3 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fa-solid fa-paper-plane mr-2"></i>
                    Tetapkan Pekerja
                </button>
            </form>
        </div>

        <div class="xl:col-span-7 bg-white border border-rose-100 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-rose-100">
                <h2 class="text-lg font-extrabold text-slate-900">
                    <i class="fa-solid fa-list-check text-pink-500 mr-2"></i>
                    Daftar Penugasan
                </h2>
                <p class="text-xs text-slate-500 mt-1">
                    Seluruh pekerjaan yang telah ditugaskan Admin.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-rose-50/60 text-slate-500 text-xs">
                        <tr>
                            <th class="px-4 py-3">Deteksi / Lokasi</th>
                            <th class="px-4 py-3">Pekerja</th>
                            <th class="px-4 py-3">Prioritas</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Tenggat</th>
                            <th class="px-4 py-3">Review Pekerjaan</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse($assignments as $assignment)
                            <tr class="hover:bg-rose-50/30 transition">
                                <td class="px-4 py-4">
                                    <a href="{{ route('detections.show', $assignment->detection_id) }}"
                                       class="font-bold text-pink-600 hover:underline">
                                        {{ $assignment->detection?->detection_code ?? '—' }}
                                    </a>
                                    <p class="text-xs text-slate-500 mt-1 max-w-[190px] truncate">
                                        {{ $assignment->detection?->location ?? 'Lokasi tidak tersedia' }}
                                    </p>
                                </td>

                                <td class="px-4 py-4">
                                    <span class="font-semibold text-slate-700">
                                        {{ $assignment->worker?->name ?? 'Tidak tersedia' }}
                                    </span>
                                </td>

                                <td class="px-4 py-4">
                                    @php
                                        $priorityColor = match($assignment->priority) {
                                            'High' => 'bg-red-50 text-red-700',
                                            'Medium' => 'bg-amber-50 text-amber-700',
                                            default => 'bg-blue-50 text-blue-700',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $priorityColor }}">
                                        {{ $assignment->priority }}
                                    </span>
                                </td>

                                <td class="px-4 py-4">
                                    @php
                                        $statusColor = match($assignment->status) {
                                            'Completed' => 'bg-emerald-50 text-emerald-700',
                                            'In Progress' => 'bg-blue-50 text-blue-700',
                                            'Pending Review' => 'bg-purple-50 text-purple-700',
                                            default => 'bg-amber-50 text-amber-700',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold whitespace-nowrap {{ $statusColor }}">
                                        {{ $assignment->status }}
                                    </span>
                                </td>

                                <td class="px-4 py-4 text-xs text-slate-600 whitespace-nowrap">
                                    {{ $assignment->due_date?->format('d M Y') ?? '—' }}
                                </td>

                                {{-- ROADWATCH_ADMIN_REVIEW_V6 --}}
                                <td class="px-4 py-4 align-top">
                                    @if($assignment->status === 'Pending Review')
                                        <div class="min-w-[220px] space-y-3">
                                            @if($assignment->completion_photo)
                                                <a href="{{ asset('storage/' . $assignment->completion_photo) }}"
                                                   target="_blank"
                                                   rel="noopener noreferrer">
                                                    <img src="{{ asset('storage/' . $assignment->completion_photo) }}"
                                                         alt="Foto hasil pekerjaan"
                                                         loading="lazy"
                                                         class="w-32 h-24 object-cover rounded-xl border border-rose-100">
                                                </a>
                                            @endif

                                            <div class="text-xs text-slate-600 whitespace-normal break-words">
                                                <span class="font-bold text-slate-800">Catatan:</span>
                                                {{ $assignment->worker_notes ?: 'Tidak ada catatan.' }}
                                            </div>

                                            <div class="flex flex-wrap gap-2">
                                                <form method="POST"
                                                      action="{{ route('assignments.approve', $assignment->id) }}"
                                                      onsubmit="return confirm('Setujui pekerjaan ini? Status deteksi akan menjadi Repaired.')">
                                                    @csrf
                                                    <button type="submit"
                                                            class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold">
                                                        <i class="fa-solid fa-check mr-1"></i>
                                                        Setujui
                                                    </button>
                                                </form>

                                                <form method="POST"
                                                      action="{{ route('assignments.reject', $assignment->id) }}"
                                                      onsubmit="return confirm('Minta pekerja melakukan revisi pekerjaan ini?')">
                                                    @csrf
                                                    <button type="submit"
                                                            class="px-3 py-2 bg-amber-100 hover:bg-amber-200 text-amber-800 rounded-lg text-xs font-bold">
                                                        <i class="fa-solid fa-rotate-left mr-1"></i>
                                                        Minta Revisi
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                    @elseif($assignment->status === 'Completed')
                                        <div class="text-xs font-bold text-emerald-700">
                                            <i class="fa-solid fa-circle-check mr-1"></i>
                                            Terverifikasi
                                        </div>

                                    @else
                                        <span class="text-xs text-slate-400">
                                            Belum ada bukti untuk direview.
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center px-6 py-14">
                                    <i class="fa-solid fa-clipboard-list text-3xl text-pink-200"></i>
                                    <p class="text-sm font-bold text-slate-700 mt-3">
                                        Belum Ada Penugasan
                                    </p>
                                    <p class="text-xs text-slate-400 mt-1">
                                        Buat penugasan pertama melalui formulir di samping.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($assignments->hasPages())
                <div class="px-5 py-4 border-t border-slate-100">
                    {{ $assignments->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
</x-app-layout>
