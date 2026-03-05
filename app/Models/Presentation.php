<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presentation extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'presentation', 'purchase_price', 'sales_price', 'unit', 'stock', 'barcode'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function inventoryPresentation() {
        return $this->hasOne(InventoryPresentation::class);
    }

    // Relación con Producto (nombre en español)
    public function product() {
        return $this->belongsTo(Product::class);
    }

    // Relación con Inventario
    public function inventory() {
        return $this->hasOne(InventoryPresentation::class);
    }
}
