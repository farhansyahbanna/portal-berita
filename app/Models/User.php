<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isEditor()
    {
        return $this->role === 'editor';
    }

     public function isUser()
    {
        return $this->role === 'user';
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

     public function hasRole($role)
    {
        return $this->role === $role;
    }

 
    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }


    public function scopeRegular($query)
    {
        return $query->where('role', 'user');
    }

    public function scopeEditor($query)
    {
        return $query->where('role', 'editor');
    }

    public function canEditPost(Post $post)
    {
        return $this->isAdmin() || ($this->isEditor() && $this->id === $post->user_id);
    }

    public function canDeletePost(Post $post)
    {
        return $this->isAdmin() || ($this->isEditor() && $this->id === $post->user_id);
    }


}
