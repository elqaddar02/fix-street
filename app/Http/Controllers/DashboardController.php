<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get user's reports
        $reports = Report::where('user_id', $user->id)
            ->with(['category', 'city', 'quartier', 'comments'])
            ->latest()
            ->get();

        // Statistics
        $totalReports = $reports->count();
        $openReports = $reports->where('status', 'OPEN')->count();
        $inProgressReports = $reports->where('status', 'IN_PROGRESS')->count();
        $resolvedReports = $reports->where('status', 'RESOLVED')->count();
        $closedReports = $reports->where('status', 'CLOSED')->count();

        // Reports by category
        $categoryGroups = $reports->groupBy('category.name');
        $reportsByCategory = [];
        foreach ($categoryGroups as $category => $group) {
            $reportsByCategory[$category] = $group->count();
        }

        // Reports by status
        $reportsByStatus = [
            'Open' => $openReports,
            'In Progress' => $inProgressReports,
            'Resolved' => $resolvedReports,
            'Closed' => $closedReports,
        ];

        // Recent reports (last 5)
        $recentReports = $reports->take(5);

        return view('dashboard', compact(
            'reports',
            'totalReports',
            'openReports',
            'inProgressReports',
            'resolvedReports',
            'closedReports',
            'reportsByCategory',
            'reportsByStatus',
            'recentReports'
        ));
    }
}