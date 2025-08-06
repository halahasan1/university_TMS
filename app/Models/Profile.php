<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'student_id',
        'bio',
        'phone',
        'department_id',
        'address',
        'image_path'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function getImageUrlAttribute()
    {
        return $this->image_path
            ? asset('storage/'.$this->image_path)
            : asset('images/default-avatar.png');
    }

}
