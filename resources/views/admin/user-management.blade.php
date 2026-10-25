<x-app-layout>
    <div class="w-full max-w-none ml-0 mr-0 px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-[10px] font-extrabold tracking-[0.2em] text-pink-400 uppercase">
                    RoadWatch Administration
                </p>
                <h1 class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-800">
                    User Management
                </h1>
                <p class="mt-2 text-sm text-slate-500">
                    Monitoring akun pengguna dan hak akses RoadWatch.
                </p>
            </div>

            <a href="{{ route('approval-center.index') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#D870A4] hover:bg-[#C2548D] text-white px-5 py-3 text-xs font-bold transition">
                <i class="fa-solid fa-shield-halved"></i>
                Approval Center
            </a>
        </div>

        @if(session('user_role_success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
                {{ session('user_role_success') }}
            </div>
        @endif


        @can('changeUserRole')
            <div class="flex justify-end">
                <a href="{{ route('user-management.deleted') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-pink-200 bg-white px-4 py-2 text-xs font-bold text-pink-700 hover:bg-pink-50">
                    <i class="fa-solid fa-trash-arrow-up"></i>
                    Deleted Users
                </a>
            </div>
        @endcan

        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4">
            @foreach([
                ['Total Pengguna', $stats['total'], 'fa-users'],
                ['Admin', $stats['admin'], 'fa-user-shield'],
                ['Pekerja', $stats['pekerja'], 'fa-helmet-safety'],
                ['Masyarakat', $stats['masyarakat'], 'fa-user-group']
            ] as [$label, $count, $icon])
                <div class="bg-white rounded-2xl border border-pink-100 shadow-sm p-4 sm:p-5 min-w-0">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-bold text-slate-500">{{ $label }}</p>
                        <i class="fa-solid {{ $icon }} text-pink-400"></i>
                    </div>
                    <p class="mt-3 text-3xl font-extrabold text-slate-800">
                        {{ $count }}
                    </p>
                </div>
            @endforeach
        </div>

        <div class="bg-white rounded-2xl border border-pink-100 shadow-sm overflow-hidden">

            <div class="px-5 sm:px-6 py-5 border-b border-pink-50">
                <form method="GET" action="{{ route('user-management.index') }}"
                      class="flex flex-col lg:flex-row lg:items-center gap-3">

                    <div class="relative flex-1 min-w-0">
                        <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-pink-300 text-xs"></i>
                        <input type="search"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Cari nama atau email pengguna..."
                               maxlength="100"
                               class="block w-full h-11 rounded-xl border border-pink-100 bg-white pl-10 pr-4 text-sm focus:border-pink-300 focus:ring-pink-200">
                    </div>

                    <select name="role"
                            class="h-11 w-full lg:w-44 rounded-xl border border-pink-100 bg-white text-sm focus:border-pink-300 focus:ring-pink-200">
                        <option value="">Semua Role</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="pekerja" {{ request('role') === 'pekerja' ? 'selected' : '' }}>Pekerja</option>
                        <option value="masyarakat" {{ request('role') === 'masyarakat' ? 'selected' : '' }}>Masyarakat</option>
                    </select>

                    <button type="submit"
                            class="h-11 inline-flex items-center justify-center rounded-xl bg-pink-100 hover:bg-pink-200 text-pink-700 px-5 text-xs font-bold transition">
                        Filter
                    </button>

                    <a href="{{ route('user-management.index') }}"
                       class="h-11 inline-flex items-center justify-center rounded-xl border border-pink-100 text-slate-500 hover:bg-pink-50 px-5 text-xs font-bold transition">
                        Reset
                    </a>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[1050px] text-left border-collapse">
                    <thead class="bg-pink-50/60">
                        <tr class="text-[11px] uppercase tracking-wider text-slate-500">
                            <th class="px-5 py-4">Pengguna</th>
                            <th class="px-5 py-4">Role</th>
                            <th class="px-5 py-4">Tipe Akun</th>
                            <th class="px-5 py-4">Tanggal Bergabung</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4">Tindakan</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-pink-50">
                        @forelse($users as $user)
                            <tr class="hover:bg-pink-50/30">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-pink-100 text-pink-600 flex items-center justify-center font-extrabold text-sm shrink-0">
                                            {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-bold text-slate-800">
                                                {{ $user->name }}
                                            </p>
                                            <p class="text-xs text-slate-500 break-all">
                                                {{ $user->email }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    @if($user->isSuperAdmin())
                                        <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full bg-violet-50 border border-violet-100 text-violet-700 px-3 py-1.5 text-xs font-bold">
                                            <i class="fa-solid fa-shield-halved text-[10px]"></i>
                                            Super Admin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-pink-50 text-pink-700 px-3 py-1.5 text-xs font-bold">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    <span class="text-xs text-slate-600">
                                        {{ $user->google_id ? 'Google OAuth' : 'Email & Password' }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-xs text-slate-500">
                                    {{ $user->created_at?->format('d M Y') ?? '-' }}
                                </td>

                                <td class="px-5 py-4">
                                    <span class="rounded-full bg-emerald-50 text-emerald-700 px-3 py-1 text-xs font-bold">
                                        Terdaftar
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    @if(auth()->user()->can('changeUserRole') && !$user->isSuperAdmin() && $user->id !== auth()->id())
                                        <form method="POST"
                                              action="{{ route('user-management.update-role', $user) }}"
                                              class="flex items-center gap-2"
                                              onsubmit="return confirm('Yakin ingin mengubah role pengguna ini?')">
                                            @csrf
                                            @method('PATCH')
                                            <select name="role"
                                                    class="rounded-xl border-pink-100 text-xs focus:border-pink-300 focus:ring-pink-200">
                                                @foreach(['admin' => 'Admin', 'pekerja' => 'Pekerja', 'masyarakat' => 'Masyarakat'] as $value => $label)
                                                    <option value="{{ $value }}" {{ $user->role === $value ? 'selected' : '' }}>
                                                        {{ $label }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit"
                                                    class="rounded-xl bg-pink-100 hover:bg-pink-200 px-3 py-2 text-xs font-bold text-pink-700">
                                                Simpan
                                            </button>

                                        </form>

                                        <form method="POST"
                                              action="{{ route('user-management.destroy-user', $user) }}"
                                              class="mt-2"
                                              onsubmit="const answer = prompt('Ketik DELETE untuk menghapus akun ini:'); if (answer !== 'DELETE') return false; this.querySelector('[name=confirmation]').value = answer; return true;">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="confirmation" value="">
                                            <button type="submit"
                                                    class="rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-xs font-bold text-red-700 hover:bg-red-100">
                                                <i class="fa-solid fa-trash-can mr-1"></i>
                                                Hapus Akun
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-slate-400">Tidak tersedia</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <i class="fa-solid fa-users-slash text-3xl text-pink-200"></i>
                                    <p class="mt-3 text-sm font-bold text-slate-700">
                                        Pengguna tidak ditemukan
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-pink-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
