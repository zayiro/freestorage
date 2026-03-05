<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Category extends Model
{
    use HasFactory;
    use LogsActivity;

    protected function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('categories')
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'Categoría creada',
                'updated' => 'Categoría actualizada',
                'deleted' => 'Categoría eliminada',
                default => $eventName,
            });
    }

    protected $fillable = ['name', 'description', 'company_id'];

    public function products() {
        return $this->hasMany(Product::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
