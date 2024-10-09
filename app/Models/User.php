<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'age',
        'picture_path',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    public function getPictureUrlAttribute()
    {
        return Storage::url($this->picture_path);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
