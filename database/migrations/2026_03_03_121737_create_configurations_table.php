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
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            // Relación con la empresa
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            
            // Campos de configuración (ejemplos)
            $table->string('currency')->default('COP');
            $table->string('timezone')->default('UTC');
            $table->boolean('notifications_enabled')->default(true);
            $table->json('settings')->nullable(); // Para configuraciones dinámicas
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configurations');
    }
};
