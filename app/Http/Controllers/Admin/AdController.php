<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdPlacement;
use App\Services\PublicUploadService;
use Illuminate\Http\Request;

class AdController extends Controller
{
    public function __construct(private readonly PublicUploadService $uploads)
    {
    }

    public function index()
    {
        $ads = Ad::with('placement')
            ->withCount(['impressions', 'clicks'])
            ->latest()
            ->paginate(15);

        return view('admin.ads.index', compact('ads'));
    }

    public function create()
    {
        $placements = AdPlacement::orderBy('name')->get();

        return view('admin.ads.create', compact('placements'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateAd($request);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['created_by'] = auth()->id();

        if ($request->hasFile('image')) {
            $validated['image'] = $this->uploads->store($request->file('image'), 'ads');
        }

        $ad = Ad::create($validated);
        logAdminAction('Create', 'Ad', $ad->id, "Created ad: {$ad->title}");

        return redirect()->route('admin.ads.index')->with('success', 'Publicité créée avec succès.');
    }

    public function edit(Ad $ad)
    {
        $placements = AdPlacement::orderBy('name')->get();

        return view('admin.ads.edit', compact('ad', 'placements'));
    }

    public function update(Request $request, Ad $ad)
    {
        $validated = $this->validateAd($request, $ad);
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $this->uploads->delete($ad->image);
            $validated['image'] = $this->uploads->store($request->file('image'), 'ads');
        }

        $ad->update($validated);
        logAdminAction('Update', 'Ad', $ad->id, "Updated ad: {$ad->title}");

        return redirect()->route('admin.ads.index')->with('success', 'Publicité mise à jour.');
    }

    public function updateStatus(Request $request, Ad $ad)
    {
        $request->validate(['is_active' => 'required|boolean']);

        $ad->update(['is_active' => $request->boolean('is_active')]);
        logAdminAction(
            $ad->is_active ? 'Activate' : 'Deactivate',
            'Ad',
            $ad->id,
            "Ad '{$ad->title}' set to " . ($ad->is_active ? 'active' : 'inactive')
        );

        return back()->with('success', 'Statut de la publicité mis à jour.');
    }

    public function destroy(Ad $ad)
    {
        $title = $ad->title;
        $this->uploads->delete($ad->image);
        $ad->delete();
        logAdminAction('Delete', 'Ad', $ad->id, "Deleted ad: {$title}");

        return redirect()->route('admin.ads.index')->with('success', 'Publicité supprimée.');
    }

    private function validateAd(Request $request, ?Ad $ad = null): array
    {
        return $request->validate([
            'ad_placement_id' => 'required|exists:ad_placements,id',
            'title' => 'required|string|max:255',
            'provider' => 'required|in:' . Ad::PROVIDER_HOUSE . ',' . Ad::PROVIDER_ADSENSE,
            'target_url' => 'required_if:provider,' . Ad::PROVIDER_HOUSE . '|nullable|url|max:2048',
            'external_slot_id' => 'required_if:provider,' . Ad::PROVIDER_ADSENSE . '|nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'weight' => 'nullable|integer|min:1|max:100',
        ]);
    }
}
