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

     public function getComments($postId)
    {
        try {
            $comments = Comment::where('post_id', $postId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($comments);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal mengambil komentar'
            ], 500);
        }
    }
}
