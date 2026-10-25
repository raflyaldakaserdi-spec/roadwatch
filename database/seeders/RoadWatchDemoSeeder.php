<?php

namespace Database\Seeders;

use App\Models\Detection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class RoadWatchDemoSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['Jl. Margonda Raya, Depok', -6.3721, 106.8312],
            ['Jl. Juanda, Depok', -6.3945, 106.8186],
            ['Jl. Raya Bogor, Depok', -6.3612, 106.8451],
            ['Jl. Sawangan Raya, Depok', -6.3988, 106.7925],
            ['Jl. Akses UI, Depok', -6.3541, 106.8423],
            ['Jl. Cinere Raya, Depok', -6.3211, 106.7812],
            ['Jl. Lenteng Agung, Jakarta', -6.3300, 106.8340],
            ['Jl. TB Simatupang, Jakarta', -6.2910, 106.8110],
            ['Jl. Raya Pasar Minggu, Jakarta', -6.2830, 106.8430],
            ['Jl. Kalimalang, Bekasi', -6.2480, 106.9710],
        ];

        $statuses = ['Detected', 'In Progress', 'Repaired'];
        $severities = ['Low', 'Medium', 'High'];

        $inserted = 0;

        for ($i = 1; $i <= 50; $i++) {
            $code = sprintf('DEMO-PTH-%04d', $i);

            // Hindari duplikasi jika seeder dijalankan ulang
            if (Detection::where('detection_code', $code)->exists()) {
                continue;
            }

            $location = $locations[($i - 1) % count($locations)];
            $date = Carbon::now('Asia/Jakarta')
                ->subDays(($i - 1) % 15)
                ->subMinutes($i * 7);

            Detection::create([
                'detection_code' => $code,
                'image' => 'https://images.unsplash.com/photo-1515162816999-a0c47dc192f7?w=600',
                'location' => $location[0],
                'latitude' => $location[1] + (($i % 7) - 3) * 0.0003,
                'longitude' => $location[2] + (($i % 5) - 2) * 0.0003,
                'detection_date' => $date->format('Y-m-d'),
                'detection_day' => $date->format('l'),
                'detection_time' => $date->format('H:i:s'),
                'status' => $statuses[($i - 1) % 3],
                'severity' => $severities[($i - 1) % 3],
            ]);

            $inserted++;
        }

        $this->command->info("Berhasil menambahkan {$inserted} data dummy RoadWatch.");
    }
}

