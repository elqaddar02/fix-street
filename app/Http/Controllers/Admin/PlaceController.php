<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\District;
use App\Models\Dossier;
use App\Models\Quartier;
use App\Services\LocationResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Districts and quartiers of a city.
 *
 * Their coordinates are not decoration: LocationResolver picks the nearest
 * quartier (then district) to a report's pin, so each point decides which
 * area a new report lands in. The forms set them on a map for that reason.
 */
class PlaceController extends Controller
{
    public function index(City $city)
    {
        $districts = $city->districts()
            ->withCount('reports')
            ->with(['quartiers' => fn ($query) => $query->withCount('reports')->orderBy('name_fr')])
            ->orderBy('name_fr')
            ->get();

        return view('admin.places.index', compact('city', 'districts'));
    }

    /* ---------------------------------------------------------------- Districts */

    public function createDistrict(City $city)
    {
        return view('admin.places.form', [
            'city' => $city,
            'place' => new District(),
            'kind' => 'district',
            'action' => route('admin.districts.store', $city),
            'neighbours' => $this->points($city->districts()->get(), 'lat', 'lng'),
            'center' => $this->cityCenter($city),
        ]);
    }

    public function storeDistrict(Request $request, City $city)
    {
        $data = $this->validated($request, nameArRequired: true);

        $district = $city->districts()->create([
            'name_fr' => $data['name_fr'],
            'name_ar' => $data['name_ar'],
            'lat' => $data['latitude'] ?? null,
            'lng' => $data['longitude'] ?? null,
            'slug' => $this->uniqueSlug(District::class, $data['name_fr']),
        ]);

        logAdminAction('Create', 'District', $district->id, "Created district: {$district->name_fr} ({$city->name})");

        return redirect()->route('admin.cities.places', $city)->with('success', __('admin.flash_district_created'));
    }

    public function editDistrict(District $district)
    {
        $city = $district->city;

        return view('admin.places.form', [
            'city' => $city,
            'place' => $district,
            'kind' => 'district',
            'action' => route('admin.districts.update', $district),
            'neighbours' => $this->points($city->districts()->whereKeyNot($district->id)->get(), 'lat', 'lng'),
            'center' => $this->cityCenter($city),
        ]);
    }

    public function updateDistrict(Request $request, District $district)
    {
        $data = $this->validated($request, nameArRequired: true);

        $district->update([
            'name_fr' => $data['name_fr'],
            'name_ar' => $data['name_ar'],
            'lat' => $data['latitude'] ?? null,
            'lng' => $data['longitude'] ?? null,
        ]);

        logAdminAction('Update', 'District', $district->id, "Updated district: {$district->name_fr}");

        return redirect()->route('admin.cities.places', $district->city_id)->with('success', __('admin.flash_district_updated'));
    }

    public function destroyDistrict(District $district)
    {
        $reports = $district->reports()->count();
        $dossiers = Dossier::where('district_id', $district->id)->count();
        $quartiers = $district->quartiers()->count();

        // Deleting would silently strip the area from existing reports and case files.
        if ($reports > 0 || $dossiers > 0 || $quartiers > 0) {
            return back()->with('error', __('admin.flash_district_in_use', [
                'name' => $district->name_fr,
                'reports' => $reports,
                'quartiers' => $quartiers,
            ]));
        }

        $cityId = $district->city_id;
        $district->delete();

        logAdminAction('Delete', 'District', $district->id, "Deleted district: {$district->name_fr}");

        return redirect()->route('admin.cities.places', $cityId)->with('success', __('admin.flash_district_deleted'));
    }

    /* ---------------------------------------------------------------- Quartiers */

    public function createQuartier(District $district)
    {
        return view('admin.places.form', [
            'city' => $district->city,
            'district' => $district,
            'place' => new Quartier(),
            'kind' => 'quartier',
            'action' => route('admin.quartiers.store', $district),
            'neighbours' => $this->siblingQuartiers($district),
            'center' => $this->districtCenter($district),
        ]);
    }

    public function storeQuartier(Request $request, District $district)
    {
        $data = $this->validated($request, nameArRequired: false);

        $quartier = $district->quartiers()->create([
            'name_fr' => $data['name_fr'],
            'name_ar' => $data['name_ar'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'slug' => $this->uniqueSlug(Quartier::class, $data['name_fr']),
        ]);

        logAdminAction('Create', 'Quartier', $quartier->id, "Created quartier: {$quartier->name_fr} ({$district->name_fr})");

        return redirect()->route('admin.cities.places', $district->city_id)->with('success', __('admin.flash_quartier_created'));
    }

    public function editQuartier(Quartier $quartier)
    {
        $district = $quartier->district;

        return view('admin.places.form', [
            'city' => $district->city,
            'district' => $district,
            'place' => $quartier,
            'kind' => 'quartier',
            'action' => route('admin.quartiers.update', $quartier),
            'neighbours' => $this->siblingQuartiers($district, except: $quartier),
            'center' => $this->districtCenter($district),
        ]);
    }

    public function updateQuartier(Request $request, Quartier $quartier)
    {
        $data = $this->validated($request, nameArRequired: false);

        $quartier->update([
            'name_fr' => $data['name_fr'],
            'name_ar' => $data['name_ar'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
        ]);

        logAdminAction('Update', 'Quartier', $quartier->id, "Updated quartier: {$quartier->name_fr}");

        return redirect()->route('admin.cities.places', $quartier->district->city_id)->with('success', __('admin.flash_quartier_updated'));
    }

    public function destroyQuartier(Quartier $quartier)
    {
        $reports = $quartier->reports()->count();
        $dossiers = Dossier::where('quartier_id', $quartier->id)->count();

        if ($reports > 0 || $dossiers > 0) {
            return back()->with('error', __('admin.flash_quartier_in_use', ['name' => $quartier->name_fr, 'count' => $reports + $dossiers]));
        }

        $cityId = $quartier->district->city_id;
        $quartier->delete();

        logAdminAction('Delete', 'Quartier', $quartier->id, "Deleted quartier: {$quartier->name_fr}");

        return redirect()->route('admin.cities.places', $cityId)->with('success', __('admin.flash_quartier_deleted'));
    }

    /* ---------------------------------------------------------------- Helpers */

    /**
     * @return array{name_fr: string, name_ar: string|null, latitude: float|null, longitude: float|null}
     */
    private function validated(Request $request, bool $nameArRequired): array
    {
        // Same bounding box LocationResolver accepts: a point outside it could never be matched.
        return $request->validate([
            'name_fr' => ['required', 'string', 'max:255'],
            'name_ar' => [$nameArRequired ? 'required' : 'nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'required_with:longitude', 'numeric', 'between:' . LocationResolver::SUPPORTED_AREA[0] . ',' . LocationResolver::SUPPORTED_AREA[1]],
            'longitude' => ['nullable', 'required_with:latitude', 'numeric', 'between:' . LocationResolver::SUPPORTED_AREA[2] . ',' . LocationResolver::SUPPORTED_AREA[3]],
        ]);
    }

    /** @param class-string<Model> $model */
    private function uniqueSlug(string $model, string $name): string
    {
        $base = Str::slug($name) ?: 'place';
        $slug = $base;

        for ($i = 2; $model::where('slug', $slug)->exists(); $i++) {
            $slug = "{$base}-{$i}";
        }

        return $slug;
    }

    /** Neighbouring points shown faintly on the map, so the admin sees how the area is split. */
    private function points($places, string $latKey, string $lngKey): array
    {
        return $places
            ->filter(fn ($place) => $place->{$latKey} !== null && $place->{$lngKey} !== null)
            ->map(fn ($place) => [
                'name' => $place->name_fr,
                'lat' => (float) $place->{$latKey},
                'lng' => (float) $place->{$lngKey},
            ])
            ->values()
            ->all();
    }

    private function siblingQuartiers(District $district, ?Quartier $except = null): array
    {
        // Every quartier of the city, not just this district: the resolver competes across all of them.
        $quartiers = Quartier::whereIn('district_id', $district->city->districts()->pluck('id'))
            ->when($except, fn ($query) => $query->whereKeyNot($except->id))
            ->get();

        return $this->points($quartiers, 'latitude', 'longitude');
    }

    private function cityCenter(?City $city): ?array
    {
        return $city?->latitude && $city?->longitude
            ? [(float) $city->latitude, (float) $city->longitude]
            : null;
    }

    private function districtCenter(District $district): ?array
    {
        return $district->lat && $district->lng
            ? [(float) $district->lat, (float) $district->lng]
            : $this->cityCenter($district->city);
    }
}
