<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\City;
use App\Models\District;
use App\Models\Quartier;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $cities = City::where('active', true)->get();
        $districts = District::all();
        $quartiers = Quartier::all();

        $cityCoordinates = [];
        foreach ($cities as $city) {
            $name = strtolower($city->name);
            $nameAr = strtolower($city->name_ar ?? '');

            if (str_contains($name, 'sal') || str_contains($nameAr, 'سلا')) {
                $cityCoordinates[$city->id] = ['lat' => 34.053126, 'lng' => -6.788346];
            }
        }

        $selectedCategory = $request->query('category');
        $selectedCity = $request->query('city');
        $selectedDistrict = $request->query('district');
        $selectedQuartier = $request->query('quartier');
        $selectedDate = $request->query('date');
        $sortBy = $request->query('sort', 'latest'); // 'latest', 'mostLiked', 'oldest'

        $reportsQuery = Report::with(['user', 'category', 'city', 'district', 'comments.user', 'likes'])
            ->withCount('likes');

        if ($selectedCategory) {
            $reportsQuery->where('category_id', $selectedCategory);
        }

        if ($selectedCity) {
            $reportsQuery->where('city_id', $selectedCity);
        }

        if ($selectedDistrict) {
            $reportsQuery->where('district_id', $selectedDistrict);
        }

        if ($selectedQuartier) {
            $reportsQuery->where('quartier_id', $selectedQuartier);
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

        $reports = $reportsQuery->paginate(12)->withQueryString();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('reports.partials.results', compact('reports'))->render(),
            ]);
        }

        return view('reports.index', compact(
            'reports',
            'categories',
            'cities',
            'districts',
            'quartiers',
            'cityCoordinates',
            'selectedCategory',
            'selectedCity',
            'selectedDistrict',
            'selectedQuartier',
            'selectedDate',
            'sortBy'
        ));
    }

    public function create()
    {
        // if (auth()->check() && auth()->user()->is_admin) {
        //     abort(403, 'Admins are not allowed to create reports.');
        // }

        $categories = Category::all();
        $cities = City::where('active', true)->get();
        $districts = District::all();
        $quartiers = Quartier::all();

        return view('reports.create', compact('categories', 'cities', 'districts', 'quartiers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'city_id'     => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id',
            'quartier_id' => 'nullable|exists:quartiers,id',
            'latitude'    => 'required|numeric|between:-90,90',
            'longitude'   => 'required|numeric|between:-180,180',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'image.image' => __('validation.image_required'),
            'image.mimes' => __('validation.image_types'),
            'image.max' => __('validation.image_size'),
            'latitude.required' => __('validation.latitude_required'),
            'latitude.numeric' => __('validation.latitude_numeric'),
            'latitude.between' => __('validation.latitude_range'),
            'longitude.required' => __('validation.longitude_required'),
            'longitude.numeric' => __('validation.longitude_numeric'),
            'longitude.between' => __('validation.longitude_range'),
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('reports', 'public');
            
            // Copy to public/storage for Windows compatibility
            $source = storage_path('app/public/' . $validated['image']);
            $destination = public_path('storage/' . $validated['image']);
            if (!file_exists(dirname($destination))) {
                mkdir(dirname($destination), 0755, true);
            }
            copy($source, $destination);
        }

        if (auth()->check() && auth()->user()->is_admin) {
            abort(403, 'Admins cannot store reports.');
        }

        $validated['status'] = 'OPEN';
        $validated['user_id'] = Auth::id();

        Report::create($validated);

        return redirect()->route('dashboard')->with('success', 'Report submitted successfully!');
    }

    public function show(Report $report)
    {
        $report->load(['comments.user', 'category', 'city', 'user', 'likes']);
        $report->loadCount('likes');

        return view('reports.show', compact('report'));
    }

    public function edit(Report $report)
    {
        // Ensure user owns the report
        if ($report->user_id !== Auth::id()) {
            abort(403);
        }

        $categories = Category::all();
        $cities = City::where('active', true)->get();
        $districts = District::all();

        return view('reports.edit', compact('report', 'categories', 'cities', 'districts'));
    }

    public function update(Request $request, Report $report)
    {
        // Ensure user owns the report
        if ($report->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'city_id'     => 'required|exists:cities,id',
            'district_id' => 'nullable|exists:districts,id',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'latitude'    => 'nullable|numeric|between:-90,90',
            'longitude'   => 'nullable|numeric|between:-180,180',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($report->image) {
                Storage::disk('public')->delete($report->image);
                $oldPath = public_path('storage/' . $report->image);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $validated['image'] = $request->file('image')->store('reports', 'public');
            
            // Copy to public/storage for Windows compatibility
            $source = storage_path('app/public/' . $validated['image']);
            $destination = public_path('storage/' . $validated['image']);
            if (!file_exists(dirname($destination))) {
                mkdir(dirname($destination), 0755, true);
            }
            copy($source, $destination);
        }

        $report->update($validated);

        return redirect()->route('dashboard')->with('success', 'Report updated successfully!');
    }

    public function destroy(Report $report)
    {
        // Ensure user owns the report
        if ($report->user_id !== Auth::id()) {
            abort(403);
        }

        // Delete image if exists
        if ($report->image) {
            Storage::disk('public')->delete($report->image);
            $path = public_path('storage/' . $report->image);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $report->delete();

        return redirect()->route('dashboard')->with('success', 'Report deleted successfully!');
    }

    public function getQuartierCoordinates(Quartier $quartier)
    {
        return response()->json([
            'latitude' => $quartier->latitude,
            'longitude' => $quartier->longitude,
            'name_fr' => $quartier->name_fr,
            'name_ar' => $quartier->name_ar,
        ]);
    }

    public function storeComment(Request $request, Report $report)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $comment = $report->comments()->create([
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        // Load the comment with user relationship for response
        $comment->load('user');

        // If it's an AJAX request, return JSON
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'comment' => [
                    'id' => $comment->id,
                    'user' => $comment->user->name,
                    'text' => $comment->comment,
                    'created_at' => $comment->created_at->diffForHumans(),
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Comment submitted successfully.');
    }
}
