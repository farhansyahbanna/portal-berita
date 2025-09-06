<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function recent()
    {
        try {
            $posts = Post::with('user')
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();

            return response()->json([
                'success' => true,
                'data' => $posts
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch recent posts'
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            // Validasi input
            $query = Post::with('user')
            ->whereNotNull('published_at')
                ->orderBy('published_at', 'desc');

            // Search filter
            if ($request->has('search')) {
                $query->where('title', 'like', '%' . $request->search . '%');
            }

            // Pagination
            $posts = $query->paginate($request->per_page ?? 10);

            return response()->json([
                'data' => $posts->items(),
                'meta' => [
                    'current_page' => $posts->currentPage(),
                    'per_page' => $posts->perPage(),
                    'total' => $posts->total(),
                    'last_page' => $posts->lastPage(),
                ],
                'links' => [
                    'first' => $posts->url(1),
                    'last' => $posts->url($posts->lastPage()),
                    'prev' => $posts->previousPageUrl(),
                    'next' => $posts->nextPageUrl(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('PostController index error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch posts',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $post = Post::with(['user', 'comments'])->find($id);

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $post
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch post'
            ], 500);
        }
    }

    public function create()
    {
        return view('admin.posts.create');
    }

    // Method untuk menyimpan post baru
    public function store(Request $request)
    {
        // Validasi data
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'published_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Simpan post
            $post = Post::create([
                'title' => $request->title,
                'content' => $request->content,
                'published_at' => $request->published_at,
                'user_id' => $request->user()->id,
                'status' => $request->published_at ? 'published' : 'draft'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Post created successfully',
                'data' => $post
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Method untuk update post
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'published_at' => 'nullable|date',
            ]);

            $post = Post::findOrFail($id);
            $post->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Post berhasil diperbarui',
                'data' => $post
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui post',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Hapus Post
    public function destroy($id)
    {
        try {
            $post = Post::findOrFail($id);
            $post->delete();

            return response()->json([
                'success' => true,
                'message' => 'Post berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus post',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
