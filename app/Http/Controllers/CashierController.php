<?php

// app/Http/Controllers/CashierController.php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\CashMovement;
use App\Services\CashierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    protected $cashierService;

    public function __construct(CashierService $cashierService)
    {
        $this->cashierService = $cashierService;
    }

    /**
     * Mostrar la interfaz de la caja
     */
    public function index()
    {
        $products = Product::where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->get();

        $todaySales = Sale::where('company_id', auth()->user()->company_id)
            ->whereDate('created_at', today())
            ->where('status', 'completed')
            ->sum('total_price');

        return view('cashier.index', compact('products', 'todaySales'));
    }

    /**
     * Agregar producto al carrito de la caja
     */
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Verificar stock
        if ($product->stock < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Stock insuficiente. Disponible: ' . $product->stock
            ], 400);
        }

        // Agregar al carrito (session)
        $cart = session()->get('cashier_cart', []);
        $cartKey = $validated['product_id'];

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $validated['quantity'];
        } else {
            $cart[$cartKey] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'quantity' => $validated['quantity'],
                'price' => $product->price,
                'subtotal' => $product->price * $validated['quantity'],
            ];
        }

        session()->put('cashier_cart', $cart);

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'total' => $this->calculateCartTotal($cart),
        ]);
    }

    /**
     * Calcular total del carrito
     */
    private function calculateCartTotal($cart)
    {
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['subtotal'];
        }
        return $subtotal;
    }

    /**
     * Procesar la venta
     */
    public function processSale(Request $request)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,card,transfer',
            'paid_amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'tax' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $cart = session()->get('cashier_cart', []);

            if (empty($cart)) {
                throw new \Exception('El carrito está vacío');
            }

            // Calcular totales
            $subtotal = $this->calculateCartTotal($cart);
            $discount = $validated['discount'] ?? 0;
            $tax = $validated['tax'] ?? 0;
            $totalPrice = $subtotal + $tax - $discount;
            $paidAmount = $validated['paid_amount'];
            $change = $paidAmount - $totalPrice;

            // Validar pago
            if ($change < 0) {
                throw new \Exception('El monto pagado es insuficiente');
            }

            // Crear venta
            $sale = Sale::create([
                'user_id' => auth()->id(),
                'company_id' => auth()->user()->company_id,
                'cashier_id' => auth()->id(),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'total_price' => $totalPrice,
                'paid_amount' => $paidAmount,
                'change' => $change,
                'payment_method' => $validated['payment_method'],
                'status' => 'completed',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Guardar ítems y actualizar stock
            foreach ($cart as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Actualizar stock
                $product->decrement('stock', $item['quantity']);

                // Guardar ítem de venta
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'product_sku' => $item['product_sku'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'discount' => 0,
                ]);
            }

            // Registrar movimiento de caja
            CashMovement::create([
                'user_id' => auth()->id(),
                'company_id' => auth()->user()->company_id,
                'sale_id' => $sale->id,
                'type' => 'income',
                'amount' => $paidAmount,
                'description' => 'Venta: ' . $sale->sale_number,
                'reference' => $sale->sale_number,
            ]);

            // Limpiar carrito
            session()->forget('cashier_cart');

            DB::commit();

            return redirect()->route('cashier.receipt', $sale->id)
                ->with('success', 'Venta realizada: ' . $sale->sale_number);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Procesar venta vía AJAX
     */
    public function processSaleAjax(Request $request)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,card,transfer',
            'paid_amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
            'tax' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $result = $this->cashierService->processSale($validated);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'sale_id' => $result['sale']->id,
                    'sale_number' => $result['sale']->sale_number,
                    'message' => $result['message'],
                    'redirect' => route('cashier.receipt', $result['sale']->id),
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                ], 400);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mostrar recibo de venta
     */
    public function receipt($id)
    {
        $sale = Sale::where('company_id', auth()->user()->company_id)
            ->findOrFail($id);

        return view('cashier.receipt', compact('sale'));
    }

    /**
     * Historial de ventas
     */
    public function history(Request $request)
    {
        $query = Sale::where('company_id', auth()->user()->company_id)
            ->with('items');

        // Filtros
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('sale_number', 'like', '%' . $request->search . '%');
        }

        $sales = $query->orderBy('created_at', 'desc')->paginate(20);

        // Obtener ventas del día (array correcto)
        $todaySales = $this->cashierService->getTodaySales();
        
        // Calcular estadísticas del periodo seleccionado
        $totalSales = [
            'count' => $sales->total(),
            'total' => $sales->sum('total_price'),
            'average' => $sales->total() > 0 ? $sales->sum('total_price') / $sales->count() : 0,
        ];

        // Métodos de pago
        $paymentMethods = $sales->groupBy('payment_method')->map(function ($group) {
            return $group->count();
        });

        return view('cashier.history', compact(
            'sales',
            'todaySales',
            'totalSales',
            'paymentMethods'
        ));
    }

    /**
     * Cerrar caja
     */
    public function closeCashier(Request $request)
    {
        $validated = $request->validate([
            'final_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        // Calcular total del día
        $todaySales = Sale::where('company_id', auth()->user()->company_id)
            ->whereDate('created_at', today())
            ->where('status', 'completed')
            ->sum('total_price');

        $difference = $validated['final_amount'] - $todaySales;

        // Registrar movimiento de cierre
        CashMovement::create([
            'user_id' => auth()->id(),
            'company_id' => auth()->user()->company_id,
            'type' => 'expense',
            'amount' => $validated['final_amount'],
            'description' => 'Cierre de caja - ' . date('d/m/Y'),
            'reference' => 'CIERRE-' . date('Ymd'),
        ]);

        return redirect()->route('cashier.dashboard')
            ->with('success', 'Caja cerrada. Diferencia: $' . number_format($difference, 2));
    }

    /**
     * Dashboard de caja
     */
    public function dashboard()
    {
        $todaySales = Sale::where('company_id', auth()->user()->company_id)
            ->whereDate('created_at', today())
            ->where('status', 'completed')
            ->get();

        $todayTotal = $todaySales->sum('total_price');
        $todayCount = $todaySales->count();

        $movements = CashMovement::where('company_id', auth()->user()->company_id)
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('cashier.dashboard', compact('todayTotal', 'todayCount', 'movements'));
    }
}