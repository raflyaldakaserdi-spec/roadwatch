<x-app-layout>
{{-- ROADWATCH_WORKER_CLEAN_V4 --}}
<style>
.rw4{width:100%;max-width:1540px;margin:0 auto;color:#172033;font-family:inherit}
.rw4 *{box-sizing:border-box}
.rw4-head{display:flex;justify-content:space-between;align-items:center;gap:20px;padding:25px 28px;background:#fff;border:1px solid #f5dce7;border-radius:20px;box-shadow:0 4px 22px rgba(81,27,54,.035)}
.rw4-eyebrow{font-size:11px;letter-spacing:1.5px;font-weight:800;color:#d92c77;margin:0 0 7px}
.rw4-head h1{font-size:27px;line-height:1.2;letter-spacing:-.7px;font-weight:800;color:#172033;margin:0}
.rw4-sub{font-size:13px;color:#748095;margin:9px 0 0;line-height:1.6}
.rw4-head-icon{width:55px;height:55px;display:flex;align-items:center;justify-content:center;border-radius:17px;background:#fff0f6;color:#d92c77;font-size:23px;flex-shrink:0}
.rw4-stats{display:grid!important;grid-template-columns:repeat(4,minmax(0,1fr))!important;gap:15px!important;margin-top:20px}
.rw4-stat{min-width:0;background:#fff;border:1px solid #f5dce7;border-radius:18px;padding:19px 21px;box-shadow:0 3px 16px rgba(81,27,54,.025)}
.rw4-stat-top{display:flex;align-items:center;justify-content:space-between;gap:8px}
.rw4-stat-label{font-size:12px;font-weight:700;color:#66738a}
.rw4-stat-icon{width:38px;height:38px;display:flex;align-items:center;justify-content:center;border-radius:12px;font-size:16px}
.rw4-stat-number{font-size:32px;font-weight:800;line-height:1.1;color:#172033;margin:13px 0 3px}
.rw4-stat-foot{font-size:11px;color:#94a0b2;margin:0}
.rw4-layout{display:grid!important;grid-template-columns:minmax(0,1.8fr) minmax(290px,1fr)!important;gap:18px!important;margin-top:20px;align-items:start}
.rw4-panel{min-width:0;background:#fff;border:1px solid #f5dce7;border-radius:19px;overflow:hidden;box-shadow:0 3px 16px rgba(81,27,54,.025)}
.rw4-panel-head{padding:20px 23px;border-bottom:1px solid #f9e7ee;display:flex;align-items:center;justify-content:space-between;gap:12px}
.rw4-panel-title{font-size:16px;font-weight:800;color:#172033;margin:0;display:flex;align-items:center;gap:10px}
.rw4-panel-title i{color:#df397d}
.rw4-panel-sub{font-size:12px;color:#8792a3;margin:5px 0 0}
.rw4-pill{background:#fff0f6;color:#c52b70;font-size:11px;font-weight:800;padding:6px 10px;border-radius:100px;white-space:nowrap}
.rw4-empty{min-height:290px;padding:45px 24px;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center}
.rw4-empty-icon{width:66px;height:66px;border-radius:19px;background:#fff0f6;color:#e77dac;display:flex;align-items:center;justify-content:center;font-size:26px;margin-bottom:17px}
.rw4-empty h3{font-size:16px;font-weight:800;color:#233049;margin:0}
.rw4-empty p{font-size:12px;color:#8b96a7;max-width:350px;line-height:1.75;margin:9px 0 0}
.rw4-task{padding:20px 23px;border-bottom:1px solid #f5eaf0}
.rw4-task:last-child{border-bottom:0}
.rw4-task-top{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap}
.rw4-task-name{font-size:14px;font-weight:800;color:#cc2c72;text-decoration:none}
.rw4-task-location{font-size:13px;color:#344055;font-weight:650;margin:7px 0 0}
.rw4-task-note{font-size:12px;color:#7a8699;line-height:1.6;margin:10px 0}
.rw4-tags{display:flex;gap:6px;flex-wrap:wrap}
.rw4-tag{font-size:10px;font-weight:800;padding:5px 9px;border-radius:100px;white-space:nowrap}
.rw4-task-bottom{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-top:14px;font-size:11px;color:#7b879a}
.rw4-task-bottom a{font-weight:800;color:#cf2c75;text-decoration:none}
.rw4-aside{display:grid!important;grid-template-columns:minmax(0,1fr)!important;gap:17px!important;min-width:0}
.rw4-summary{padding:22px 23px}
.rw4-summary h2,.rw4-links h2{font-size:15px;font-weight:800;color:#172033;margin:0 0 21px;display:flex;align-items:center;gap:9px}
.rw4-summary h2 i,.rw4-links h2 i{color:#df397d}
.rw4-progress-label{display:flex;justify-content:space-between;align-items:flex-end;gap:8px;color:#7a879a;font-size:12px}
.rw4-progress-label strong{font-size:26px;color:#cf2c75;line-height:1}
.rw4-track{height:8px;border-radius:20px;background:#fae6ee;overflow:hidden;margin:12px 0 20px}
.rw4-fill{height:100%;border-radius:20px;background:#df397d}
.rw4-summary-row{display:flex;justify-content:space-between;gap:10px;font-size:12px;color:#718095;margin-top:13px}
.rw4-summary-row strong{color:#233049}
.rw4-links{padding:21px 23px}
.rw4-links h2{margin-bottom:14px}
.rw4-link{display:flex;align-items:center;gap:12px;padding:12px 0;text-decoration:none!important;color:#27364d!important}
.rw4-link+.rw4-link{border-top:1px solid #f6eaf0}
.rw4-link-icon{height:38px;width:38px;border-radius:11px;display:flex;align-items:center;justify-content:center;background:#fff0f6;color:#d92c77;flex-shrink:0}
.rw4-link-text{flex:1;min-width:0}
.rw4-link-text strong{display:block;font-size:12px;font-weight:800}
.rw4-link-text small{display:block;font-size:11px;color:#8b96a7;margin-top:3px}
.rw4-link-arrow{color:#a6b0c0;font-size:12px}
.rw4-pagination{padding:14px 22px;border-top:1px solid #f5eaf0}
@media(max-width:1100px){.rw4-layout{grid-template-columns:minmax(0,1fr)!important}.rw4-aside{grid-template-columns:repeat(2,minmax(0,1fr))!important}}
@media(max-width:700px){.rw4-stats{grid-template-columns:repeat(2,minmax(0,1fr))!important}.rw4-aside{grid-template-columns:minmax(0,1fr)!important}.rw4-head{padding:21px}.rw4-head h1{font-size:23px}.rw4-head-icon{width:44px;height:44px}.rw4-stat{padding:16px}.rw4-stat-number{font-size:27px}.rw4-panel-head{padding:18px}.rw4-task{padding:18px}}
@media(max-width:390px){.rw4-stats{gap:9px!important}.rw4-stat{padding:12px}.rw4-stat-label{font-size:10px}.rw4-head-icon{display:none}}
</style>
@php
    $rw4Total = (int) ($taskStats['total'] ?? 0);
    $rw4Completed = (int) ($taskStats['completed'] ?? 0);
    $rw4Percent = $rw4Total > 0 ? min(100, max(0, round($rw4Completed / $rw4Total * 100))) : 0;
@endphp
<div class="rw4">
    <header class="rw4-head">
        <div>
            <p class="rw4-eyebrow">ROADWATCH / FIELD OPERATIONS</p>
            <h1>Dashboard Pekerja</h1>
            <p class="rw4-sub">Halo, {{ auth()->user()->name }}. Pantau penugasan dan progres pekerjaan lapangan Anda.</p>
        </div>
        <div class="rw4-head-icon"><i class="fa-solid fa-helmet-safety"></i></div>
    </header>

    {{-- ROADWATCH_WORKER_FLASH_V6 --}}
    @if(session('success'))
        <div style="margin-top:16px;padding:14px 18px;background:#ecfdf5;border:1px solid #a7f3d0;color:#047857;border-radius:13px;font-size:12px;font-weight:700">
            <i class="fa-solid fa-circle-check" style="margin-right:7px"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div style="margin-top:16px;padding:14px 18px;background:#fff1f2;border:1px solid #fecdd3;color:#be123c;border-radius:13px;font-size:12px">
            <strong>Permintaan belum dapat diproses:</strong>
            <ul style="margin:8px 0 0;padding-left:18px;list-style:disc">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="rw4-stats" aria-label="Statistik penugasan">
        @foreach([
            ['Total Penugasan', $rw4Total, 'fa-clipboard-list', '#fff0f6', '#d92c77', 'Semua tugas Anda'],
            ['Menunggu', $taskStats['assigned'] ?? 0, 'fa-clock', '#fff8e8', '#d68a00', 'Belum dimulai'],
            ['Dalam Pengerjaan', $taskStats['progress'] ?? 0, 'fa-screwdriver-wrench', '#eef5ff', '#3476e9', 'Sedang ditangani'],
            ['Selesai', $rw4Completed, 'fa-circle-check', '#e9fbf3', '#0b9e69', 'Terverifikasi Admin']
        ] as [$label, $value, $icon, $bg, $fg, $foot])
            <article class="rw4-stat">
                <div class="rw4-stat-top">
                    <span class="rw4-stat-label">{{ $label }}</span>
                    <span class="rw4-stat-icon" style="background:{{ $bg }};color:{{ $fg }}"><i class="fa-solid {{ $icon }}"></i></span>
                </div>
                <p class="rw4-stat-number">{{ number_format($value) }}</p>
                <p class="rw4-stat-foot">{{ $foot }}</p>
            </article>
        @endforeach
    </section>

    <div class="rw4-layout">
        <section class="rw4-panel">
            <div class="rw4-panel-head">
                <div>
                    <h2 class="rw4-panel-title"><i class="fa-solid fa-list-check"></i> Penugasan Saya</h2>
                    <p class="rw4-panel-sub">Pekerjaan yang ditetapkan Admin untuk Anda</p>
                </div>
                <span class="rw4-pill">{{ $rw4Total }} tugas</span>
            </div>
            @forelse($myTasks as $task)
                @php
                    $rw4PriorityStyle = match($task->priority) {
                        'High' => 'background:#fff0f0;color:#c63e47',
                        'Medium' => 'background:#fff8e8;color:#a76b00',
                        default => 'background:#edf5ff;color:#286cc2',
                    };
                    $rw4StatusStyle = match($task->status) {
                        'In Progress' => 'background:#edf5ff;color:#286cc2',
                        'Pending Review' => 'background:#f4edff;color:#8150b7',
                        'Completed' => 'background:#e9fbf3;color:#087f57',
                        default => 'background:#fff8e8;color:#a76b00',
                    };
                @endphp
                <article class="rw4-task">
                    <div class="rw4-task-top">
                        <div>
                            <a class="rw4-task-name" href="{{ route('detections.show', $task->detection_id) }}">{{ $task->detection?->detection_code ?? 'Deteksi tidak tersedia' }}</a>
                            <p class="rw4-task-location"><i class="fa-solid fa-location-dot" style="color:#e08bb1;margin-right:5px"></i>{{ $task->detection?->location ?? 'Lokasi belum tersedia' }}</p>
                        </div>
                        <div class="rw4-tags">
                            <span class="rw4-tag" style="{{ $rw4PriorityStyle }}">{{ $task->priority }}</span>
                            <span class="rw4-tag" style="{{ $rw4StatusStyle }}">{{ $task->status }}</span>
                        </div>
                    </div>
                    <p class="rw4-task-note">{{ $task->instructions ?: 'Tidak ada instruksi tambahan dari Admin.' }}</p>
                    {{-- ROADWATCH_WORKER_ACTIONS_V6 --}}
                    <div style="margin-top:15px">

                        @if($task->status === 'Assigned')
                            <form method="POST"
                                  action="{{ route('my-assignments.start', $task->id) }}">
                                @csrf
                                <button type="submit"
                                        style="background:#db2777;color:white;border:0;border-radius:11px;padding:10px 16px;font-size:12px;font-weight:800;cursor:pointer">
                                    <i class="fa-solid fa-play" style="margin-right:6px"></i>
                                    Mulai Pekerjaan
                                </button>
                            </form>

                        @elseif($task->status === 'In Progress')
                            <details style="border:1px solid #f5dce7;border-radius:13px;padding:13px;background:#fffafd">
                                <summary style="cursor:pointer;color:#be185d;font-size:12px;font-weight:800">
                                    <i class="fa-solid fa-camera" style="margin-right:6px"></i>
                                    Kirim Bukti Pekerjaan
                                </summary>

                                <form method="POST"
                                      action="{{ route('my-assignments.submit', $task->id) }}"
                                      enctype="multipart/form-data"
                                      style="display:grid;gap:11px;margin-top:14px">
                                    @csrf

                                    <label style="font-size:12px;font-weight:700;color:#334155">
                                        Catatan Hasil Pekerjaan
                                    </label>

                                    <textarea name="worker_notes"
                                              required
                                              maxlength="3000"
                                              rows="3"
                                              placeholder="Jelaskan hasil pekerjaan yang telah dilakukan..."
                                              style="width:100%;border:1px solid #f3d2e0;border-radius:10px;font-size:12px;padding:10px">{{ $task->worker_notes }}</textarea>

                                    <label style="font-size:12px;font-weight:700;color:#334155">
                                        Foto Bukti Perbaikan
                                    </label>

                                    <input type="file"
                                           name="completion_photo"
                                           accept="image/jpeg,image/png,image/webp"
                                           required
                                           style="width:100%;font-size:12px">

                                    <p style="font-size:11px;color:#94a3b8">
                                        Format JPG, PNG, atau WEBP. Maksimal 5 MB.
                                    </p>

                                    <button type="submit"
                                            style="background:#db2777;color:white;border:0;border-radius:10px;padding:11px 14px;font-size:12px;font-weight:800;cursor:pointer">
                                        <i class="fa-solid fa-paper-plane" style="margin-right:6px"></i>
                                        Kirim untuk Review Admin
                                    </button>
                                </form>
                            </details>

                        @elseif($task->status === 'Pending Review')
                            <div style="background:#f5f0ff;color:#7544a5;padding:12px;border-radius:11px;font-size:12px;font-weight:700">
                                <i class="fa-solid fa-hourglass-half" style="margin-right:6px"></i>
                                Bukti pekerjaan sudah dikirim. Menunggu review Admin.
                            </div>

                        @elseif($task->status === 'Completed')
                            <div style="background:#e9fbf3;color:#087f57;padding:12px;border-radius:11px;font-size:12px;font-weight:700">
                                <i class="fa-solid fa-circle-check" style="margin-right:6px"></i>
                                Pekerjaan selesai dan telah disetujui Admin.
                            </div>
                        @endif

                        @if($task->completion_photo
                            && in_array($task->status, ['Pending Review', 'Completed']))
                            <div style="margin-top:12px">
                                <p style="font-size:11px;font-weight:800;color:#64748b;margin-bottom:7px">
                                    Dokumentasi Pekerjaan
                                </p>
                                <a href="{{ asset('storage/' . $task->completion_photo) }}"
                                   target="_blank" rel="noopener noreferrer">
                                    <img src="{{ asset('storage/' . $task->completion_photo) }}"
                                         alt="Bukti pekerjaan"
                                         loading="lazy"
                                         style="width:100%;max-width:270px;max-height:170px;object-fit:cover;border-radius:11px;border:1px solid #f5dce7">
                                </a>
                            </div>
                        @endif

                    </div>

                    <div class="rw4-task-bottom">
                        <span><i class="fa-regular fa-calendar"></i> Tenggat: {{ $task->due_date?->format('d M Y') ?? 'Tidak ditentukan' }}</span>
                        <a href="{{ route('detections.show', $task->detection_id) }}">Lihat detail <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </article>
            @empty
                <div class="rw4-empty">
                    <div class="rw4-empty-icon"><i class="fa-solid fa-clipboard-check"></i></div>
                    <h3>Belum Ada Penugasan</h3>
                    <p>Belum ada tugas yang diberikan kepada Anda. Penugasan baru dari Admin akan muncul di sini.</p>
                </div>
            @endforelse
            @if($myTasks->hasPages())
                <div class="rw4-pagination">{{ $myTasks->links() }}</div>
            @endif
        </section>

        <aside class="rw4-aside">
            <section class="rw4-panel rw4-summary">
                <h2><i class="fa-solid fa-chart-simple"></i> Status Pekerjaan</h2>
                <div class="rw4-progress-label"><span>Tingkat penyelesaian</span><strong>{{ $rw4Percent }}%</strong></div>
                <div class="rw4-track"><div class="rw4-fill" style="width:{{ $rw4Percent }}%"></div></div>
                <div class="rw4-summary-row"><span>Menunggu verifikasi</span><strong>{{ $taskStats['review'] ?? 0 }}</strong></div>
                <div class="rw4-summary-row"><span>Selesai diverifikasi</span><strong style="color:#0b9e69">{{ $rw4Completed }}</strong></div>
                <div class="rw4-summary-row"><span>Dalam pengerjaan</span><strong>{{ $taskStats['progress'] ?? 0 }}</strong></div>
            </section>
            <nav class="rw4-panel rw4-links" aria-label="Akses cepat">
                <h2><i class="fa-solid fa-arrow-up-right-dots"></i> Akses Cepat</h2>
                <a class="rw4-link" href="{{ route('map.index') }}">
                    <span class="rw4-link-icon"><i class="fa-solid fa-map-location-dot"></i></span>
                    <span class="rw4-link-text"><strong>Peta Monitoring</strong><small>Lihat lokasi kerusakan jalan</small></span>
                    <i class="fa-solid fa-arrow-up-right-from-square rw4-link-arrow"></i>
                </a>
                <a class="rw4-link" href="{{ route('detections.index') }}">
                    <span class="rw4-link-icon"><i class="fa-solid fa-road"></i></span>
                    <span class="rw4-link-text"><strong>Data Deteksi</strong><small>Informasi kerusakan terdeteksi</small></span>
                    <i class="fa-solid fa-arrow-up-right-from-square rw4-link-arrow"></i>
                </a>
            </nav>
        </aside>
    </div>
</div>
</x-app-layout>
