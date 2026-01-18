<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class LocationController extends Controller
{
    /**
     * Display a listing of the locations.
     */
    public function index(): View
    {
        $locations = Location::paginate(10);
        return view('locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new location.
     */
    public function create(): View
    {
        return view('locations.create');
    }

    /**
     * Store a newly created location in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:locations|max:50',
            'name' => 'required|string|max:255',
            'zone' => 'nullable|string|max:100',
            'aisle' => 'nullable|string|max:100',
            'rack' => 'nullable|string|max:100',
        ]);

        Location::create($validated);

        return redirect()->route('locations.index')->with('success', 'Lokasi stok berhasil ditambahkan');
    }

    /**
     * Display the specified location.
     */
    public function show(Location $location): View
    {
        $stocks = $location->stocks()->with('item')->paginate(10);
        return view('locations.show', compact('location', 'stocks'));
    }

    /**
     * Show the form for editing the specified location.
     */
    public function edit(Location $location): View
    {
        return view('locations.edit', compact('location'));
    }

    /**
     * Update the specified location in storage.
     */
    public function update(Request $request, Location $location): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:locations,code,' . $location->id . '|max:50',
            'name' => 'required|string|max:255',
            'zone' => 'nullable|string|max:100',
            'aisle' => 'nullable|string|max:100',
            'rack' => 'nullable|string|max:100',
        ]);

        $location->update($validated);

        return redirect()->route('locations.index')->with('success', 'Lokasi stok berhasil diupdate');
    }

    /**
     * Remove the specified location from storage.
     */
    public function destroy(Location $location): RedirectResponse
    {
        $location->delete();
        return redirect()->route('locations.index')->with('success', 'Lokasi stok berhasil dihapus');
    }
}
