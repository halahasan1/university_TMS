<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasRoles,HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Profile relation
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }
    // Tasks assigned to this user
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }
    // Tasks created by this user (e.g., by professor or dean)
    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }
    // News created by user
    public function news(): HasMany
    {
        return $this->hasMany(News::class, 'author_id');
    }
    // Comments
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }


    protected static function booted()
    {
        static::created(function ($user) {
            $user->profile()->create(); //generate an embty profile for user
        });
    }

}
