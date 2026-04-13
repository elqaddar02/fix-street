<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $sortBy = $request->input('sort', 'latest'); // 'latest', 'oldest', 'mostLiked'

        $reportsQuery = Report::with(['user', 'category', 'city', 'district'])
            ->withCount('likes')
            ->when($status, fn($q) => $q->where('status', $status));

        // Apply sorting
        switch ($sortBy) {
            case 'mostLiked':
                $reportsQuery->orderByDesc('likes_count');
                break;
            case 'oldest':
                $reportsQuery->oldest();
                break;
            case 'latest':
            default:
                $reportsQuery->latest();
                break;
        }

        $reports = $reportsQuery->paginate(15)->withQueryString();

        return view('admin.reports.index', compact('reports', 'status', 'sortBy'));
    }

    public function show(Report $report)
    {
        $report->load(['user', 'category', 'city', 'district', 'comments.user', 'likes']);
        $report->loadCount('likes');

        return view('admin.reports.show', compact('report'));
    }

    public function updateStatus(Request $request, Report $report)
    {
        $request->validate(['status' => 'required|in:OPEN,IN_PROGRESS,RESOLVED,REJECTED']);

        $report->update(['status' => $request->status]);

        return back()->with('success', 'Statut du signalement mis à jour.');
    }

    public function bulkUpdateStatus(Request $request)
    {
        $data = $request->validate([
            'report_ids' => 'required|array|min:1',
            'report_ids.*' => 'integer|exists:reports,id',
            'status' => 'required|in:OPEN,IN_PROGRESS,RESOLVED,REJECTED',
        ]);

        $updatedCount = Report::whereIn('id', $data['report_ids'])->update(['status' => $data['status']]);

        return back()->with('success', "Statut appliqué à {$updatedCount} signalement(s).");
    }

    public function destroy(Report $report)
    {
        $report->delete();

        return redirect()->route('admin.reports.index')->with('success', 'Signalement supprimé.');
    }
}
