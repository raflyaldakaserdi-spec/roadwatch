<x-app-layout>
    <div class="w-full px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-pink-400">
                    RoadWatch Administration
                </p>
                <h1 class="mt-2 text-3xl font-extrabold text-slate-800">
                    Deleted Users
                </h1>
                <p class="mt-2 text-sm text-slate-500">
                    Akun yang dinonaktifkan dan dapat dipulihkan.
                </p>
            </div>
            <a href="{{ route('user-management.index') }}"
               class="rounded-xl bg-pink-100 px-4 py-3 text-xs font-bold text-pink-700">
                Kembali ke User Management
            </a>
        </div>

        @if(session('user_role_success'))
            <div class="rounded-xl bg-emerald-50 p-4 text-sm font-bold text-emerald-700">
                {{ session('user_role_success') }}
            </div>
        @endif

        <div class="overflow-x-auto rounded-2xl border border-pink-100 bg-white">
            <table class="w-full min-w-[700px] text-left text-sm">
                <thead class="bg-pink-50 text-slate-600">
                    <tr>
                        <th class="p-4">Pengguna</th>
                        <th class="p-4">Role</th>
                        <th class="p-4">Tanggal Dihapus</th>
                        <th class="p-4">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="border-t border-pink-50">
                            <td class="p-4">
                                <p class="font-bold text-slate-800">{{ $user->name }}</p>
                                <p class="text-xs text-slate-500">{{ $user->email }}</p>
                            </td>
                            <td class="p-4">{{ ucfirst($user->role) }}</td>
                            <td class="p-4">
                                {{ $user->deleted_at?->format('d M Y H:i') }}
                            </td>
                            <td class="p-4">
                                <form method="POST"
                                      action="{{ route('user-management.restore-user', $user->id) }}"
                                      onsubmit="return confirm('Pulihkan akun ini?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="rounded-xl bg-emerald-50 px-4 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-100">
                                        Restore Account
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-10 text-center text-slate-500">
                                Belum ada akun yang dihapus.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $users->links() }}
    </div>
</x-app-layout>
