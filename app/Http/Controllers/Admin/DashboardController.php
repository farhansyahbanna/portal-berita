<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', 'admin']); 
    }

     public function index()
    {

        $user = Auth::user();

        $stats = [];

        if ($user->isAdmin()) {
            $stats = [
                'total_posts' => Post::count(),
                'total_comments' => Comment::count(),
                'published_posts' => Post::whereNotNull('published_at')->count(),
                'pending_posts' => Post::whereNull('published_at')->count(),
                'total_editors' => User::where('role', 'editor')->count(),
                'total_users' => User::where('role', 'user')->count(),
            ];
        } else {
            // Untuk editor, hanya tampilkan statistik post mereka sendiri
            $stats = [
                'total_posts' => Post::where('user_id', $user->id)->count(),
                'published_posts' => Post::where('user_id', $user->id)
                    ->whereNotNull('published_at')->count(),
                'pending_posts' => Post::where('user_id', $user->id)
                    ->whereNull('published_at')->count(),
            ];
        }

        $recent_posts = Post::with('user')
            ->when($user->isEditor(), function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
            ->latest()
            ->take(5)
            ->get();

        $recent_comments = Comment::with('post')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_posts', 'recent_comments'));
    }
}
