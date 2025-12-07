<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistroComprasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registro_compras', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('descripcion')->nullable(); // opcional, ej: "Compra de snacks"
            $table->decimal('total', 12, 2)->default(0); // total gastado en esa compra
            $table->date('fecha_compra')->default(DB::raw('CURRENT_DATE')); // fecha de la compra
            $table->string('proveedor')->nullable(); // opcional
            $table->string('metodo_pago')->nullable();
            $table->timestamps();

            // índices útiles
            $table->index('fecha_compra');
            $table->index('proveedor');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registro_compras');
    }
}
