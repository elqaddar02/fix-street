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
    public function index()
    {
        $reports = Report::with(['user', 'category', 'city'])
            ->latest()
            ->paginate(12);

        return view('reports.index', compact('reports'));
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
}
