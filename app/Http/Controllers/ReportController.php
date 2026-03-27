<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $cities = City::all(); // Keep cities for now, but focus on category and date

        $selectedCategory = $request->query('category');
        $selectedDate = $request->query('date');

        $reportsQuery = Report::with(['user', 'category', 'city', 'comments.user'])->latest();

        if ($selectedCategory) {
            $reportsQuery->where('category_id', $selectedCategory);
        }

        if ($selectedDate) {
            switch ($selectedDate) {
                case 'today':
                    $reportsQuery->whereDate('created_at', today());
                    break;
                case 'week':
                    $reportsQuery->where('created_at', '>=', now()->startOfWeek());
                    break;
                case 'month':
                    $reportsQuery->where('created_at', '>=', now()->startOfMonth());
                    break;
            }
        }

        $reports = $reportsQuery->paginate(12)->withQueryString();

        return view('reports.index', compact('reports', 'categories', 'cities', 'selectedCategory', 'selectedDate'));
    }

    public function create()
    {
        $categories = Category::all();
        $cities = City::all();

        return view('reports.create', compact('categories', 'cities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'city_id'     => 'required|exists:cities,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'latitude'    => 'nullable|numeric|between:-90,90',
            'longitude'   => 'nullable|numeric|between:-180,180',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('reports', 'public');
        }

        $validated['status'] = 'OPEN';
        $validated['user_id'] = Auth::id();

        Report::create($validated);

        return redirect()->route('dashboard')->with('success', 'Report submitted successfully!');
    }

    public function show(Report $report)
    {
        $report->load(['comments.user', 'category', 'city', 'user']);

        return view('reports.show', compact('report'));
    }

    public function storeComment(Request $request, Report $report)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $report->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Comment submitted successfully.');
    }
}
