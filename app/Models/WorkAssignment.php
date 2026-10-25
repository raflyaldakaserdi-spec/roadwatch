<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkAssignment extends Model
{
    protected $fillable = [
        'detection_id',
        'worker_id',
        'assigned_by',
        'priority',
        'status',
        'instructions',
        'worker_notes',
        'completion_photo',
        'due_date',
        'started_at',
        'submitted_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    // ROADWATCH_ASSIGNMENT_HISTORY_V7
    public function histories(): HasMany
    {
        return $this->hasMany(
            WorkAssignmentHistory::class,
            'work_assignment_id'
        );
    }

    public function detection(): BelongsTo
    {
        return $this->belongsTo(Detection::class);
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
