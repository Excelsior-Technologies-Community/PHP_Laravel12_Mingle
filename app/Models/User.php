<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'profile_photo_path',
        'bio',
        'cover_photo',
        'theme',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }

    public function themeColors(): array
    {
        $defaults = [
            'bg'      => '#f9fafb',
            'surface' => '#ffffff',
            'text'    => '#111827',
            'accent'  => '#4f46e5',
        ];

        if (! $this->theme) {
            return $defaults;
        }

        $decoded = json_decode($this->theme, true);

        if (! is_array($decoded)) {
            return $defaults;
        }

        return array_merge($defaults, array_filter($decoded));
    }
}