<?php

namespace App\Http\Controllers;

use App\Models\InventoryPresentation;
use App\Models\Presentation;
use App\Models\Product;
use Illuminate\Http\Request;

class PresentationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Solo usuarios logueados
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        //dd($request->all());
        $query = Presentation::with(['product', 'inventory']);

        // Filtrar por producto si se proporciona
        if ($request->has('product_id') && $request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        // Filtrar por categoría si se proporciona
        if ($request->has('category_id') && $request->category_id) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        // Filtrar por stock bajo
        if ($request->has('minimun_quantity') && $request->minimun_quantity) {
            $query->whereHas('inventory', function ($q) {
                $q->whereRaw('current_quantity <= minimun_quantity');
            });
        }

        $presentations = $query->orderBy('product_id', 'asc')
                                ->paginate(15);

        $products = Product::all();

        return view('presentations.index', compact('presentations', 'products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request) {
        $products = Product::all();
        $productId = $request->product_id;
        $productoSeleccionado = $productId ? Product::findOrFail($productId) : null;

        return view('presentations.create', compact('products', 'productoSeleccionado'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'presentation' => 'required|string|max:255',
            'purchase_price' => 'required|numeric|min:0',
            'sales_price' => 'required|numeric|min:0',
            'unit' => 'required|string|max:50',
            'current_quantity' => 'required|integer|min:1',            
            'minimum_quantity' => 'nullable|integer|min:0',
            'location' => 'nullable|string|max:100',
        ]);

        $presentation = Presentation::create([
            'product_id' => $request->product_id,
            'presentation' => $request->presentation,
            'purchase_price' => $request->purchase_price,
            'sales_price' => $request->sales_price,
            'unit' => $request->unit,
            'stock' => $request->current_quantity,            
            'active' => true,
        ]);

        
        // Crear inventario inicial para la presentación
        InventoryPresentation::create([
            'presentation_id' => $presentation->id,
            'current_quantity' => $request->current_quantity ?? 0,
            'minimum_quantity' => $request->minimum_quantity ?? 0,
            'location' => $request->location ?? 'General',
        ]);

        return redirect()->route('products.show', $request->product_id)->with('success', 'Presentación creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Presentation $presentation)
    {
        if ($presentation->company_id !== auth()->user()->company_id) {
            abort(403);
        }
        return view('presentations.show', compact('presentation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Presentation $presentation)
    {
        /*if ($presentation->company_id !== auth()->user()->company_id) {
            abort(403);
        }*/

        return view('presentations.edit', compact('presentation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Presentation $presentation)
    {
        $productFind = Product::where('company_id', auth()->user()->company_id)->find($presentation->product_id);

        if (!$productFind) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'active' => 'boolean',
            'purchase_price' => 'required|numeric|min:0',
            'sales_price' => 'required|numeric|min:0',            
            'current_quantity' => 'required|integer|min:1',            
            'minimum_quantity' => 'nullable|integer|min:0',
            'location' => 'nullable|string|max:255',
            
        ]);

        $presentation->update($request->all());
        return redirect()->route('presentations.index')->with('success', 'Presentación actualizada.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Presentation $presentation)
    {
        if ($presentation->company_id !== auth()->user()->company_id) {
            abort(403);
        }
        $presentation->delete();
        return redirect()->route('presentations.index')->with('success', 'Presentación eliminada.');
    }
}
