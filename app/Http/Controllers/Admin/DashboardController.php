<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\City;
use App\Models\Report;
use App\Models\ReportComment;
use App\Models\User;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /** Number of days covered by the activity chart and the trend comparison. */
    private const TREND_DAYS = 30;

    public function index()
    {
        $windowStart = Carbon::today()->subDays(self::TREND_DAYS - 1);
        $previousStart = $windowStart->copy()->subDays(self::TREND_DAYS);

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

        // Real period-over-period movement, so the cards stop showing invented percentages.
        $trends = [
            'users' => $this->trend(User::query(), $windowStart, $previousStart),
            'reports' => $this->trend(Report::query(), $windowStart, $previousStart),
            'comments' => $this->trend(ReportComment::query(), $windowStart, $previousStart),
        ];

        // Daily report volume for the activity chart.
        $reportsPerDay = $this->dailySeries($windowStart);

        // Share of reports that reached a terminal state.
        $resolutionRate = $totalReports > 0
            ? round(($resolvedReports / $totalReports) * 100)
            : 0;

        // Reports by category
        $reportsByCategory = Report::select('categories.name')
            ->join('categories', 'reports.category_id', '=', 'categories.id')
            ->groupBy('categories.name')
            ->selectRaw('COUNT(*) as count')
            ->orderBy('count', 'desc')
            ->limit(8)
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
            'recentActivity',
            'trends',
            'reportsPerDay',
            'resolutionRate'
        ));
    }

    /**
     * Compare the current window against the one immediately before it.
     *
     * Returns the percentage change, or null when the previous window was
     * empty — a jump from 0 has no meaningful percentage and the card renders
     * it as "nouveau" instead of a fabricated number.
     *
     * @return array{current: int, percent: int|null, up: bool}
     */
    private function trend($query, Carbon $windowStart, Carbon $previousStart): array
    {
        $current = (clone $query)->where('created_at', '>=', $windowStart)->count();
        $previous = (clone $query)
            ->where('created_at', '>=', $previousStart)
            ->where('created_at', '<', $windowStart)
            ->count();

        if ($previous === 0) {
            return [
                'current' => $current,
                'percent' => null,
                'up' => $current > 0,
            ];
        }

        $percent = (int) round((($current - $previous) / $previous) * 100);

        return [
            'current' => $current,
            'percent' => $percent,
            'up' => $percent >= 0,
        ];
    }

    /**
     * Daily report counts across the window, with empty days filled in as 0 so
     * the chart keeps a continuous x-axis.
     *
     * @return array<int, array{date: string, count: int}>
     */
    private function dailySeries(Carbon $windowStart): array
    {
        $counts = Report::where('created_at', '>=', $windowStart)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day')
            ->toArray();

        $series = [];
        for ($day = $windowStart->copy(); $day->lte(Carbon::today()); $day->addDay()) {
            $key = $day->toDateString();
            $series[] = [
                'date' => $key,
                'count' => (int) ($counts[$key] ?? 0),
            ];
        }

        return $series;
    }
}
