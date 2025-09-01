<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;



class Post extends Model
{

    /**
     * App\Models\Post
     *
     * @property int $id
     * @property string $title
     * @property \App\Models\User $user
     * @method static \Illuminate\Database\Eloquent\Builder|Post whereTitle($value)
     */
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'published_at',
        'user_id'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function commentsCount()
    {
        return $this->comments()->count();
    }

    public function getExcerptAttribute()
    {
         return Str::of(strip_tags($this->content))->limit(150);
    }
}
