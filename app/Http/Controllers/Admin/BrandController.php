<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return Inertia::render('admin/brands/Index', [
            'brands' => $brands,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/brands/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'website' => 'nullable|url',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('brands', 'public');
        }

        Brand::create($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        $brand->load('products');
        
        return Inertia::render('admin/brands/Show', [
            'brand' => $brand,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        return Inertia::render('admin/brands/Edit', [
            'brand' => $brand,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'website' => 'nullable|url',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($brand->logo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($brand->logo);
            }
            $validated['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $brand->update($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        if ($brand->products()->count() > 0) {
            return back()->with('error', 'Cannot delete brand with associated products.');
        }

        $brand->delete();

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand deleted successfully.');
    }
}
