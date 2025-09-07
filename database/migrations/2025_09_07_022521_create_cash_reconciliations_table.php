<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cash_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->decimal('petty', 8, 2)->default(50);
            $table->integer('bill_20')->default(0);
            $table->integer('bill_10')->default(0);
            $table->integer('bill_5')->default(0);
            $table->integer('bill_1')->default(0);
            $table->integer('coin_10')->default(0);
            $table->integer('coin_5')->default(0);
            $table->integer('coin_1')->default(0);
            $table->integer('coin_25')->default(0);
            $table->decimal('total_counted', 8, 2);
            $table->decimal('total_sales_cash', 8, 2);
            $table->decimal('difference', 8, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cash_reconciliations');
    }
};
