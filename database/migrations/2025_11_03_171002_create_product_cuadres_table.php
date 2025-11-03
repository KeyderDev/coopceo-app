<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_cuadres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('stock')->default(0);     // Stock esperado
            $table->integer('contado')->nullable();   // Cantidad contada
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_cuadres');
    }
};
