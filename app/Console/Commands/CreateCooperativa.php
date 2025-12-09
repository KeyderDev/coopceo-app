<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class CreateCooperativa extends Command
{
    protected $signature = 'jucoop:create-coop {codigo} {nombre} {email} {password}';
    protected $description = 'Crear una nueva cooperativa, su base de datos y un usuario administrador.';

    public function handle()
    {
        $codigo = strtoupper($this->argument('codigo'));
        $nombre = $this->argument('nombre');
        $email = strtolower($this->argument('email'));
        $password = $this->argument('password');

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

        config([
            'database.connections.tenant.database' => $dbName,
            'database.connections.tenant.username' => env('DB_MAIN_USERNAME'),
            'database.connections.tenant.password' => env('DB_MAIN_PASSWORD'),
        ]);

        DB::purge('tenant');
        DB::reconnect('tenant');

        Artisan::call('migrate', [
            '--path' => 'database/migrations/school',
            '--database' => 'tenant',
            '--force' => true,
        ]);

        DB::connection('tenant')->table('users')->insert([
            'nombre' => 'Administrador',
            'apellido' => 'Principal',
            'email' => $email,
            'password' => Hash::make($password),
            'admin' => 1,
            'api_token' => bin2hex(random_bytes(30))
        ]);

        DB::connection('mysql_main')->table('cooperativas')->insert([
            'nombre' => $nombre,
            'codigo' => $codigo,
            'db_name' => $dbName,
            'db_user' => env('DB_MAIN_USERNAME'),
            'db_pass' => env('DB_MAIN_PASSWORD'),
        ]);

        $this->info("Usuario administrador creado: $email");
        $this->info("Cooperativa registrada y creada exitosamente");

        return 0;
    }
}
