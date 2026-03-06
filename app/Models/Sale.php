<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = ['invoice_number', 'items', 'customer_name', 'customer_phone', 'customer_id', 'customer_address', 'user_id', 'total_price', 'discount', 'discount_percentage', 'tax', 'delivery_fee', 'payment_method', 'status', 'notes', 'company_id'];

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
        return $this->belongsTo(User::class);
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
}
