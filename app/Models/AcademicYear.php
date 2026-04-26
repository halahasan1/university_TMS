<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    protected $fillable = ['year_number', 'label'];

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}
