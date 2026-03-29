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

        $reports = Report::with(['user', 'category', 'city', 'quartier'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.reports.index', compact('reports', 'status'));
    }

    public function show(Report $report)
    {
        $report->load(['user', 'category', 'city', 'quartier', 'comments.user']);

        return view('admin.reports.show', compact('report'));
    }

    public function updateStatus(Request $request, Report $report)
    {
        $request->validate(['status' => 'required|in:OPEN,IN_PROGRESS,RESOLVED,REJECTED']);

        $report->update(['status' => $request->status]);

        return back()->with('success', 'Statut du signalement mis à jour.');
    }

    public function destroy(Report $report)
    {
        $report->delete();

        return redirect()->route('admin.reports.index')->with('success', 'Signalement supprimé.');
    }
}
