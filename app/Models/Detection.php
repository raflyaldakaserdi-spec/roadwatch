<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'detection_code',
        'image',
        'location',
        'latitude',
        'longitude',
        'detection_date',
        'detection_day',
        'detection_time',
        'status',
        'severity',
    ];

    // Relasi ke User (Pekerja / Masyarakat yang mengunggah)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}