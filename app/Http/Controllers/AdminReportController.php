<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminReportController extends Controller
{
    public function index()
    {
        abort_unless(Auth::user()->is_admin, 403);

        $reports = Report::with(['user', 'category', 'city'])
            ->latest()
            ->paginate(15);

        return view('admin.reports.index', compact('reports'));
    }

    public function updateStatus(Request $request, Report $report)
    {
        abort_unless(Auth::user()->is_admin, 403);

        $validated = $request->validate([
            'status' => 'required|in:OPEN,IN_PROGRESS,RESOLVED',
        ]);

        $report->update($validated);

        return redirect()->route('admin.reports.index')->with('success', 'Report status updated successfully!');
    }
}
