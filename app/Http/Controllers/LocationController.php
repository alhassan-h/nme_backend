<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return \App\Models\Location::ordered()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:locations',
            'is_active' => 'boolean',
        ]);

        $location = \App\Models\Location::create($request->all());
        return response()->json($location, 201);
    }

    public function update(Request $request, $id)
    {
        $location = \App\Models\Location::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:locations,name,' . $id,
            'is_active' => 'boolean',
        ]);

        $location->update($request->all());
        return response()->json($location);
    }

    public function destroy($id)
    {
        $location = \App\Models\Location::findOrFail($id);
        $location->delete();
        return response()->json(['message' => 'Location deleted successfully']);
    }
}
