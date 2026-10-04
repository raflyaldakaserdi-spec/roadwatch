<x-app-layout>
    <!-- Header Page -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Data Deteksi Jalan Berlubang</h1>
            <p class="text-sm text-slate-500 mt-1 font-medium">Daftar rekaman deteksi jalan berlubang secara real-time</p>
        </div>
    </div>

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
                <button type="submit" class="flex-1 py-2 px-4 bg-violet-600 hover:bg-violet-700 text-white font-bold text-xs rounded-xl transition shadow-sm">
                    Filter
                </button>
                <a href="{{ route('detections.index') }}" class="py-2 px-4 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs rounded-xl transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl border border-purple-100/60 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
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
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
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

                            <!-- Status Badge -->
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold tracking-wide
                                    {{ $item->status == 'Detected' ? 'bg-[#FEF3C7] text-[#92400E]' : '' }}
                                    {{ $item->status == 'In Progress' ? 'bg-[#E0F2FE] text-[#0369A1]' : '' }}
                                    {{ $item->status == 'Repaired' ? 'bg-[#D1FAE5] text-[#065F46]' : '' }}">
                                    {{ $item->status == 'Repaired' ? 'Selesai' : $item->status }}
                                </span>
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
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $detections->links() }}
            </div>
        @endif
    </div>
</x-app-layout>