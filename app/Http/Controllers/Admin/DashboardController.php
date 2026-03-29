<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Report;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalReports = Report::count();
        $totalCities = City::count();

        $statusCounts = Report::selectRaw("status, count(*) as total")
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        $latestReports = Report::with(['user', 'category', 'city', 'quartier'])
            ->latest()
            ->take(8)
            ->get();

        $latestUsers = User::latest()->take(8)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalReports',
            'totalCities',
            'statusCounts',
            'latestReports',
            'latestUsers'
        ));
    }
}
