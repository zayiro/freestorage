<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use App\Models\Presentation;
use App\Models\Company;

class SalesController extends Controller
{
    // Método para procesar la venta desde el carrito
    public function store(Request $request)
{
    $request->validate([
        'company_id' => 'nullable|exists:companies,id', // Validar compañía opcional
    ]);
    
    $cart = session()->get('cart', []);
    
    if (empty($cart)) {
        return redirect()->back()->with('error', 'El carrito está vacío.');
    }
    
    $total = 0;
    $items = [];
    
    // Verificar stock y calcular total (igual que antes)
    foreach ($cart as $item) {
        $presentation = Presentation::find($item['id']);
        if (!$presentation || $presentation->stock_quantity < $item['quantity']) {
            return redirect()->back()->with('error', 'Stock insuficiente para ' . $item['presentation']);
        }
        $total += $item['price'] * $item['quantity'];
        $items[] = $item;
    }
    
    // Reducir stock (igual que antes)
    foreach ($cart as $item) {
        $presentation = Presentation::find($item['id']);
        $presentation->stock_quantity -= $item['quantity'];
        $presentation->save();
    }
    
    // Crear venta con compañía opcional
    Sale::create([
        'invoice_number' => Sale::generateInvoiceNumber(),
        'user_id' => auth()->id(),
        'total_amount' => $total,
        'items' => json_encode($items),
        'company_id' => auth()->user()->company_id,
    ]);
    
    session()->forget('cart');
    
    return redirect()->route('sales.receipt', ['sale' => Sale::latest()->first()])->with('success', 'Venta procesada exitosamente.');
}
    
    // Mostrar recibo de venta
    public function receipt(Sale $sale)
    {
        $items = json_decode($sale->items, true);        
        $company = Company::findOrFail($sale->company_id);

        $total_items = collect($items)->sum('quantity');
        
        return view('sales.receipt', compact('sale', 'items', 'total_items', 'company'));
    }
    
    // Listar ventas (opcional, para admin)
    public function index()
    {
        $sales = Sale::orderBy('created_at', 'desc')->paginate(10);
        return view('sales.index', compact('sales'));
    }

    public function checkout(Request $request)
    {        
        // 1. Obtener carrito de compras
        $cart = session()->get('cart', []);
        
        // 2. Validar que el carrito no esté vacío
        if (empty($cart)) {
            return redirect()->route('sales.cart')
                ->with('error', 'El carrito está vacío');
        }

        // 3. Validar datos del formulario
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_id' => 'nullable|string|max:20',            
            'customer_address' => 'nullable|string|max:255',
            'discount' => 'nullable|numeric|min:0|max:100',
            'delivery_fee' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,card,transfer',
            'notes' => 'nullable|string|max:500',            
        ]);

        try {
            // 4. Iniciar transacción de base de datos
            DB::beginTransaction();

            // 5. Calcular SUBTOTAL (suma de productos)
            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += $item['sales_price'] * $item['quantity'];
            }

            // 6. Calcular DESCUENTO sobre SUBTOTAL
            $discountPercent = 0;
            $discountAmount = 0;
            if ($validated['discount'] > 0) {
                $discountPercent = $validated['discount'];
                $discountAmount = $subtotal * ($validated['discount'] / 100);
            }                       

            // 7. Calcular IMPUESTOS (19%) sobre (Subtotal - Descuento)
            $taxPercent = 19;
            $taxableAmount = $subtotal - $discountAmount;
            $taxAmount = $taxableAmount * ($taxPercent / 100);

            $deliveryFee = $validated['delivery_fee'] ?? 0;

            // 8. Calcular TOTAL FINAL
            $finalTotal = $taxableAmount + $deliveryFee;

            // 9. Verificar stock disponible
            foreach ($cart as $item) {
                $presentation = Presentation::find($item['presentation_id']);
                
                // Verificar stock
                if ($presentation->stock < $item['quantity']) {
                    return response()->json(['error' => 'Stock insuficiente'], 400);
                }
            }

            // 10. Crear registro en tabla sales
            $sale = Sale::create([
                'invoice_number' => Sale::generateInvoiceNumber(),
                'user_id' => auth()->id(),
                'items' => json_encode($cart),
                'customer_name' => $validated['customer_name'],                
                'customer_phone' => $validated['customer_phone'],
                'customer_id' => $validated['customer_id'],
                'customer_address' => $validated['customer_address'],                
                'total_price' => $finalTotal,
                'discount' => $discountAmount,
                'discount_percentage' => $discountPercent,
                'tax' => $taxAmount,
                'delivery_fee' => $validated['delivery_fee'],
                'payment_method' => $validated['payment_method'],
                'company_id' => auth()->user()->company_id,
                'status' => 'completed',
                'notes' => $validated['notes'],                
            ]);

            // 11. Decrementar stock
            foreach ($cart as $item) {                
                $presentation = Presentation::find($item['presentation_id']);
                if ($presentation) {
                    $presentation->decrement('stock', $item['quantity']);
                }
            }

            // 12. Confirmar transacción
            DB::commit();

            // 13. Limpiar carrito de compras
            session()->forget('cart');

            // 14. Redirigir con mensaje de éxito
            return redirect()->route('sales.receipt', ['sale' => $sale])
                ->with('success', 'Venta realizada con éxito. Factura: ' . $sale->invoice_number);

        } catch (\Exception $e) {
            // 15. Si hay error, revertir transacción
            DB::rollBack();

            return redirect()->route('cart.show')
                ->with('error', 'Error al realizar la venta: ' . $e->getMessage());
        }
    }

    public function find(Request $request)
    {
        $request->validate([
            'invoice_number' => 'required|string|max:50',
        ]);

        $invoice_number = $request->invoice_number;

        if (!$invoice_number) {
            return back()->with('error', 'Por favor ingresa un número de factura');
        }

        // Buscar la factura
        $sale = Sale::where('invoice_number', $invoice_number)->first();

        if (!$sale) {
            return back()->with('error', 'Factura no encontrada');
        }

        $items = json_decode($sale->items, true);        
        $company = Company::findOrFail($sale->company_id);
        
        return view('sales.receipt', compact('sale', 'items', 'company'));        
    }
}