<?php

namespace App\Console\Commands;

use App\Models\UsuarioGlobal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncUsuariosGlobal extends Command
{
    protected $signature = 'jucoop:sync-usuarios-global';
    protected $description = 'Sincroniza usuarios actuales a usuarios_global';

    public function handle()
    {
        $users = DB::connection('mysql')->table('users')->get();

        foreach ($users as $user) {
            UsuarioGlobal::updateOrCreate(
                ['email' => $user->email],
                ['coop_codigo' => 'COO1']
            );
        }

        return 0;
    }
}
