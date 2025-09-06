<?php

namespace App\Http\Controllers\Api\Editor;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Hanya ambil post milik user yang login
            $query = Post::where('user_id', auth()->id())
                        ->orderBy('created_at', 'desc');

            // Filter by status
             if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Search in name or email
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
                });
            }
            $perPage = $request->per_page ?? 15;
            $posts = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $posts->items(),
                'meta' => [
                    'current_page' => $posts->currentPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                    'last_page' => $posts->lastPage(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('EditorPostController index error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch posts'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:draft,pending,published',
            'published_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $post = Post::create([
                'title' => $request->title,
                'content' => $request->content,
                'status' => $request->status,
                'published_at' => $request->published_at,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Post created successfully',
                'data' => $post
            ], 201);

        } catch (\Exception $e) {
            \Log::error('EditorPostController store error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create post'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $post = Post::where('user_id', auth()->id())->find($id);

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found or access denied'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $post
            ]);

        } catch (\Exception $e) {
            \Log::error('EditorPostController show error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch post'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $post = Post::where('user_id', auth()->id())->find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found or access denied'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:draft,pending,published',
            'published_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $post->update([
                'title' => $request->title,
                'content' => $request->content,
                'status' => $request->status,
                'published_at' => $request->published_at,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Post updated successfully',
                'data' => $post
            ]);

        } catch (\Exception $e) {
            \Log::error('EditorPostController update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update post'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $post = Post::where('user_id', auth()->id())->find($id);

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Post not found or access denied'
            ], 404);
        }

        try {
            $post->delete();

            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('EditorPostController destroy error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete post'
            ], 500);
        }
    }
}
