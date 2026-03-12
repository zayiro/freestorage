<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Models\CashMovement;

class Sale extends Model
{
    use HasFactory;
    use LogsActivity;

    protected function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['invoice_number', 'items', 'customer_name', 'customer_phone', 'customer_id', 'customer_address', 'user_id', 'total_price', 'total_items', 'discount', 'discount_percentage', 'tax', 'delivery_fee', 'payment_method', 'status', 'notes', 'company_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('sales')
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'Venta creada',
                'updated' => 'Venta actualizada',
                'deleted' => 'Venta eliminada',
                default => $eventName,
            });
    }

    protected $fillable = ['invoice_number', 'items', 'customer_name', 'customer_phone', 'customer_id', 'customer_address', 'user_id', 'total_price', 'total_items', 'discount', 'discount_percentage', 'tax', 'delivery_fee', 'payment_method', 'status', 'notes', 'company_id'];

    protected $casts = ['total_price' => 'decimal:2', 'discount' => 'decimal:2', 'discount_percentage' => 'decimal:2', 'tax' => 'decimal:2', 'delivery_fee' => 'decimal:2'];

     /**
     * Relación con el producto vendido.
     */

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relación con el usuario que realizó la venta.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con los ítems de venta
     */
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Generar número de factura único.
     */
    public static function generateInvoiceNumber()
    {
        $year = date('Y');
        $month = date('m');
        $lastSale = self::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastSale ? intval(substr($lastSale->invoice_number, -6)) + 1 : 1;

        return sprintf('FAC-%s%06d', $year, $number);
    }

    public function decrementarStock($cantidad, $descripcion = '')
    {
        if ($this->stock < $cantidad) {
            return false;
        }

        $this->decrement('stock', $cantidad);

        // Registrar movimiento de stock
        $this->movimientos()->create([
            'tipo' => 'salida',
            'cantidad' => $cantidad,
            'descripcion' => $descripcion,
            'stock_anterior' => $this->stock + $cantidad,
            'stock_actual' => $this->stock - $cantidad,
        ]);

        return true;
    }

    // Método para incrementar stock
    public function incrementarStock($cantidad, $descripcion = '')
    {
        $this->increment('stock', $cantidad);

        // Registrar movimiento de stock
        $this->movimientos()->create([
            'tipo' => 'entrada',
            'cantidad' => $cantidad,
            'descripcion' => $descripcion,
            'stock_anterior' => $this->stock - $cantidad,
            'stock_actual' => $this->stock + $cantidad,
        ]);

        return true;
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function movement()
    {
        return $this->belongsTo(CashMovement::class);
    }

    /**
     * Obtener total de ítems (CORREGIDO)
     */
    public function getTotalItemsAttribute()
    {
        return $this->items()->sum('quantity');
    }

    /**
     * Obtener productos vendidos
     */
    public function getProductsSoldAttribute()
    {
        return $this->items()->pluck('product_name');
    }

    /**
     * Calcular descuento total
     */
    public function getDiscountTotalAttribute()
    {
        return $this->items()->sum('discount');
    }

    /**
     * Calcular impuesto total
     */
    public function getTaxTotalAttribute()
    {
        return $this->items()->sum('tax');
    }

    /**
     * Obtener total de ventas
     */
    public function getTotalSalesAttribute()
    {
        return $this->items()->sum('subtotal');
    }

    /**
     * Obtener cantidad de ítems
     */
    public function getItemCountAttribute()
    {
        return $this->items()->count();
    }
}
