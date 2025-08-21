<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
     public function index()
    {
        $stats = [
            'total_posts' => Post::count(),
            'total_comments' => Comment::count(),
            'published_posts' => Post::whereNotNull('published_at')->count(),
            'pending_posts' => Post::whereNull('published_at')->count(),
        ];

        $recent_posts = Post::with('user')
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
