<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\ReportComment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $query = ReportComment::with(['user', 'report']);

        if ($request->has('status')) {
            $status = $request->get('status');
            if ($status === 'pending') {
                $query->whereNull('approved');
            } elseif ($status === 'approved') {
                $query->where('approved', true);
            } elseif ($status === 'rejected') {
                $query->where('approved', false);
            }
        }

        $comments = $query->latest()->paginate(20);
        return view('admin.comments.index', compact('comments'));
    }

    public function approve(ReportComment $comment)
    {
        $comment->update(['approved' => true]);
        logAdminAction('Approve', 'Comment', $comment->id, "Approved comment on report #{$comment->report_id}");
        return back()->with('success', 'Commentaire approuvé.');
    }

    public function reject(ReportComment $comment)
    {
        $comment->update(['approved' => false]);
        logAdminAction('Reject', 'Comment', $comment->id, "Rejected comment on report #{$comment->report_id}");
        return back()->with('success', 'Commentaire rejeté.');
    }

    public function destroy(ReportComment $comment)
    {
        $reportId = $comment->report_id;
        $comment->delete();
        logAdminAction('Delete', 'Comment', $comment->id, "Deleted comment from report #{$reportId}");
        return back()->with('success', 'Commentaire supprimé.');
    }
}