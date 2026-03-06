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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->json('items')->nullable(); // JSON con detalles de los productos vendidos
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_id')->nullable();
            $table->string('customer_address')->nullable();
            $table->decimal('total_price', 8, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->string('payment_method')->default('cash');    
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('completed');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
