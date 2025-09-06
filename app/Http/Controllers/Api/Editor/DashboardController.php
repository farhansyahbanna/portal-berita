<?php

namespace App\Http\Controllers\Api\Editor;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
     public function stats(Request $request)
    {
        try {
            $userId = auth()->id();
            
            $stats = [
                'total_posts' => Post::where('user_id', $userId)->count(),
                'published_posts' => Post::where('user_id', $userId)
                    ->where('status', 'published')
                    ->count(),
                'pending_posts' => Post::where('user_id', $userId)
                    ->where('status', 'pending')
                    ->count(),
                'draft_posts' => Post::where('user_id', $userId)
                    ->where('status', 'draft')
                    ->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            \Log::error('EditorDashboardController stats error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard stats'
            ], 500);
        }
    }

    public function recentPosts(Request $request)
    {
        try {
            $userId = auth()->id();
            
            $posts = Post::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(['id', 'title', 'status', 'created_at']);

            return response()->json([
                'success' => true,
                'data' => $posts
            ]);

        } catch (\Exception $e) {
            \Log::error('EditorDashboardController recentPosts error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch recent posts'
            ], 500);
        }
    }

    public function recentActivity(Request $request)
    {
        try {
            $userId = auth()->id();
            
            $posts = Post::where('user_id', $userId)
                ->orderBy('updated_at', 'desc')
                ->take(5)
                ->get();
            
            $activities = $posts->map(function ($post) {
                return [
                    'id' => $post->id,
                    'type' => 'post_' . $post->status,
                    'description' => 'Post "' . $post->title . '" ' . $post->status,
                    'created_at' => $post->updated_at
                ];
            });
            

            return response()->json([
                'success' => true,
                'data' => $activities
            ]);

        } catch (\Exception $e) {
            \Log::error('EditorDashboardController recentActivity error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch recent activity'
            ], 500);
        }
    }
}
