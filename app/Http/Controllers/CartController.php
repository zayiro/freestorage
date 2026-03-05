<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Presentation;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'presentation_id' => 'required|exists:presentations,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $presentation = Presentation::find($request->presentation_id);
        // Cargar presentación CON producto e inventario
        $presentation = Presentation::with(['product', 'inventory'])->findOrFail($request->presentation_id);
        
        // Verificar stock
        if ($presentation->stock < $request->quantity) {
            return response()->json(['error' => 'Stock insuficiente'], 400);
        }

        // Usar sesión para carrito (array de items)
        $cart = session()->get('cart', []);
        $key = $presentation->id;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $request->quantity;
        } else {
            $cart[$key] = [
                'product_id' => $presentation->product->id,
                'presentation_id' => $presentation->id,
                'product_name' => $presentation->product->name,
                'presentation' => $presentation->presentation,
                'sales_price' => $presentation->sales_price,
                'quantity' => $request->quantity,
            ];
        }

        // Guardar en sesión
        session()->put('cart', $cart);

        // Calcular total del carrito
        $total = array_sum(array_map(fn($item) => $item['sales_price'] * $item['quantity'], $cart));

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado al carrito',
            'cart_count' => count($cart),
            'cart_total' => number_format($total, 2),
        ]);
    }

    public function show()
    {
        $cart = session()->get('cart', []);        
        $total = 0;

        foreach ($cart as $item) {
            $total += $item['sales_price'] * $item['quantity'];
        }

        return view('cart.show', compact('cart', 'total'));
    }

    // Obtener contenido del carrito (JSON)
    public function getCart() {
        $cart = Session::get('cart', []);
        $total = 0;
        $totalItems = 0;

        foreach ($cart as $item) {
            $total += $item['sales_price'] * $item['quantity'];
            $totalItems += $item['quantity'];
        }

        return response()->json([
            'success' => true,
            'message' => 'Carrito obtenido correctamente',
            'cart' => $cart,
            'cart_count' => count($cart),
            'cart_total' => number_format($total, 2),
        ]);
    }

    // Actualizar cantidad en carrito
    public function update(Request $request) {
        $request->validate([
            'presentation_id' => 'required|exists:presentations,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = session()->get('cart', []);
        $presentationId = $request->presentation_id;

        if ($request->quantity == 0) {
            // Eliminar si cantidad es 0
            unset($cart[$presentationId]);
        } else {
            // Verificar stock
            $presentation = Presentation::with('inventory')->findOrFail($presentationId);
            $stockActual = $presentation->stock ?? 0;
            
            if ($request->quantity > $stockActual) {
                return redirect()->back()->with('error', 'Stock insuficiente. Máximo: ' . $stockActual);
            }

            if (isset($cart[$presentationId])) {
                $cart[$presentationId]['quantity'] = $request->quantity;
            }
        }

        session()->put('cart', $cart);
        return redirect()->route('cart.show')->with('success', 'Cantidad actualizada en el carrito.');
    }

    // Eliminar producto del carrito
    public function destroy($presentationId) {
        
        $item = session()->get('cart.' . $presentationId);
        if ($item) {
            $cart = session()->get('cart', []);
            unset($cart[$presentationId]);
            session()->put('cart', $cart);

            $cart = session()->get('cart', []);
            $total = array_sum(array_map(fn($item) => $item['sales_price'] * $item['quantity'], $cart));
    
            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado del carrito',
                'cart_count' => count($cart),
                'cart_total' => number_format($total, 2)                
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Producto no encontrado en el carrito'
        ], 404);
    }

    // Vaciar carrito
    public function clear() {
        session()->forget('cart');
        
        return redirect()->route('cart.show')->with('success', 'Carrito vaciado.');
    }

    public function addForm() {
        $presentations = Presentation::with(['product', 'inventory'])->get();
        return view('cart.add', compact('presentations'));
    }
}