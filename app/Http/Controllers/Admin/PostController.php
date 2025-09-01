<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{


    public function __construct()
    {
        $this->middleware(['auth', 'editor'])->except(['show']);
    }

    public function index()
    {
       
        $user = Auth::user();

        
        if ($user->isAdmin()) {
            $posts = Post::with('user')->latest()->paginate(10);
        } else {
            // Editor hanya melihat post mereka sendiri
            $posts = Post::with('user')
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(10);
        }

        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.posts.create');
    }

    public function store(StorePostRequest $request)
    {
        $post = new Post($request->validated());
        $post->user_id = $request->user()->id;
        $post->save();

        return redirect()->route('admin.posts.index')
                         ->with('success', 'Post created successfully.');
    }

    // public function store(StorePostRequest $request)
    // {
    //     try {
    //         $validated = $request->validated();
            
    //         $post = new Post();
    //         $post->title = $validated['title'];
    //         $post->content = $validated['content'];
    //         $post->user_id = $request->user()->id();
            
    //         if (!empty($validated['published_at'])) {
    //             $post->published_at = $validated['published_at'];
    //         }
            
    //         $post->save();

    //         return redirect()->route('admin.posts.index')
    //             ->with('success', 'Post berhasil dibuat.');

    //     } catch (\Exception $e) {
    //         return redirect()->back()
    //             ->withInput()
    //             ->with('error', 'Gagal membuat post: ' . $e->getMessage());
    //     }
    // }

    public function show(Post $post)
    {
         $user = Auth::user();
        if ($user->isEditor() && $user->id !== $post->user_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        $user = Auth::user();

        if ($user->isEditor() && $user->id !== $post->user_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.posts.edit', compact('post'));
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $user = Auth::user();

        if ($user->isEditor() && $user->id !== $post->user_id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $validated = $request->validated();

            $post->title   = $validated['title'];
            $post->content = $validated['content'];
            $post->published_at = $validated['published_at'] ?? null;

            $post->save();

            return redirect()->route('admin.posts.index')
                ->with('success', 'Post berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui post: ' . $e->getMessage());
        }
    }

    public function destroy(Post $post)
    {
        $user = Auth::user();

        if ($user->isEditor() && $user->id !== $post->user_id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $post->delete();

            return redirect()->route('admin.posts.index')
                ->with('success', 'Post berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus post: ' . $e->getMessage());
        }
    }
}
