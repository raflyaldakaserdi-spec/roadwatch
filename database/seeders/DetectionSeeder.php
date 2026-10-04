<?php

namespace Database\Seeders;

use App\Models\Detection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DetectionSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan data lama jika ada
        Detection::truncate();

        $today = Carbon::today()->format('Y-m-d');
        $dayName = Carbon::now()->format('l');

        $dummyData = [
            [
                'detection_code' => 'PTH-0001',
                'image' => 'https://images.unsplash.com/photo-1515162816999-a0c47dc192f7?w=600',
                'location' => 'Jl. Margonda Raya, Depok',
                'latitude' => -6.37210000,
                'longitude' => 106.83120000,
                'detection_date' => $today,
                'detection_day' => $dayName,
                'detection_time' => '10:25:31',
                'status' => 'Detected',
                'severity' => 'High',
            ],
            [
                'detection_code' => 'PTH-0002',
                'image' => 'https://images.unsplash.com/photo-1515162816999-a0c47dc192f7?w=600',
                'location' => 'Jl. Juanda, Depok',
                'latitude' => -6.39450000,
                'longitude' => 106.81860000,
                'detection_date' => $today,
                'detection_day' => $dayName,
                'detection_time' => '11:12:43',
                'status' => 'Detected',
                'severity' => 'Medium',
            ],
            [
                'detection_code' => 'PTH-0003',
                'image' => 'https://images.unsplash.com/photo-1515162816999-a0c47dc192f7?w=600',
                'location' => 'Jl. Raya Bogor KM 29',
                'latitude' => -6.36120000,
                'longitude' => 106.84510000,
                'detection_date' => $today,
                'detection_day' => $dayName,
                'detection_time' => '08:40:15',
                'status' => 'Detected',
                'severity' => 'High',
            ],
            [
                'detection_code' => 'PTH-0004',
                'image' => 'https://images.unsplash.com/photo-1515162816999-a0c47dc192f7?w=600',
                'location' => 'Jl. Sawangan Raya, Depok',
                'latitude' => -6.39880000,
                'longitude' => 106.79250000,
                'detection_date' => $today,
                'detection_day' => $dayName,
                'detection_time' => '09:15:20',
                'status' => 'In Progress',
                'severity' => 'Low',
            ],
            [
                'detection_code' => 'PTH-0005',
                'image' => 'https://images.unsplash.com/photo-1515162816999-a0c47dc192f7?w=600',
                'location' => 'Jl. Akses UI, Kelapa Dua',
                'latitude' => -6.35410000,
                'longitude' => 106.84230000,
                'detection_date' => $today,
                'detection_day' => $dayName,
                'detection_time' => '14:02:11',
                'status' => 'Repaired',
                'severity' => 'Medium',
            ],
            [
                'detection_code' => 'PTH-0006',
                'image' => 'https://images.unsplash.com/photo-1515162816999-a0c47dc192f7?w=600',
                'location' => 'Jl. Cinere Raya, Depok',
                'latitude' => -6.32110000,
                'longitude' => 106.78120000,
                'detection_date' => $today,
                'detection_day' => $dayName,
                'detection_time' => '15:30:00',
                'status' => 'Detected',
                'severity' => 'High',
            ],
        ];

        foreach ($dummyData as $data) {
            Detection::create($data);
        }
    }
}