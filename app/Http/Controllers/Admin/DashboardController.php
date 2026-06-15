<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Report;
use App\Models\ReportComment;
use App\Models\User;
use App\Models\Category;
use App\Models\AuditLog;

class DashboardController extends Controller
{
    public function index()
    {
        // Core Statistics
        $totalUsers = User::count();
        $totalReports = Report::count();
        $totalCities = City::count();
        $totalCategories = Category::count();

        // User statistics
        $activeUsers = User::where('active', true)->count();
        $inactiveUsers = User::where('active', false)->count();

        // Report statistics
        $openReports = Report::where('status', 'OPEN')->count();
        $inProgressReports = Report::where('status', 'IN_PROGRESS')->count();
        $resolvedReports = Report::where('status', 'RESOLVED')->count();
        $rejectedReports = Report::where('status', 'REJECTED')->count();

        // Comment statistics
        $totalComments = ReportComment::count();
        $pendingComments = ReportComment::whereNull('approved')->count();

        // Status counts for chart
        $statusCounts = [
            'OPEN' => $openReports,
            'IN_PROGRESS' => $inProgressReports,
            'RESOLVED' => $resolvedReports,
            'REJECTED' => $rejectedReports,
        ];

        // Reports by category
        $reportsByCategory = Report::select('categories.name')
            ->join('categories', 'reports.category_id', '=', 'categories.id')
            ->groupBy('categories.name')
            ->selectRaw('COUNT(*) as count')
            ->orderBy('count', 'desc')
            ->pluck('count', 'name')
            ->toArray();

        // Reports by city
        $reportsByCity = Report::select('cities.name')
            ->join('cities', 'reports.city_id', '=', 'cities.id')
            ->groupBy('cities.name')
            ->selectRaw('COUNT(*) as count')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->pluck('count', 'name')
            ->toArray();

        // Latest data
        $latestReports = Report::with(['user', 'category', 'city', 'district'])
            ->latest()
            ->take(8)
            ->get();

        $latestUsers = User::latest()->take(8)->get();

        // Recent activity
        $recentActivity = AuditLog::with('admin')
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalReports',
            'totalCities',
            'totalCategories',
            'activeUsers',
            'inactiveUsers',
            'totalComments',
            'pendingComments',
            'openReports',
            'inProgressReports',
            'resolvedReports',
            'rejectedReports',
            'statusCounts',
            'reportsByCategory',
            'reportsByCity',
            'latestReports',
            'latestUsers',
            'recentActivity'
        ));
    }
}
