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
Schema::create('drawers', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // Ej: 4A
    $table->unsignedBigInteger('assigned_user_id')->nullable();
    $table->decimal('petty', 8, 2)->default(0);
    $table->boolean('is_open')->default(false);
    $table->timestamps();

    $table->foreign('assigned_user_id')->references('id')->on('users')->nullOnDelete();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drawers');
    }
};
