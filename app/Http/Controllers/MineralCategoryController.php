<?php

namespace App\Http\Controllers;

use App\Services\MineralCategoryService;
use Illuminate\Http\Request;

class MineralCategoryController extends Controller
{
    protected $mineralCategoryService;

    public function __construct(MineralCategoryService $mineralCategoryService)
    {
        $this->mineralCategoryService = $mineralCategoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = $this->mineralCategoryService->getCategoriesWithCounts();

        return response()->json($categories);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
