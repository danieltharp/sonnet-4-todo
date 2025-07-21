<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the task.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark the task as complete.
     */
    public function markAsComplete(): void
    {
        $this->update([
            'status' => 'complete',
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark the task as incomplete.
     */
    public function markAsIncomplete(): void
    {
        $this->update([
            'status' => 'incomplete',
            'completed_at' => null,
        ]);
    }

    /**
     * Check if the task is complete.
     */
    public function isComplete(): bool
    {
        return $this->status === 'complete';
    }
}
