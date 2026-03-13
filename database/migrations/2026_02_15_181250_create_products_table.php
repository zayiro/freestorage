<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');          
            $table->string('image')->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null'); // Referencia a categories, nullable
            $table->foreignId('brand_id')->nullable()->constrained()->onDelete('set null'); // Referencia a brands, opcional
            $table->string('sku')->nullable();            
            $table->string('barcode')->unique()->nullable();
            $table->text('barcode_image')->nullable();
            $table->text('description')->nullable();            
            $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('set null');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');        
    }
};
