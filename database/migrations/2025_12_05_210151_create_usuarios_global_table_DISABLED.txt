<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::connection('mysql_main')->create('usuarios_global', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('coop_codigo');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::connection('mysql_main')->dropIfExists('usuarios_global');
    }
};
