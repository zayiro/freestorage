<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Configuration extends Model
{
    // Relación: Una configuración pertenece a UNA empresa
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Definir campos llenables
    protected $fillable = [
        'company_id',
        'currency',
        'timezone',
        'notifications_enabled',
        'settings'
    ];
}