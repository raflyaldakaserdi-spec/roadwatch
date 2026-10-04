<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" class="text-xs font-bold text-slate-700" />
            <x-text-input id="name" class="block mt-1 w-full text-xs rounded-xl border-purple-100" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-1" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-xs font-bold text-slate-700" />
            <x-text-input id="email" class="block mt-1 w-full text-xs rounded-xl border-purple-100" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Role Selection Cards -->
        <div>
            <x-input-label :value="__('Daftar Sebagai Peran')" class="text-xs font-bold text-slate-700 mb-2" />
            <div class="grid grid-cols-3 gap-2">
                <!-- Admin -->
                <label class="relative flex flex-col items-center justify-center p-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-violet-50 hover:border-violet-300 transition group">
                    <input type="radio" name="role" value="admin" class="sr-only peer" {{ old('role') == 'admin' ? 'checked' : '' }} required>
                    <div class="p-2 bg-white rounded-lg text-slate-600 peer-checked:bg-violet-600 peer-checked:text-white transition">
                        <i class="fa-solid fa-user-shield text-sm"></i>
                    </div>
                    <span class="text-[11px] font-bold text-slate-700 mt-1.5 peer-checked:text-violet-600">Admin</span>
                    <span class="absolute inset-0 rounded-xl border-2 border-transparent peer-checked:border-violet-600 pointer-events-none"></span>
                </label>

                <!-- Pekerja -->
                <label class="relative flex flex-col items-center justify-center p-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-violet-50 hover:border-violet-300 transition group">
                    <input type="radio" name="role" value="pekerja" class="sr-only peer" {{ old('role', 'pekerja') == 'pekerja' ? 'checked' : '' }}>
                    <div class="p-2 bg-white rounded-lg text-slate-600 peer-checked:bg-violet-600 peer-checked:text-white transition">
                        <i class="fa-solid fa-helmet-safety text-sm"></i>
                    </div>
                    <span class="text-[11px] font-bold text-slate-700 mt-1.5 peer-checked:text-violet-600">Pekerja</span>
                    <span class="absolute inset-0 rounded-xl border-2 border-transparent peer-checked:border-violet-600 pointer-events-none"></span>
                </label>

                <!-- Masyarakat -->
                <label class="relative flex flex-col items-center justify-center p-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-violet-50 hover:border-violet-300 transition group">
                    <input type="radio" name="role" value="masyarakat" class="sr-only peer" {{ old('role') == 'masyarakat' ? 'checked' : '' }}>
                    <div class="p-2 bg-white rounded-lg text-slate-600 peer-checked:bg-violet-600 peer-checked:text-white transition">
                        <i class="fa-solid fa-users text-sm"></i>
                    </div>
                    <span class="text-[11px] font-bold text-slate-700 mt-1.5 peer-checked:text-violet-600">Masyarakat</span>
                    <span class="absolute inset-0 rounded-xl border-2 border-transparent peer-checked:border-violet-600 pointer-events-none"></span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Kata Sandi')" class="text-xs font-bold text-slate-700" />
            <x-text-input id="password" class="block mt-1 w-full text-xs rounded-xl border-purple-100" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" class="text-xs font-bold text-slate-700" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full text-xs rounded-xl border-purple-100" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="flex items-center justify-between pt-2">
            <a class="underline text-xs text-slate-600 hover:text-slate-900 rounded-md" href="{{ route('login') }}">
                {{ __('Sudah punya akun?') }}
            </a>

            <x-primary-button class="ms-4 bg-violet-600 hover:bg-violet-700">
                {{ __('Daftar') }}
            </x-primary-button>
        </div>
    </form>
</x-app-layout>