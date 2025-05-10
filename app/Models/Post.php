<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'title', 'content'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function likes()
    {
        return $this->belongsToMany(User::class, 'like_post')->withTimestamps();
    }
    public function getLikedByUserAttribute()
    {
        return auth()->check() && $this->likes->contains(auth()->id());
    }
    protected $appends = ['liked_by_user'];
}
