<?php

namespace App\Services;

use App\Models\Configuration;
use App\Models\Product;
use App\Models\User;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;

class ProductLimitService
{
    /**
     * Verificar si se puede crear un producto
     */
    public function canCreateProduct(): array
    {
        $configuration = Configuration::getCurrentCompanyConfiguration();

        if (!$configuration) {
            return [
                'success' => false,
                'message' => 'Configuración no encontrada. Contacta al administrador.',
            ];
        }

        if (!$configuration->isActive()) {
            return [
                'success' => false,
                'message' => 'Tu plan ha expirado. Por favor, renueva tu suscripción.',
            ];
        }

        $currentProducts = Product::where('company_id', Auth::user()->company_id)
            ->where('active', true)
            ->count();

        if ($currentProducts >= $configuration->quantity_products) {
            return [
                'success' => false,
                'message' => "Has alcanzado el límite de {$configuration->quantity_products} productos. Por favor, actualiza tu plan.",
            ];
        }

        return [
            'success' => true,
            'message' => 'Puedes crear más productos',
            'current' => $currentProducts,
            'limit' => $configuration->quantity_products,
            'remaining' => $configuration->quantity_products - $currentProducts,
        ];
    }

    /**
     * Verificar si se puede crear un usuario
     */
    public function canCreateUser(): array
    {
        $configuration = Configuration::getCurrentCompanyConfiguration();

        if (!$configuration) {
            return [
                'success' => false,
                'message' => 'Configuración no encontrada.',
            ];
        }

        if (!$configuration->isActive()) {
            return [
                'success' => false,
                'message' => 'Tu plan ha expirado.',
            ];
        }

        $currentUsers = User::where('company_id', Auth::user()->company_id)->count();

        if ($currentUsers >= $configuration->quantity_users) {
            return [
                'success' => false,
                'message' => "Has alcanzado el límite de {$configuration->quantity_users} usuarios.",
            ];
        }

        return [
            'success' => true,
            'message' => 'Puedes crear más usuarios',
            'current' => $currentUsers,
            'limit' => $configuration->quantity_users,
            'remaining' => $configuration->quantity_users - $currentUsers,
        ];
    }

    /**
     * Verificar si se puede crear una venta
     */
    public function canCreateSale(): array
    {
        $configuration = Configuration::getCurrentCompanyConfiguration();

        if (!$configuration) {
            return [
                'success' => false,
                'message' => 'Configuración no encontrada.',
            ];
        }

        if (!$configuration->isActive()) {
            return [
                'success' => false,
                'message' => 'Tu plan ha expirado.',
            ];
        }

        $currentSales = Sale::where('company_id', Auth::user()->company_id)
            ->where('status', 'completed')
            ->count();

        if ($currentSales >= $configuration->quantity_sales) {
            return [
                'success' => false,
                'message' => "Has alcanzado el límite de {$configuration->quantity_sales} ventas.",
            ];
        }

        return [
            'success' => true,
            'message' => 'Puedes crear más ventas',
            'current' => $currentSales,
            'limit' => $configuration->quantity_sales,
            'remaining' => $configuration->quantity_sales - $currentSales,
        ];
    }

    /**
     * Obtener estadísticas de uso
     */
    public function getUsageStatistics(): array
    {
        $configuration = Configuration::getCurrentCompanyConfiguration();

        if (!$configuration) {
            return [
                'success' => false,
                'message' => 'Configuración no encontrada.',
            ];
        }

        $products = Product::where('company_id', Auth::user()->company_id)
            ->where('active', true)
            ->count();

        $users = User::where('company_id', Auth::user()->company_id)->count();

        $sales = Sale::where('company_id', Auth::user()->company_id)
            ->where('status', 'completed')
            ->count();

        return [
            'success' => true,
            'products' => [
                'current' => $products,
                'limit' => $configuration->quantity_products,
                'percentage' => ($products / $configuration->quantity_products) * 100,
                'remaining' => $configuration->quantity_products - $products,
            ],
            'users' => [
                'current' => $users,
                'limit' => $configuration->quantity_users,
                'percentage' => ($users / $configuration->quantity_users) * 100,
                'remaining' => $configuration->quantity_users - $users,
            ],
            'sales' => [
                'current' => $sales,
                'limit' => $configuration->quantity_sales,
                'percentage' => ($sales / $configuration->quantity_sales) * 100,
                'remaining' => $configuration->quantity_sales - $sales,
            ],
            'plan' => $configuration->plan,
            'expires_at' => $configuration->expires_at,
        ];
    }
}