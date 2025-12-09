<?php

namespace App\Services;

use App\Models\Cooperativa;
use Illuminate\Support\Facades\DB;

class TenantManager
{
    protected $coop;

    public function setFromCoopCode(string $codigo)
    {
        $coop = Cooperativa::where('codigo', $codigo)->firstOrFail();

        DB::purge('tenant');

        config([
            'database.connections.tenant.driver' => 'mysql',
            'database.connections.tenant.host' => config('database.connections.mysql_main.host'),
            'database.connections.tenant.port' => config('database.connections.mysql_main.port'),
            'database.connections.tenant.database' => $coop->db_name,
            'database.connections.tenant.username' => $coop->db_user,
            'database.connections.tenant.password' => $coop->db_pass,
        ]);

        config(['database.default' => 'tenant']);

        DB::reconnect('tenant');
        DB::connection('tenant')->getPdo();

        $this->coop = $coop;
    }
    public function getCoop()
    {
        return $this->coop;
    }

        public function useTenant(string $codigo)
    {
        return $this->setFromCoopCode($codigo);
    }
}
