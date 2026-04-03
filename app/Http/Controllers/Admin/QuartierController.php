<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Quartier;
use Illuminate\Http\Request;

class QuartierController extends Controller
{
    public function index()
    {
        $quartiers = Quartier::with('city')->orderBy('name')->paginate(10);

        return view('admin.quartiers.index', compact('quartiers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'active' => 'required|boolean',
        ]);

        // Check if quartier name is unique within the city
        $exists = Quartier::where('name', $data['name'])
            ->where('city_id', $data['city_id'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'Ce quartier existe déjà dans cette ville.'])->withInput();
        }

        Quartier::create($data);

        return redirect()->route('admin.quartiers.index')->with('success', 'Quartier ajouté.');
    }

    public function edit(Quartier $quartier)
    {
        $cities = City::where('active', true)->orderBy('name')->get();

        return view('admin.quartiers.edit', compact('quartier', 'cities'));
    }

    public function update(Request $request, Quartier $quartier)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'active' => 'required|boolean',
        ]);

        // Check if quartier name is unique within the city (excluding current)
        $exists = Quartier::where('name', $data['name'])
            ->where('city_id', $data['city_id'])
            ->where('id', '!=', $quartier->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['name' => 'Ce quartier existe déjà dans cette ville.'])->withInput();
        }

        $quartier->update($data);

        return redirect()->route('admin.quartiers.index')->with('success', 'Quartier mis à jour.');
    }

    public function destroy(Quartier $quartier)
    {
        $quartier->delete();

        return redirect()->route('admin.quartiers.index')->with('success', 'Quartier supprimé.');
    }
}