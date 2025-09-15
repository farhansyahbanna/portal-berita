<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function recents(Request $request)
    {
        try {
            $comments = Comment::with('post')
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
            
            return response()->json([
                'success' => true,
                'data' => $comments
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch recent comments',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $query = Comment::with('post'); 

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $query->where('content', 'like', '%' . $request->search . '%');
            }

            $comments = $query->paginate($request->per_page ?? 20);

            return response()->json([
                'success' => true,
                'data' => $comments->items(),
                'meta' => [
                    'current_page' => $comments->currentPage(),
                    'per_page' => $comments->perPage(),
                    'total' => $comments->total(),
                    'last_page' => $comments->lastPage(),
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data komentar',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comment not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,approved,rejected,spam'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $comment->update([
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Comment status updated successfully',
                'data' => $comment
            ]);

        } catch (\Exception $e) {
            \Log::error('AdminCommentController updateStatus error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update comment status'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comment not found'
            ], 404);
        }

        try {
            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('AdminCommentController destroy error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete comment'
            ], 500);
        }
    }

    public function recent()
    {
        try {
            $comments = Comment::with(['post', 'user'])
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();

            return response()->json([
                'success' => true,
                'data' => $comments
            ]);

        } catch (\Exception $e) {
            \Log::error('AdminCommentController recent error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch recent comments'
            ], 500);
        }
    }

    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comment_ids' => 'required|array',
            'comment_ids.*' => 'integer|exists:comments,id',
            'action' => 'required|in:approve,reject,spam,delete'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $commentIds = $request->comment_ids;
            $action = $request->action;

            switch ($action) {
                case 'approve':
                    Comment::whereIn('id', $commentIds)->update(['status' => 'approved']);
                    break;
                case 'reject':
                    Comment::whereIn('id', $commentIds)->update(['status' => 'rejected']);
                    break;
                case 'spam':
                    Comment::whereIn('id', $commentIds)->update(['status' => 'spam']);
                    break;
                case 'delete':
                    Comment::whereIn('id', $commentIds)->delete();
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => 'Bulk action completed successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('AdminCommentController bulkAction error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk action'
            ], 500);
        }
    }
}