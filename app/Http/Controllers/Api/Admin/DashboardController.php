<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        try {
            $stats = [
                'total_posts' => Post::count(),
                'published_posts' => Post::where('status', 'published')->count(),
                'pending_posts' => Post::where('status', 'pending')->count(),
                'draft_posts' => Post::where('status', 'draft')->count(),
                'total_comments' => Comment::count(),
                'approved_comments' => Comment::where('status', 'approved')->count(),
                'pending_comments' => Comment::where('status', 'pending')->count(),
                'total_users' => User::count(),
                'admin_users' => User::where('role', 'admin')->count(),
                'editor_users' => User::where('role', 'editor')->count(),
            ];
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Dashboard error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
        
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
