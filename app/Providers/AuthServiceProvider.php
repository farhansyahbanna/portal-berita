<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    public function boot()
    {
        $this->registerPolicies();

        // Define admin gate
        Gate::define('admin', function ($user) {
            return $user->isAdmin();
        });

        // Define post ownership gate
        Gate::define('manage-post', function ($user, $post) {
            return $user->isAdmin() && $user->id === $post->user_id;
        });

          // Gate untuk editor access
        Gate::define('access-editor', function (User $user) {
            return $user->isAdmin() || $user->isEditor();
        });

        // Gate untuk edit post
        Gate::define('edit-post', function (User $user, Post $post) {
            return $user->isAdmin() || ($user->isEditor() && $user->id === $post->user_id);
        });

        // Gate untuk delete post
        Gate::define('delete-post', function (User $user, Post $post) {
            return $user->isAdmin() || ($user->isEditor() && $user->id === $post->user_id);
        });
    }
}