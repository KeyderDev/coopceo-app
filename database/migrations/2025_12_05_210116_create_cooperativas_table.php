<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::connection('mysql_main')->create('cooperativas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo')->unique();
            $table->string('db_name');
            $table->string('db_user');
            $table->string('db_pass');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::connection('mysql_main')->dropIfExists('cooperativas');
    }
};
