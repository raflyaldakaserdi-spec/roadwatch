<x-app-layout>


    <div class="w-full px-4 sm:px-6 lg:px-8 py-6">
        <div class="w-full max-w-none ml-0 mr-0 space-y-6">

            <div class="mb-6">
                <p class="text-[10px] font-extrabold tracking-[0.2em] text-pink-400 uppercase">
                    RoadWatch Preferences
                </p>
                <h1 class="mt-2 text-2xl sm:text-3xl font-extrabold text-slate-800">
                    Account Settings
                </h1>
                <p class="mt-2 text-sm text-slate-500">
                    Kelola profil, keamanan akun, dan hak akses RoadWatch.
                </p>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 items-start">
            <div class="min-w-0 rounded-2xl border border-pink-100 bg-white shadow-sm p-5 sm:p-7">
                <div class="w-full min-w-0">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="min-w-0 rounded-2xl border border-pink-100 bg-white shadow-sm p-5 sm:p-7">
                <div class="w-full min-w-0">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
            </div><!-- End settings two-column grid -->

            <!-- RoadWatch Role Access Settings V1 -->
            <div class="min-w-0 rounded-2xl border border-pink-100 bg-white shadow-sm p-5 sm:p-7">
                <div class="max-w-xl space-y-5">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">
                            Pengajuan Hak Akses
                        </h3>
                        <p class="mt-1 text-sm text-slate-500">
                            Kelola permintaan akses Admin atau Pekerja pada RoadWatch.
                        </p>
                    </div>

                    @if(session('role_request_success'))
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                            {{ session('role_request_success') }}
                        </div>
                    @endif

                    @if($user->role === 'masyarakat')
                        @if($latestRoleRequest && $latestRoleRequest->status === 'pending')
                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                                <p class="font-semibold text-amber-800">
                                    Menunggu Persetujuan
                                </p>
                                <p class="mt-1 text-sm text-amber-700">
                                    Pengajuan akses
                                    <strong>{{ ucfirst($latestRoleRequest->requested_role) }}</strong>
                                    sedang ditinjau administrator.
                                </p>
                            </div>
                        @else
                            @if($latestRoleRequest && $latestRoleRequest->status === 'rejected')
                                <div class="rounded-xl border border-rose-200 bg-rose-50 p-4">
                                    <p class="font-semibold text-rose-800">
                                        Pengajuan Sebelumnya Ditolak
                                    </p>
                                    @if($latestRoleRequest->review_note)
                                        <p class="mt-1 text-sm text-rose-700">
                                            {{ $latestRoleRequest->review_note }}
                                        </p>
                                    @endif
                                </div>
                            @endif

                            <form method="POST"
                                  action="{{ route('role-requests.store') }}"
                                  class="space-y-4">
                                @csrf

                                <div>
                                    <label for="requested_role"
                                           class="block text-sm font-semibold text-slate-700 mb-2">
                                        Role yang Diajukan
                                    </label>
                                    <select id="requested_role"
                                            name="requested_role"
                                            required
                                            class="w-full rounded-xl border-slate-200 text-sm focus:border-violet-500 focus:ring-violet-500">
                                        <option value="">Pilih Role</option>
                                        <option value="pekerja" {{ old('requested_role') === 'pekerja' ? 'selected' : '' }}>
                                            Pekerja
                                        </option>
                                        <option value="admin" {{ old('requested_role') === 'admin' ? 'selected' : '' }}>
                                            Admin
                                        </option>
                                    </select>
                                    <x-input-error :messages="$errors->get('requested_role')" class="mt-2" />
                                </div>

                                <div>
                                    <label for="organization"
                                           class="block text-sm font-semibold text-slate-700 mb-2">
                                        Instansi / Organisasi (Opsional)
                                    </label>
                                    <input id="organization"
                                           type="text"
                                           name="organization"
                                           maxlength="255"
                                           value="{{ old('organization') }}"
                                           class="w-full rounded-xl border-slate-200 text-sm focus:border-violet-500 focus:ring-violet-500"
                                           placeholder="Nama instansi atau organisasi">
                                    <x-input-error :messages="$errors->get('organization')" class="mt-2" />
                                </div>

                                <div>
                                    <label for="reason"
                                           class="block text-sm font-semibold text-slate-700 mb-2">
                                        Alasan Pengajuan
                                    </label>
                                    <textarea id="reason"
                                              name="reason"
                                              rows="3"
                                              minlength="10"
                                              maxlength="2000"
                                              required
                                              class="w-full rounded-xl border-slate-200 text-sm focus:border-violet-500 focus:ring-violet-500"
                                              placeholder="Jelaskan alasan membutuhkan akses khusus...">{{ old('reason') }}</textarea>
                                    <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                                </div>

                                <p class="text-xs leading-relaxed text-slate-500">
                                    Akun tetap berstatus Masyarakat hingga pengajuan disetujui.
                                    Akses Admin hanya dapat disetujui Super Admin.
                                </p>

                                <button type="submit"
                                        class="inline-flex items-center justify-center rounded-xl bg-violet-600 px-5 py-3 text-sm font-semibold text-white hover:bg-violet-700 transition">
                                    Kirim Pengajuan
                                </button>
                            </form>
                        @endif
                    @else
                        <div class="rounded-xl border border-violet-200 bg-violet-50 p-4">
                            <p class="text-sm font-semibold text-violet-800">
                                Role Aktif: {{ ucfirst($user->role) }}
                            </p>
                            <p class="mt-1 text-sm text-violet-700">
                                Akun Anda sudah memiliki hak akses khusus.
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="min-w-0 rounded-2xl border border-pink-100 bg-white shadow-sm p-5 sm:p-7">
                <div class="w-full min-w-0">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
