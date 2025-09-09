<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return \App\Models\Unit::ordered()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:units',
            'is_active' => 'boolean',
        ]);

        $unit = \App\Models\Unit::create($request->all());
        return response()->json($unit, 201);
    }

    public function update(Request $request, $id)
    {
        $unit = \App\Models\Unit::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:units,name,' . $id,
            'is_active' => 'boolean',
        ]);

        $unit->update($request->all());
        return response()->json($unit);
    }

    public function destroy($id)
    {
        $unit = \App\Models\Unit::findOrFail($id);
        $unit->delete();
        return response()->json(['message' => 'Unit deleted successfully']);
    }
}
