<?php

namespace Database\Seeders;

use App\Models\Detection;
use App\Models\User;
use App\Models\WorkAssignment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class RoadWatchDemo26Seeder extends Seeder
{
    public function run(): void
    {
        $worker = User::query()
            ->whereKey(6)
            ->where('role', 'pekerja')
            ->firstOrFail();

        $admin = User::query()
            ->where('role', 'admin')
            ->orderBy('id')
            ->firstOrFail();

        $locations = [
            'Jl. Sudirman, Jakarta',
            'Jl. Thamrin, Jakarta',
            'Jl. Gatot Subroto, Jakarta',
            'Jl. Rasuna Said, Jakarta',
            'Jl. Casablanca, Jakarta',
            'Jl. Prof. Dr. Satrio, Jakarta',
            'Jl. HR Rasuna Said, Jakarta',
            'Jl. MT Haryono, Jakarta',
            'Jl. DI Panjaitan, Jakarta',
            'Jl. Ahmad Yani, Jakarta',
            'Jl. Pramuka, Jakarta',
            'Jl. Salemba Raya, Jakarta',
            'Jl. Kramat Raya, Jakarta',
            'Jl. Senen Raya, Jakarta',
            'Jl. Gunung Sahari, Jakarta',
            'Jl. Mangga Dua Raya, Jakarta',
            'Jl. Gajah Mada, Jakarta',
            'Jl. Hayam Wuruk, Jakarta',
            'Jl. S Parman, Jakarta',
            'Jl. Tomang Raya, Jakarta',
            'Jl. Daan Mogot, Jakarta',
            'Jl. Panjang, Jakarta',
            'Jl. Kebon Jeruk Raya, Jakarta',
            'Jl. Arteri Pondok Indah, Jakarta',
            'Jl. Fatmawati, Jakarta',
            'Jl. TB Simatupang, Jakarta',
        ];

        DB::transaction(function () use ($worker, $admin, $locations) {
            for ($i = 1; $i <= 26; $i++) {
                $code = sprintf('RW-DEMO-%03d', $i);

                $existing = Detection::query()
                    ->where('detection_code', $code)
                    ->first();

                if ($existing) {
                    $assignment = WorkAssignment::query()
                        ->where('detection_id', $existing->id)
                        ->first();

                    if (!$assignment || $assignment->worker_id !== $worker->id) {
                        throw new RuntimeException(
                            "STOP: Konflik data dummy {$code}. "
                            . "Periksa secara manual."
                        );
                    }

                    continue;
                }

                $status = match (true) {
                    $i <= 8 => 'Assigned',
                    $i <= 16 => 'In Progress',
                    $i <= 22 => 'Pending Review',
                    default => 'Completed',
                };

                $priority = match ($i % 3) {
                    0 => 'High',
                    1 => 'Medium',
                    default => 'Low',
                };

                $date = now()->subDays(27 - $i);

                $detection = Detection::create([
                    'detection_code' => $code,
                    'image' => 'demo/detection-placeholder.jpg',
                    'location' => $locations[$i - 1],
                    'latitude' => -6.20 + ($i * 0.001),
                    'longitude' => 106.80 + ($i * 0.001),
                    'detection_date' => $date->toDateString(),
                    'detection_day' => $date->format('l'),
                    'detection_time' => $date->format('H:i:s'),
                    'status' => $status === 'Completed'
                        ? 'Repaired'
                        : 'Detected',
                    'severity' => $priority,
                ]);

                $assignment = WorkAssignment::create([
                    'detection_id' => $detection->id,
                    'worker_id' => $worker->id,
                    'assigned_by' => $admin->id,
                    'priority' => $priority,
                    'status' => $status,
                    'instructions' =>
                        '[DEMO] Periksa dan lakukan perbaikan '
                        . 'kerusakan jalan di '
                        . $locations[$i - 1],
                    'worker_notes' => $i > 16
                        ? '[DEMO] Perbaikan jalan telah dilakukan.'
                        : null,
                    'completion_photo' => $i > 16
                        ? 'demo/assignment-proof-placeholder.jpg'
                        : null,
                    'due_date' => $date->copy()->addDays(7),
                    'started_at' => $i > 8 ? $date->copy()->addDay() : null,
                    'submitted_at' => $i > 16 ? $date->copy()->addDays(2) : null,
                    'completed_at' => $i > 22 ? $date->copy()->addDays(3) : null,
                ]);
            }
        });

        $this->command?->info(
            'SUCCESS: 26 demo assignments tersedia untuk worker ID 6.'
        );
    }
}
