<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Post extends Model
{

    protected $fillable = ['title', 'content', 'image_path', 'user_id', 'slug', 'created_at', 'last_update'];

    public $timestamps = false;

    public function getImageUrlAttribute()
    {
        return Storage::url($this->image_path);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($post) {
            $post->slug = Str::slug($post->title);


            $post->created_at = now();
            $post->last_update = now();
        });

        static::updating(function ($post) {
            $post->last_update = now();
        });
    }
}
