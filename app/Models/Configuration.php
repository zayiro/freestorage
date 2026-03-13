<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Configuration extends Model
{  
    // Definir campos llenables
    protected $fillable = [
        'company_id',
        'currency',
        'timezone',
        'notifications_enabled',
        'plan',
        'quantity_products',
        'quantity_users',
        'quantity_sales',
        'is_active',
        'settings'
    ];

    protected $casts = [
        'quantity_products' => 'integer',
        'quantity_users' => 'integer',
        'quantity_sales' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relación: Una configuración pertenece a UNA empresa
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Obtener configuración de la empresa actual
     */
    public static function getCurrentCompanyConfiguration()
    {
        return self::where('company_id', auth()->user()->company_id)
            ->first();
    }

    /**
     * Verificar si la configuración está activa
     */
    public function isActive()
    {
        return $this->is_active && 
               ($this->expires_at === null || $this->expires_at->isFuture());
    }

    /**
     * Verificar si se puede crear más productos
     */
    public function canCreateProducts()
    {
        if (!$this->isActive()) {
            return false;
        }

        $currentProducts = Product::where('company_id', $this->company_id)
            ->where('active', true)
            ->count();

        return $currentProducts < $this->quantity_products;
    }

    /**
     * Obtener mensaje de error
     */
    public function getLimitMessage($type = 'products')
    {
        $messages = [
            'products' => "Has alcanzado el límite de {$this->quantity_products} productos. Por favor, actualiza tu plan.",
            'users' => "Has alcanzado el límite de {$this->quantity_users} usuarios. Por favor, actualiza tu plan.",
            'sales' => "Has alcanzado el límite de {$this->quantity_sales} ventas. Por favor, actualiza tu plan.",
        ];

        return $messages[$type] ?? "Has alcanzado el límite de {$this->$type}. Por favor, actualiza tu plan.";
    }
}