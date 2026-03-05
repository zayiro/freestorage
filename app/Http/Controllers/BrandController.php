<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $brands = Brand::where('company_id', auth()->user()->company_id)->get();
        return view('brands.index', compact('brands'));
    }

    public function create()
    {
        return view('brands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Brand::create([
            'name' => $request->name,
            'description' => $request->description,
            'company_id' => auth()->user()->company_id,
        ]);

        return redirect()->route('brands.index')->with('success', 'Marca creada exitosamente.');
    }

    public function show(Brand $brand)
    {
        if ($brand->company_id !== auth()->user()->company_id) {
            abort(403);
        }
        return view('brands.show', compact('brand'));
    }

    public function edit(Brand $brand)
    {
        if ($brand->company_id !== auth()->user()->company_id) {
            abort(403);
        }
        return view('brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        if ($brand->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $brand->update($request->all());
        return redirect()->route('brands.index')->with('success', 'Marca actualizada.');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->company_id !== auth()->user()->company_id) {
            abort(403);
        }
        $brand->delete();
        return redirect()->route('brands.index')->with('success', 'Marca eliminada.');
    }
}