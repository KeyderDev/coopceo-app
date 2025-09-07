<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['product_id']); // eliminar la FK primero
            $table->dropColumn('product_id');    // eliminar la columna
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained('products');
        });
    }
};
