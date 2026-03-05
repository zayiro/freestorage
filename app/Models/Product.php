<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image', 'category_id', 'description', 'company_id', 'brand_id'];
    
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    public function presentations()
    {
        return $this->hasMany(Presentation::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function inventories()
    {
        return $this->hasMany(InventoryPresentation::class);
    }
    
    // Obtener stock total (suma de todos los inventarios)
    public function getTotalStockAttribute()
    {
        return $this->inventories->sum('quantity');
    }
}
