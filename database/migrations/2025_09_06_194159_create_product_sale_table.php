<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')
                  ->constrained('sales')
                  ->onDelete('cascade'); // si se elimina la venta, se eliminan las relaciones
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->onDelete('cascade'); // si se elimina el producto, se eliminan las relaciones
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_product');
    }
};
