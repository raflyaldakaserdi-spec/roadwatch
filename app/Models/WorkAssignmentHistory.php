<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkAssignmentHistory extends Model
{
    protected $fillable = [
        'work_assignment_id',
        'user_id',
        'action',
        'old_status',
        'new_status',
        'notes',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(
            WorkAssignment::class,
            'work_assignment_id'
        );
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
