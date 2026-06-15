<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportLikeController extends Controller
{
    /**
     * Toggle like status for a report.
     * If the user has liked the report, remove the like.
     * If the user hasn't liked the report, add the like.
     */
    public function toggle(Request $request, Report $report)
    {
        $user = Auth::user();

        // Check if user has already liked this report
        $like = $report->likes()->where('user_id', $user->id)->first();

        if ($like) {
            // Unlike
            $like->delete();
            $liked = false;
        } else {
            // Like
            $report->likes()->create([
                'user_id' => $user->id,
            ]);
            $liked = true;
        }

        // If it's an AJAX request, return JSON
        if ($request->expectsJson()) {
            return response()->json([
                'liked' => $liked,
                'likes_count' => $report->likes()->count(),
            ]);
        }

        // Otherwise, redirect back
        return redirect()->back()->with('success', $liked ? 'Report liked!' : 'Report unliked!');
    }
}
