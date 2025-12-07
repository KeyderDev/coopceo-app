<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use App\Models\Cooperativa;

class CreateCooperativa extends Command
{
    protected $signature = 'jucoop:create-coop {codigo} {nombre}';
    protected $description = 'Crear una nueva cooperativa y su base de datos con la estructura JuCoop.';

    public function handle()
    {
        $codigo = strtoupper($this->argument('codigo'));
        $nombre = $this->argument('nombre');
        $dbName = 'coop_' . strtolower($codigo);

        $this->info("Creando cooperativa: $nombre ($codigo)");
        $this->info("Base de datos: $dbName");

        $exists = DB::connection('mysql_main')
            ->table('cooperativas')
            ->where('codigo', $codigo)
            ->exists();

        if ($exists) {
            $this->error("Ya existe una cooperativa con el código $codigo");
            return 1;
        }

        DB::statement("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        config(['database.connections.mysql.database' => $dbName]);
        DB::purge('mysql');
        DB::reconnect('mysql');

        $this->info("Ejecutando migraciones en $dbName...");

        Artisan::call('migrate', [
            '--path' => 'database/migrations/school',
            '--database' => 'mysql',
            '--force' => true,
        ]);

        $this->info("Migraciones completadas.");

        DB::connection('mysql_main')->table('cooperativas')->insert([
            'nombre' => $nombre,
            'codigo' => $codigo,
            'db_name' => $dbName,
            'db_user' => env('DB_MAIN_USERNAME'),
            'db_pass' => env('DB_MAIN_PASSWORD'),
        ]);

        $this->info("Cooperativa registrada exitosamente en jucoop_main.");
        $this->info("Cooperativa creada completamente: $nombre ($codigo)");

        return 0;
    }
}
