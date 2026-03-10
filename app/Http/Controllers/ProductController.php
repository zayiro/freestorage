<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorPNG;

class ProductController extends Controller
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
        // Mostrar solo productos de la compañía del usuario
        $products = Product::where('company_id', auth()->user()->company_id)->get();
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('company_id', auth()->user()->company_id)->get();
        $brands = Brand::where('company_id', auth()->user()->company_id)->get();

        return view('products.create', compact('categories', 'brands'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validación: imagen, tipos permitidos, max 2MB
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id', // Valida que exista en la tabla categories            
            'brand_id' => 'nullable|exists:brands,id', // Valida que exista en la tabla brands
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $companyId = auth()->user()->company_id;
            $imagePath = $request->file('image')->store("products/{$companyId}", 'public'); // Guarda en products/{company_id}/
        }

        Product::create([
            'name' => $request->name,
            'image' => $imagePath,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'company_id' => auth()->user()->company_id, // Asigna automáticamente
        ]);        

        return redirect()->route('products.index')->with('success', 'Producto creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Verificar que el producto pertenezca a la compañía del usuario
        if ($product->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {        
        if ($product->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $categories = Category::where('company_id', auth()->user()->company_id)->get();
        $brands = Brand::where('company_id', auth()->user()->company_id)->get();

        return view('products.edit', compact('product', 'categories', 'brands'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        if ($product->company_id !== auth()->user()->company_id) {
            abort(403);
        }
        
        $request->validate([
            'name' => 'required|string|max:255', 
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',           
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'description' => 'nullable|string',            
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            $companyId = auth()->user()->company_id;
            // Elimina la imagen anterior si existe
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store("products/{$companyId}", 'public');
        }

        $product->update([
            'name' => $request->name,
            'image' => $imagePath,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
        ]);

        if (empty($product->barcode)) {
            //validar si quedo bien con las funciones privadas al final comparar
            //con el modelo de product
            $barcode = $this->generateUniqueBarcode($product->company_id);
            $product->update(['barcode' => $barcode]);
            
        }
        
        return redirect()->route('products.index')->with('success', 'Producto actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->company_id !== auth()->user()->company_id) {
            abort(403);
        }
        
        try {
            // Elimina la imagen
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $product->delete();
            
            return redirect()->route('products.index')->with('success', 'Producto eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('products.index')->with('error', 'No se puede eliminar el producto porque tiene presentaciones o ventas asociadas.');
        }
    }

    public function sell(Request $request, Product $product)
    {
        $quantity = $request->quantity;
        if ($product->stock_quantity >= $quantity) {
            $product->stock_quantity -= $quantity;
            $product->save();
            Sale::create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'total_price' => $product->price * $quantity,
                'sale_date' => now(),
            ]);
            return redirect()->back()->with('success', 'Sale recorded!');
        }
        return redirect()->back()->with('error', 'Insufficient stock!');
    }

    public function getPresentations(Request $request, $productId)
    {
        $product = Product::where('company_id', auth()->user()->company_id)
            ->with(['presentations'])
            ->find($productId);
        
        if (!$product) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }
        
        $presentations = $product->presentations->map(function ($presentation) {
            return [
                'id' => $presentation->id,
                'presentation_name' => $presentation->presentation,
                'stock' => $presentation->stock,
                'price' => $presentation->sales_price,
                'unit' => $presentation->unit,
            ];
        });
        
        return response()->json([            
            'presentations' => $presentations,
        ]);
    }

    /**
     * Generar código de barras único para la empresa
     */
    private function generateUniqueBarcode($companyId)
    {
        // Obtener el último producto de la empresa
        $lastProduct = Product::where('company_id', $companyId)
            ->orderBy('id', 'desc')
            ->first();
        
        // Extraer número del último código (ej: COMP-00001 -> 1)
        $nextNumber = $lastProduct ? (int)substr($lastProduct->barcode, 5) + 1 : 1;
        
        // Formatear con ceros a la izquierda
        return 'COMP-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generar y guardar imagen del código de barras
     */
    private function saveBarcodeImage($barcode, $companyId)
    {
        $generator = new BarcodeGeneratorPNG();
        $image = $generator->getBarcode($barcode, 'CODE128');
        
        // Crear carpeta de la compañía si no existe
        $directory = "products/{$companyId}";
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        // Guardar imagen
        $filename = "products/{$companyId}/{$barcode}.png";
        Storage::disk('public')->put($filename, $image);

        return $filename;
    }
}
