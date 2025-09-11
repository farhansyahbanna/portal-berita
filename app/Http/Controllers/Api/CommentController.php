<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request)
    {
        $post = Post::findOrFail($request->post_id);

        $comment = new Comment();
        $comment->post_id = $post->id;
        $comment->name = $request->name;
        $comment->email = $request->email;
        $comment->content = $request->content;
        $comment->save();

        return response()->json([
            'success' => true,
            'comment' => $comment,
            'message' => 'Comment added successfully'
        ]);
    }

    public function index($postId)
    {
        try {
            $post = Post::find($postId);

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found'
                ], 404);
            }

            $query = Comment::where('post_id', $postId)
                        ->where('status', 'approved') // Hanya comment approved
                        ->orderBy('created_at', 'desc');

            $perPage = $request->per_page ?? 10;
            $comments = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $comments->items(),
                'meta' => [
                    'current_page' => $comments->currentPage(),
                    'per_page' => $comments->perPage(),
                    'total' => $comments->total(),
                    'last_page' => $comments->lastPage(),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('PostController getComments error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch comments'
            ], 500);
        }
    }
}
