<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Detection;

class DetectionObserver
{
    private function details(Detection $detection): array
    {
        return $detection->only([
            'detection_code',
            'location',
            'latitude',
            'longitude',
            'status',
            'severity',
        ]);
    }

    private function source(): string
    {
        return request()->is('api/*') ? 'esp32_api' :
            (app()->runningInConsole() ? 'console' : 'web');
    }

    public function created(Detection $detection): void
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'created',
            'description' => "Data deteksi {$detection->detection_code} ditambahkan di {$detection->location}",
            'subject_type' => Detection::class,
            'subject_id' => $detection->id,
            'source' => $this->source(),
            'new_values' => $this->details($detection),
        ]);
    }

    public function updated(Detection $detection): void
    {
        $allowed = [
            'location',
            'latitude',
            'longitude',
            'status',
            'severity',
        ];

        $changes = array_intersect_key(
            $detection->getChanges(),
            array_flip($allowed)
        );

        if (empty($changes)) {
            return;
        }

        $oldValues = array_intersect_key(
            $detection->getRawOriginal(),
            $changes
        );

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'updated',
            'description' => "Data deteksi {$detection->detection_code} diperbarui",
            'subject_type' => Detection::class,
            'subject_id' => $detection->id,
            'source' => $this->source(),
            'old_values' => $oldValues,
            'new_values' => $changes,
        ]);
    }

    public function deleted(Detection $detection): void
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'description' => "Data deteksi {$detection->detection_code} dihapus",
            'subject_type' => Detection::class,
            'subject_id' => $detection->id,
            'source' => $this->source(),
            'old_values' => $this->details($detection),
        ]);
    }
}

