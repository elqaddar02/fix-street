<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\RespondsToStatusChange;
use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class CityController extends Controller
{
    use RespondsToStatusChange;

    public function index()
    {
        $cities = City::orderBy('name')->paginate(10);

        return view('admin.cities.index', compact('cities'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:cities,name',
            'active' => 'required|boolean',
        ]);

        City::create($data);

        return redirect()->route('admin.cities.index')->with('success', 'Ville ajoutée.');
    }

    public function edit(City $city)
    {
        return view('admin.cities.edit', compact('city'));
    }

    public function update(Request $request, City $city)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:cities,name,' . $city->id,
            'active' => 'required|boolean',
        ]);

        $city->update($data);

        return redirect()->route('admin.cities.index')->with('success', 'Ville mise à jour.');
    }

    public function updateStatus(Request $request, City $city)
    {
        $request->validate(['active' => 'required|boolean']);

        $city->update(['active' => (bool) $request->active]);

        return $this->statusResponse(
            $request,
            $city->active ? 'Ville activée.' : 'Ville désactivée.',
            ['active' => $city->active]
        );
    }

    public function destroy(City $city)
    {
        $city->delete();

        return redirect()->route('admin.cities.index')->with('success', 'Ville supprimée.');
    }
}
