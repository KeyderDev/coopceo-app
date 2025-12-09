<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use App\Models\Cooperativa;
use App\Services\TenantManager;

class TenantsMigrate extends Command
{
    protected $signature = 'tenants:migrate {--path=}';
    protected $description = 'Ejecuta migraciones en todas las cooperativas';

    public function handle()
    {
        $path = $this->option('path');

        $coops = Cooperativa::all();

        foreach ($coops as $coop) {
            $this->info("Migrando tenant: {$coop->codigo}");

            app(TenantManager::class)->setFromCoopCode($coop->codigo);

            $params = [
                '--database' => 'tenant',
                '--force' => true,
            ];

            if ($path) {
                $params['--path'] = $path;
            }

            Artisan::call('migrate', $params);

            $this->info("✔ Migración completada para {$coop->codigo}");
        }

        $this->info('✔ Todas las cooperativas han sido migradas.');
    }
}
