<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EditorDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'editor']);
    }

    public function index()
    {
        $user = Auth::user();
        
        // Statistics untuk editor
        $stats = [
            'total_posts' => Post::where('user_id', $user->id)->count(),
            'published_posts' => Post::where('user_id', $user->id)
                ->whereNotNull('published_at')->count(),
            'draft_posts' => Post::where('user_id', $user->id)
                ->whereNull('published_at')->count(),
            'recent_comments' => Comment::whereHas('post', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count(),
        ];

        // Recent posts oleh editor ini
        $recent_posts = Post::with(['comments' => function($query) {
                $query->latest()->take(3);
            }])
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // Recent comments pada post editor ini
        $recent_comments = Comment::with(['post'])
            ->whereHas('post', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->latest()
            ->take(5)
            ->get();

        return view('admin.editor-dashboard', compact('stats', 'recent_posts', 'recent_comments'));
    }
}
