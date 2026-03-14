<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Presentation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Picqer\Barcode\BarcodeGeneratorSVG;
use App\Services\ProductLimitService;

class ProductController extends Controller
{
    protected $productLimitService;

    public function __construct(ProductLimitService $productLimitService)
    {
        $this->middleware('auth');
        $this->productLimitService = $productLimitService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mostrar solo productos de la compañía del usuario
        $products = Product::where('company_id', auth()->user()->company_id)->get();

        // Obtener estadísticas de uso
        $usageStats = $this->productLimitService->getUsageStatistics();

        return view('products.index', compact('products', 'usageStats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Verificar límite de productos
        $limitCheck = $this->productLimitService->canCreateProduct();        

        if (!$limitCheck['success']) {
            return redirect()->route('products.index')->with('error', $limitCheck['message']);
        }

        $categories = Category::where('company_id', auth()->user()->company_id)->get();
        $brands = Brand::where('company_id', auth()->user()->company_id)->get();

        return view('products.create', compact('categories', 'brands', 'limitCheck'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Verificar límite de productos
        $limitCheck = $this->productLimitService->canCreateProduct();

        if (!$limitCheck['success']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $limitCheck['message']);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2300', // Validación: imagen, tipos permitidos, max 2MB
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
            'company_id' => auth()->user()->company_id,
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
     * Display the specified resource.
     */
    public function showProduct(Product $product)
    {
        // Verificar que el producto pertenezca a la compañía del usuario
        if ($product->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        return view('products.showProduct', compact('product'));
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

        $barcodeImage = $product->barcode_image;

        if ($product->barcode && empty($product->barcode_image)) {            
            $barcodeImage = $this->generateBarcodeImageSVG($product);                              
        }
        
        $product->update([
            'name' => $request->name,
            'image' => $imagePath,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'barcode_image' => $barcodeImage,
        ]);
                
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
    private function generateBarcodeImageSVG($product)
    {
        // Generar imagen
        $generator = new BarcodeGeneratorSVG();
        $image = $generator->getBarcode($product->barcode, 'C128B', 3);
        
        // Guardar imagen
        $filename = "products/{$product->company_id}/{$product->barcode}.svg";
        Storage::disk('public')->put($filename, $image);
        
        // Formatear con ceros a la izquierda
        return $filename;
    }
    
    /**
     * Generar busqueda código de barras único para la empresa
     */
    public function searchByBarcode(Request $request)
    {
        $barcode = $request->barcode;
        
        $product = Product::where('barcode', $barcode)
                        ->orWhere('barcode', 'like', '%' . $barcode . '%')
                        ->first();
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Producto no encontrado'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'product' => $product
        ]);
    }

    public function whatsappShare($productId)
    {
        $product = Product::findOrFail($productId);
        
        $message = $this->buildShareMessage($product);
        $whatsappLink = $this->generateWhatsAppLink($message);
        
        return redirect()->away($whatsappLink);
    }
    
    private function buildShareMessage(Product $product)
    {
        $presentations = Presentation::where('id', $product->id)->orderBy('sales_price', 'asc')->first();
        $salesPrice = $presentations->sales_price;

        return "{$product->name}\n" .
               "Desde: $" . number_format($salesPrice, 2) . "\n" .
               "Producto ID: " . $product->id;
    }
    
    private function generateWhatsAppLink($phone, $message)
    {
        $encodedMessage = urlencode($message);
        return "https://wa.me/+{$phone}?text=" . $encodedMessage;
    }

    public function shareToClient(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'client_phone' => 'required|regex:/^57[0-9]{10}$/'
        ]);
        
        $product = Product::findOrFail($request->product_id);
        $message = $this->buildShareMessage($product);
        $whatsappUrl = $this->generateWhatsAppLink($request->client_phone, $message);
                
        // REDIRECT con WhatsApp URL
        return redirect()->away($whatsappUrl);
    }
}
