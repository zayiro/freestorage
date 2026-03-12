<?php

// app/Http/Controllers/SaleItemController.php

namespace App\Http\Controllers;

use App\Models\SaleItem;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;

class SaleItemController extends Controller
{
    /**
     * Obtener todos los ítems de una venta
     */
    public function getBySale($saleId)
    {
        $sale = Sale::where('company_id', auth()->user()->company_id)
            ->findOrFail($saleId);

        $items = $sale->items()->with('product')->get();

        return response()->json([
            'success' => true,
            'items' => $items,
            'total' => $items->sum('subtotal'),
        ]);
    }

    /**
     * Agregar ítem a una venta
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Verificar stock
        if ($product->stock < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuficiente. Disponible: ' . $product->stock,
            ], 400);
        }

        $item = SaleItem::create([
            'sale_id' => $validated['sale_id'],
            'product_id' => $validated['product_id'],
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'quantity' => $validated['quantity'],
            'price' => $validated['price'],
            'subtotal' => $validated['price'] * $validated['quantity'],
            'discount' => 0,
            'tax' => 0,
            'status' => 'completed',
        ]);

        return response()->json([
            'success' => true,
            'item' => $item,
        ]);
    }

    /**
     * Actualizar ítem
     */
    public function update(Request $request, $id)
    {
        $item = SaleItem::findOrFail($id);

        $validated = $request->validate([
            'quantity' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:completed,pending,cancelled',
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'item' => $item,
        ]);
    }

    /**
     * Eliminar ítem
     */
    public function destroy($id)
    {
        $item = SaleItem::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ítem eliminado correctamente',
        ]);
    }

    /**
     * Obtener estadísticas de ítems
     */
    public function getStatistics()
    {
        $totalItems = SaleItem::count();
        $totalQuantity = SaleItem::sum('quantity');
        $totalSales = SaleItem::sum('subtotal');
        $totalDiscount = SaleItem::sum('discount');
        $totalTax = SaleItem::sum('tax');

        return response()->json([
            'success' => true,
            'statistics' => [
                'total_items' => $totalItems,
                'total_quantity' => $totalQuantity,
                'total_sales' => $totalSales,
                'total_discount' => $totalDiscount,
                'total_tax' => $totalTax,
            ],
        ]);
    }
}
