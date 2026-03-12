<?php

// app/Services/CashierService.php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\CashMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class CashierService
{
    private const CART_KEY = 'cashier_cart';

    // ... (otros métodos existentes)

    /**
     * Obtener productos más vendidos
     */
    public function getTopProducts(int $limit = 10): array
    {
        $companyId = Auth::user()->company_id;

        $products = SaleItem::whereHas('sale', function ($query) use ($companyId) {
            $query->where('company_id', $companyId)
                  ->where('status', 'completed');
        })
        ->select(
            'product_id',
            'product_name',
            'product_sku',
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(subtotal) as total_sales'),
            DB::raw('AVG(price) as avg_price')
        )
        ->groupBy('product_id', 'product_name', 'product_sku')
        ->orderBy('total_quantity', 'desc')
        ->limit($limit)
        ->get();

        return $products->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'product_sku' => $item->product_sku,
                'total_quantity' => $item->total_quantity,
                'total_sales' => $item->total_sales,
                'avg_price' => $item->avg_price,
            ];
        })->toArray();
    }

    /**
     * Obtener ventas del día (CORREGIDO)
     */
    public function getTodaySales(): array
    {
        $companyId = Auth::user()->company_id;

        // Obtener ventas del día como Collection
        $sales = Sale::where('company_id', $companyId)
            ->whereDate('created_at', today())
            ->where('status', 'completed')
            ->with(['cashier', 'items'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Convertir a Collection para usar sum()
        $salesCollection = collect($sales);

        // Calcular estadísticas
        $total = $salesCollection->sum('total_price');
        $count = $salesCollection->count();
        $average = $count > 0 ? $total / $count : 0;
        $totalItems = $salesCollection->sum(function($sale) {
            return $sale->items->sum('quantity');
        });

        // Calcular por método de pago
        $cashTotal = $salesCollection->where('payment_method', 'cash')->sum('total_price');
        $cardTotal = $salesCollection->where('payment_method', 'card')->sum('total_price');
        $transferTotal = $salesCollection->where('payment_method', 'transfer')->sum('total_price');

        $cashPercentage = $total > 0 ? ($cashTotal / $total) * 100 : 0;
        $cardPercentage = $total > 0 ? ($cardTotal / $total) * 100 : 0;
        $transferPercentage = $total > 0 ? ($transferTotal / $total) * 100 : 0;

        return [
            'sales' => $sales,
            'count' => $count,
            'total' => $total,
            'average' => $average,
            'total_items' => $totalItems,
            'cash' => $cashTotal,
            'card' => $cardTotal,
            'transfer' => $transferTotal,
            'cash_percentage' => round($cashPercentage, 1),
            'card_percentage' => round($cardPercentage, 1),
            'transfer_percentage' => round($transferPercentage, 1),
        ];
    }

    /**
     * Obtener movimientos de caja del día (CORREGIDO)
     */
    public function getTodayMovements(): array
    {
        $companyId = Auth::user()->company_id;

        // Obtener movimientos del día como Collection
        $movements = CashMovement::where('company_id', $companyId)
            ->whereDate('created_at', today())
            ->with(['user', 'sale'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Convertir a Collection para usar sum()
        $movementsCollection = collect($movements);

        // Calcular totales
        $income = $movementsCollection->where('type', 'income')->sum('amount');
        $expense = $movementsCollection->where('type', 'expense')->sum('amount');
        $balance = $income - $expense;

        // Agrupar por tipo
        $incomeMovements = $movementsCollection->where('type', 'income');
        $expenseMovements = $movementsCollection->where('type', 'expense');

        return [
            'movements' => $movements,
            'income' => $income,
            'expense' => $expense,
            'balance' => $balance,
            'income_count' => $incomeMovements->count(),
            'expense_count' => $expenseMovements->count(),
            'income_details' => $incomeMovements->map(function ($movement) {
                return [
                    'id' => $movement->id,
                    'type' => $movement->type,
                    'amount' => $movement->amount,
                    'description' => $movement->description,
                    'reference' => $movement->reference,
                    'created_at' => $movement->created_at->format('H:i'),
                    'user' => $movement->user->name ?? 'N/A',
                ];
            }),
            'expense_details' => $expenseMovements->map(function ($movement) {
                return [
                    'id' => $movement->id,
                    'type' => $movement->type,
                    'amount' => $movement->amount,
                    'description' => $movement->description,
                    'reference' => $movement->reference,
                    'created_at' => $movement->created_at->format('H:i'),
                    'user' => $movement->user->name ?? 'N/A',
                ];
            }),
        ];
    }

    /**
     * Obtener ventas por hora (CORREGIDO)
     */
    public function getSalesByHour(): array
    {
        $companyId = Auth::user()->company_id;

        $sales = Sale::where('company_id', $companyId)
            ->whereDate('created_at', today())
            ->where('status', 'completed')
            ->get();

        $hours = [];
        for ($i = 0; $i < 24; $i++) {
            $hours[] = [
                'hour' => sprintf('%02d:00', $i),
                'count' => 0,
                'total' => 0,
            ];
        }

        foreach ($sales as $sale) {
            $hour = \Carbon\Carbon::parse($sale->created_at)->format('H:00');
            $hours[$hour]['count']++;
            $hours[$hour]['total'] += $sale->total_price;
        }

        return $hours;
    }

    /**
     * Obtener productos más vendidos por periodo
     */
    public function getTopProductsByPeriod(string $period = 'month', int $limit = 10): array
    {
        $companyId = Auth::user()->company_id;
        $dateCondition = $this->getDateCondition($period);

        $products = SaleItem::whereHas('sale', function ($query) use ($companyId, $dateCondition) {
            $query->where('company_id', $companyId)
                  ->where('status', 'completed')
                  ->where($dateCondition['column'], $dateCondition['value']);
        })
        ->select(
            'product_id',
            'product_name',
            'product_sku',
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(subtotal) as total_sales')
        )
        ->groupBy('product_id', 'product_name', 'product_sku')
        ->orderBy('total_quantity', 'desc')
        ->limit($limit)
        ->get();

        return $products->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'product_sku' => $item->product_sku,
                'total_quantity' => $item->total_quantity,
                'total_sales' => $item->total_sales,
            ];
        })->toArray();
    }

    /**
     * Obtener condición de fecha según periodo
     */
    private function getDateCondition(string $period): array
    {
        switch ($period) {
            case 'day':
                return ['column' => 'created_at', 'value' => today()];
            case 'week':
                return ['column' => 'created_at', 'value' => today()->startOfWeek()];
            case 'month':
                return ['column' => 'created_at', 'value' => today()->startOfMonth()];
            case 'year':
                return ['column' => 'created_at', 'value' => today()->startOfYear()];
            default:
                return ['column' => 'created_at', 'value' => today()->startOfMonth()];
        }
    }

    /**
     * Obtener productos con stock bajo
     */
    public function getLowStockProducts(int $threshold = 10): array
    {
        $companyId = Auth::user()->company_id;

        return Product::where('company_id', $companyId)
            ->where('stock', '<=', $threshold)
            ->where('active', true)
            ->orderBy('stock', 'asc')
            ->limit(10)
            ->get();
    }

    /**
     * Obtener productos sin stock
     */
    public function getOutOfStockProducts(): array
    {
        $companyId = Auth::user()->company_id;

        return Product::where('company_id', $companyId)
            ->where('stock', 0)
            ->where('active', true)
            ->get();
    }

    /**
     * Obtener estadísticas de productos
     */
    public function getProductStatistics(): array
    {
        $companyId = Auth::user()->company_id;

        $totalProducts = Product::where('company_id', $companyId)->count();
        $activeProducts = Product::where('company_id', $companyId)
            ->where('active', true)
            ->count();
        $lowStockProducts = Product::where('company_id', $companyId)
            ->where('stock', '<=', 10)
            ->count();
        $outOfStockProducts = Product::where('company_id', $companyId)
            ->where('stock', 0)
            ->count();

        $totalSales = SaleItem::whereHas('sale', function ($query) use ($companyId) {
            $query->where('company_id', $companyId)
                  ->where('status', 'completed');
        })->sum('quantity');

        $totalRevenue = SaleItem::whereHas('sale', function ($query) use ($companyId) {
            $query->where('company_id', $companyId)
                  ->where('status', 'completed');
        })->sum('subtotal');

        return [
            'total_products' => $totalProducts,
            'active_products' => $activeProducts,
            'low_stock_products' => $lowStockProducts,
            'out_of_stock_products' => $outOfStockProducts,
            'total_items_sold' => $totalSales,
            'total_revenue' => $totalRevenue,
        ];
    }

    /**
     * Obtener estadísticas de movimientos (CORREGIDO)
     */
    public function getMovementStatistics(): array
    {
        $companyId = Auth::user()->company_id;

        // Movimientos del día
        $todayMovements = $this->getTodayMovements();

        // Movimientos del mes
        $monthMovements = CashMovement::where('company_id', $companyId)
            ->whereMonth('created_at', now()->month)
            ->get();

        $monthIncome = collect($monthMovements)->where('type', 'income')->sum('amount');
        $monthExpense = collect($monthMovements)->where('type', 'expense')->sum('amount');

        // Movimientos del año
        $yearMovements = CashMovement::where('company_id', $companyId)
            ->whereYear('created_at', now()->year)
            ->get();

        $yearIncome = collect($yearMovements)->where('type', 'income')->sum('amount');
        $yearExpense = collect($yearMovements)->where('type', 'expense')->sum('amount');

        return [
            'today' => [
                'income' => $todayMovements['income'],
                'expense' => $todayMovements['expense'],
                'balance' => $todayMovements['balance'],
                'income_count' => $todayMovements['income_count'],
                'expense_count' => $todayMovements['expense_count'],
            ],
            'month' => [
                'income' => $monthIncome,
                'expense' => $monthExpense,
                'balance' => $monthIncome - $monthExpense,
            ],
            'year' => [
                'income' => $yearIncome,
                'expense' => $yearExpense,
                'balance' => $yearIncome - $yearExpense,
            ],
        ];
    }
}