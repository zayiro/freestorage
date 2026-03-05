<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Configuration;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'phone', 'email', 'image'];

    // Relación: Una empresa tiene UNA configuración
    public function configuration()
    {
        return $this->hasOne(Configuration::class);
    }

    // Aquí definimos el evento para crear la configuración automáticamente
    protected static function boot()
    {
        parent::boot();

        // Se ejecuta ANTES de guardar la empresa en la base de datos
        static::creating(function ($company) {
            // Creamos la configuración vacía y la vinculamos
            $company->configuration()->save(new Configuration());
        });
    }

    public function users() {
        return $this->hasMany(User::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function brands()
    {
        return $this->hasMany(Brand::class);
    }
}
