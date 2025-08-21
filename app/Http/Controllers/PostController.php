<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::whereNotNull('published_at')
                    ->orderBy('published_at', 'desc')
                    ->paginate(10);
        
        return view('posts.index', compact('posts'));
    }

    public function show($id)
    {
        $post = Post::with('comments')->whereNotNull('published_at')->findOrFail($id);
        $commentsCount = $post->comments->count();
        
        return view('posts.show', compact('post',  'commentsCount'));
    }
}
