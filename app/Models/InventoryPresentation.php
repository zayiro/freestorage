<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryPresentation extends Model
{
    use HasFactory;
    protected $table = 'inventory_presentations';
    public $timestamps = false; // Desactiva timestamps automáticos
    protected $fillable = ['presentation_id', 'current_quantity', 'minimum_quantity', 'location', 'updated_at'];

    public function presentation() {
        return $this->belongsTo(Presentation::class);
    }

    // Actualizar updated_at automáticamente al crear o modificar
    protected static function boot() {
        parent::boot();

        static::updating(function ($model) {
            $model->updated_at = now();
        });

        static::creating(function ($model) {
            $model->updated_at = now();
        });
    }

    public function presentacion() {
        return $this->belongsTo(Presentation::class);
    }

    // Helpers
    public function necesitaReabastecimiento() {
        return $this->cantidad_actual <= $this->cantidad_minima;
    }

    public function agregarStock($cantidad) {
        $this->increment('cantidad_actual', $cantidad);
    }

    public function quitarStock($cantidad) {
        if ($this->cantidad_actual >= $cantidad) {
            $this->decrement('cantidad_actual', $cantidad);
            return true;
        }
        return false;
    }
}
