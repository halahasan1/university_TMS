<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Department extends Model
{
    protected $fillable = ['name', 'faculty_id'];

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }

    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    protected $withCount = ['profiles'];
}

