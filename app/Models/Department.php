<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    protected $fillable = ['name'];


    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }

    protected $withCount = ['profiles'];
}
