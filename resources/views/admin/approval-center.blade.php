<x-app-layout>
    <div class="w-full max-w-none ml-0 mr-0 px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-extrabold tracking-[0.2em] text-pink-400 uppercase">
                    RoadWatch Administration
                </p>
                <h1 class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-800">
                    Approval Center
                </h1>
                <p class="mt-2 text-sm text-slate-500">
                    Kelola pengajuan hak akses pengguna RoadWatch.
                </p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-pink-100 text-pink-600 flex items-center justify-center">
                <i class="fa-solid fa-shield-halved text-xl"></i>
            </div>
        </div>

        @if(session('role_approval_success'))
            <div class="rounded-2xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-sm text-emerald-700">
                {{ session('role_approval_success') }}
            </div>
        @endif

        <div id="rw-approval-stats" class="grid grid-cols-3 gap-3 sm:gap-4">
            @foreach([
                ['Pending', $stats['pending'], 'fa-clock', 'amber'],
                ['Approved', $stats['approved'], 'fa-circle-check', 'emerald'],
                ['Rejected', $stats['rejected'], 'fa-circle-xmark', 'rose']
            ] as [$label, $count, $icon, $tone])
                <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-3 sm:p-5 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold text-slate-500">{{ $label }}</p>
                        <i class="fa-solid {{ $icon }} text-pink-400"></i>
                    </div>
                    <p class="mt-3 text-2xl sm:text-3xl font-extrabold text-slate-800">
                        {{ $count }}
                    </p>
                </div>
            @endforeach
        </div>

        <div id="rw-approval-table" class="bg-white border border-pink-100 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 sm:px-6 py-5 border-b border-pink-50">
                <h2 class="text-base font-extrabold text-slate-800">
                    Daftar Pengajuan
                </h2>
                <p class="mt-1 text-xs text-slate-500">
                    Permintaan pending ditampilkan terlebih dahulu.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left border-collapse">
                    <thead class="bg-pink-50/60">
                        <tr class="text-[11px] uppercase tracking-wider text-slate-500">
                            <th class="px-5 py-4 align-middle">Pengguna</th>
                            <th class="px-5 py-4 align-middle">Role</th>
                            <th class="w-[28%] px-5 py-4 text-left align-middle">Alasan</th>
                            <th class="px-5 py-4 align-middle">Status</th>
                            <th class="px-5 py-4 align-middle">Tanggal</th>
                            <th class="px-5 py-4 align-middle">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-pink-50">
                        @forelse($roleRequests as $item)
                            <tr class="align-top hover:bg-pink-50/30">
                                <td class="px-5 py-4 align-middle">
                                    <p class="text-sm font-bold text-slate-800">
                                        {{ $item->user?->name ?? 'Pengguna dihapus' }}
                                    </p>
                                    <p class="text-xs text-slate-500 break-all">
                                        {{ $item->user?->email ?? '-' }}
                                    </p>
                                    @if($item->organization)
                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $item->organization }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 align-middle">
                                    <span class="text-xs font-bold text-pink-700 bg-pink-50 px-3 py-1 rounded-full">
                                        {{ ucfirst($item->requested_role) }}
                                    </span>
                                </td>
                                <td class="w-[28%] px-5 py-4 align-middle text-left">
                                    <div class="w-full min-w-0">
                                        <p class="text-xs leading-5 text-slate-600 whitespace-normal break-words text-left">
                                            {{ $item->reason ?? '-' }}
                                        </p>
                                    </div>
                                    @if($item->review_note)
                                        <p class="mt-2 text-xs leading-5 text-slate-500 whitespace-normal break-words text-left">
                                            Catatan: {{ $item->review_note }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 align-middle">
                                    <span class="text-xs font-bold px-3 py-1 rounded-full
                                        {{ $item->status === 'pending'
                                            ? 'bg-amber-50 text-amber-700'
                                            : ($item->status === 'approved'
                                                ? 'bg-emerald-50 text-emerald-700'
                                                : 'bg-rose-50 text-rose-700') }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-xs text-slate-500 whitespace-nowrap">
                                    {{ $item->created_at?->format('d M Y H:i') ?? '-' }}
                                </td>
                                <td class="px-5 py-4 align-middle">
                                    @if(
                                        $item->status === 'pending'
                                        && $item->user
                                        && $item->user->id !== auth()->id()
                                        && $item->user->role === 'masyarakat'
                                        && (
                                            ($item->requested_role === 'pekerja' && auth()->user()->can('approveWorker'))
                                            || ($item->requested_role === 'admin' && auth()->user()->can('approveAdmin'))
                                        )
                                    )
                                        <div class="space-y-2 min-w-[170px]">
                                            <form method="POST" action="{{ route('role-requests.approve', $item) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    onclick="return confirm('Setujui pengajuan ini?')"
                                                    class="w-full rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-3 py-2">
                                                    <i class="fa-solid fa-check mr-1"></i> Approve
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('role-requests.reject', $item) }}" class="space-y-2">
                                                @csrf
                                                @method('PATCH')
                                                <input type="text" name="review_note"
                                                    maxlength="2000"
                                                    placeholder="Alasan penolakan (opsional)"
                                                    class="w-full rounded-xl border-slate-200 text-xs">
                                                <button type="submit"
                                                    onclick="return confirm('Tolak pengajuan ini?')"
                                                    class="w-full rounded-xl border border-rose-200 text-rose-700 hover:bg-rose-50 text-xs font-bold px-3 py-2">
                                                    <i class="fa-solid fa-xmark mr-1"></i> Reject
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-400">
                                            {{ $item->status === 'pending' ? 'Tidak memiliki izin' : 'Selesai' }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <i class="fa-solid fa-inbox text-3xl text-pink-200"></i>
                                    <p class="mt-3 text-sm font-bold text-slate-700">
                                        Belum ada pengajuan akses
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        Pengajuan dari pengguna akan muncul di sini.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($roleRequests->hasPages())
                <div class="px-6 py-4 border-t border-pink-100">
                    {{ $roleRequests->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- roadwatch-smart-refresh-v2 -->
    <script>
    (() => {
        const endpoint = @json(route('approval-center.live-data'));
        let currentVersion = null;
        let busy = false;

        async function checkUpdates() {
            if (document.hidden || busy) return;

            busy = true;

            try {
                const response = await fetch(endpoint + window.location.search, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    cache: 'no-store'
                });

                if (!response.ok) return;

                const data = await response.json();

                if (currentVersion === data.version) return;

                const parser = new DOMParser();
                const doc = parser.parseFromString(data.html, 'text/html');

                const newStats = doc.querySelector('#rw-approval-stats');
                const newTable = doc.querySelector('#rw-approval-table');
                const oldStats = document.querySelector('#rw-approval-stats');
                const oldTable = document.querySelector('#rw-approval-table');

                if (!newStats || !newTable || !oldStats || !oldTable) return;

                // Jangan menghapus teks yang sedang diketik.
                const active = document.activeElement;
                if (active && oldTable.contains(active) &&
                    active.matches('input, textarea, select')) {
                    return;
                }

                oldStats.replaceWith(newStats);
                oldTable.replaceWith(newTable);

                currentVersion = data.version;
            } catch (error) {
                console.warn('RoadWatch approval refresh:', error);
            } finally {
                busy = false;
            }
        }

        checkUpdates();
        setInterval(checkUpdates, 10000);
    })();
    </script>

</x-app-layout>
