<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Brand extends Model
{
    use HasFactory;
    use LogsActivity;

    protected function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('brands')
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'Marca creada',
                'updated' => 'Marca actualizada',
                'deleted' => 'Marca eliminada',
                default => $eventName,
            });
    }

    protected $fillable = ['name', 'description', 'company_id'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
