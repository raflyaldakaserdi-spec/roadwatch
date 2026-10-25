<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'device_code',
        'name',
        'mcu',
        'gps_module',
        'status',
        'last_seen_at',
        'gps_status',
        'latitude',
        'longitude',
        'firmware_version',
        'api_token_hash',
    ];

    protected $hidden = [
        'api_token_hash',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function getIsOnlineAttribute(): bool
    {
        return $this->status === 'Active'
            && $this->last_seen_at !== null
            && $this->last_seen_at->greaterThanOrEqualTo(now()->subMinutes(2));
    }

    public function detections()
    {
        return $this->hasMany(Detection::class);
    }
}
