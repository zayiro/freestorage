<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Solo usuarios logueados
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mostrar solo categorías de la compañía del usuario
        $categories = Category::where('company_id', auth()->user()->company_id)->get();
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'company_id' => auth()->user()->company_id, // Asocia al usuario logueado
        ]);

        return redirect()->route('categories.index')->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        // Verificar que la categoría pertenezca a la compañía del usuario
        if ($category->company_id !== auth()->user()->company_id) {
            abort(403);
        }
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        if ($category->company_id !== auth()->user()->company_id) {
            abort(403);
        }
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        if ($category->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($request->all());
        return redirect()->route('categories.index')->with('success', 'Categoría actualizada.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if ($category->company_id !== auth()->user()->company_id) {
            abort(403);
        }
       
        try {
            $category->delete();
            return redirect()->route('categories.index')->with('success', 'Categoría eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('categories.index')->with('error', 'No se puede eliminar la categoría porque tiene productos asociados.');
        }
    }
}
