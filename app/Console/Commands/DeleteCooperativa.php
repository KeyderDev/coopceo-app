<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteCooperativa extends Command
{
    protected $signature = 'jucoop:delete-coop {codigo}';
    protected $description = 'Eliminar una cooperativa y su base de datos.';

    public function handle()
    {
        $codigo = strtoupper($this->argument('codigo'));
        $coop = DB::connection('mysql_main')->table('cooperativas')->where('codigo', $codigo)->first();

        if (!$coop) {
            $this->error("No existe una cooperativa con el código $codigo");
            return 1;
        }

        $dbName = $coop->db_name;

        $this->info("Eliminando base de datos: $dbName");
        DB::statement("DROP DATABASE IF EXISTS `$dbName`");

        DB::connection('mysql_main')->table('cooperativas')->where('codigo', $codigo)->delete();

        $this->info("Cooperativa eliminada: $codigo");

        return 0;
    }
}
