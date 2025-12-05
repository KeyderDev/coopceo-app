<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{
public function info()
{
    $db = DB::getDatabaseName();

    $tables = DB::select("
        SELECT 
            table_name AS name,
            engine,
            table_rows,
            create_time,
            update_time,
            table_collation,
            ROUND((data_length / 1024 / 1024), 2) AS data_mb,
            ROUND((index_length / 1024 / 1024), 2) AS index_mb,
            ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
        FROM information_schema.TABLES
        WHERE table_schema = ?
    ", [$db]);

    $totalData = array_sum(array_column($tables, 'data_mb'));
    $totalIndex = array_sum(array_column($tables, 'index_mb'));
    $totalSize = array_sum(array_column($tables, 'size_mb'));

    return response()->json([
        'database' => $db,
        'tables' => $tables,
        'total_data_mb' => round($totalData, 2),
        'total_index_mb' => round($totalIndex, 2),
        'total_mb' => round($totalSize, 2),
        'count' => count($tables)
    ]);
}


    public function backup()
    {
        // OBTENER NOMBRE DE BASE DE DATOS
        $db = DB::getDatabaseName();
        $file = "backup_{$db}_" . date('Y-m-d_His') . ".sql";
        $path = storage_path("app/public/$file");

        // ASEGURAR QUE EL DIRECTORIO EXISTE
        if (!is_dir(storage_path("app/public"))) {
            mkdir(storage_path("app/public"), 0775, true);
        }

        // DATOS DE CONEXIÓN
        $user = env('DB_USERNAME');
        $pass = env('DB_PASSWORD');
        $host = env('DB_HOST');

        // ESCAPE PASSWORD PARA QUE NO ROMPA EL COMANDO
        $escapedPass = escapeshellarg($pass);

        // COMANDO PARA LINUX
        $command = "mysqldump -h {$host} -u {$user} -p{$escapedPass} {$db} > \"{$path}\"";

        exec($command, $output, $resultCode);

        if ($resultCode !== 0) {
            return response()->json([
                'error' => 'Error ejecutando mysqldump',
                'command' => $command,
                'output' => $output,
                'code' => $resultCode
            ], 500);
        }

        return response()->download($path)->deleteFileAfterSend(true);
    }
}
