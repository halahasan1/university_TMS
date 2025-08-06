<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'file_path',
        'assigned_to',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function subtasks()
    {
        return $this->hasMany(Subtask::class);
    }

    protected static function booted()
    {
        static::creating(function ($task) {
            if (auth()->check()) {
                $task->created_by = auth()->id();
            }
        });
    }

}
