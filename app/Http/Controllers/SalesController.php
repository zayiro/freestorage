<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Presentation;

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
        return view('sales.receipt', compact('sale', 'items'));
    }
    
    // Listar ventas (opcional, para admin)
    public function index()
    {
        $sales = Sale::orderBy('created_at', 'desc')->paginate(10);
        return view('sales.index', compact('sales'));
    }
}