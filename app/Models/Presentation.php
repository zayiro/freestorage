<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Presentation extends Model
{
    use HasFactory;
    use LogsActivity;

    protected function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['product_id', 'presentation', 'purchase_price', 'sales_price', 'unit', 'stock', 'barcode', 'active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('presentations')
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'Presentación creada',
                'updated' => 'Presentación actualizada',
                'deleted' => 'Presentación eliminada',
                default => $eventName,
            });
    }

    protected $fillable = ['product_id', 'presentation', 'purchase_price', 'sales_price', 'unit', 'stock', 'active'];

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
