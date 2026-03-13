<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Picqer\Barcode\BarcodeGeneratorSVG;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;
    use LogsActivity;

    protected function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'image', 'category_id', 'description', 'brand_id', 'barcode', 'barcode_image'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('products')
            ->setDescriptionForEvent(fn(string $eventName) => match($eventName) {
                'created' => 'Producto creado',
                'updated' => 'Producto actualizado',
                'deleted' => 'Producto eliminado',
                default => $eventName,
            });
    }

    protected $fillable = ['name', 'image', 'category_id', 'barcode', 'barcode_image', 'description', 'company_id', 'brand_id'];
    
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

    /**
     * Generar un código de barras único
     * 
     * @return string
     */
    public static function generateBarcode()
    {
        $maxAttempts = 100; // Límite de seguridad para evitar bucles infinitos
        $attempts = 0;
        $barcode = '';

        do {
            $barcode = self::generateRandomBarcode();
            $attempts++;

            // Verificar si el código ya existe en la base de datos
            $exists = self::where('barcode', $barcode)->exists();

        } while ($exists && $attempts < $maxAttempts);

        if ($attempts >= $maxAttempts) {
            throw new \Exception("No se pudo generar un código de barras único después de $maxAttempts intentos.");
        }

        return $barcode;
    }

    /**
     * Calcular el dígito de control para EAN-13
     */
    private static function calculateChecksum($number)
    {
        $sum = 0;
        $length = strlen($number);
        
        for ($i = 0; $i < $length; $i++) {
            $digit = (int) $number[$i];
            $sum += ($i % 2 == 0) ? $digit * 3 : $digit;
        }
        
        $remainder = $sum % 10;
        return ($remainder == 0) ? 0 : 10 - $remainder;
    }

    /**
     * Generar un número aleatorio de 12 dígitos + checksum
     */
    private static function generateRandomBarcode()
    {
        $companyId = auth()->user()->company_id;

        $baseNumber = str_pad(rand(0, 999999999999), 9, '0', STR_PAD_LEFT);
        $checksum = self::calculateChecksum($baseNumber);
        return $baseNumber . $checksum . $companyId;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->barcode)) {
                try {
                    $product->barcode = self::generateBarcode();
                    
                    // Generar imagen
                    $generator = new BarcodeGeneratorSVG();
                    $image = $generator->getBarcode($product->barcode, 'C128B', 3);
                    
                    // Guardar imagen
                    $filename = "products/{$product->company_id}/{$product->barcode}.svg";
                    Storage::disk('public')->put($filename, $image);
                    
                    $product->barcode_image = $filename;
                } catch (\Exception $e) {
                    \Log::error('Error SVG Barcode: ' . $e->getMessage());
                    return null;
                }
            }
        });
    }

    /**
     * Verificar si se puede crear más productos
     */
    public static function canCreate()
    {
        $configuration = Configuration::getCurrentCompanyConfiguration();

        if (!$configuration) {
            return [
                'success' => false,
                'message' => 'Configuración no encontrada',
            ];
        }

        if (!$configuration->isActive()) {
            return [
                'success' => false,
                'message' => 'La configuración de la empresa ha expirado',
            ];
        }

        $currentProducts = self::where('company_id', auth()->user()->company_id)
            ->where('active', true)
            ->count();

        if ($currentProducts >= $configuration->quantity_products) {
            return [
                'success' => false,
                'message' => $configuration->getLimitMessage('products'),
            ];
        }

        return [
            'success' => true,
            'message' => 'Puedes crear más productos',
        ];
    }

    /**
     * Obtener cantidad de productos activos
     */
    public static function getActiveCount()
    {
        return self::where('company_id', auth()->user()->company_id)
            ->where('active', true)
            ->count();
    }

    /**
     * Obtener porcentaje de uso
     */
    public static function getUsagePercentage()
    {
        $configuration = Configuration::getCurrentCompanyConfiguration();

        if (!$configuration) {
            return 0;
        }

        $currentProducts = self::getActiveCount();
        $percentage = ($currentProducts / $configuration->quantity_products) * 100;

        return min($percentage, 100);
    }
}
