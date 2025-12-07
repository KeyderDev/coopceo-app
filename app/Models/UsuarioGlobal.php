<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioGlobal extends Model
{
    protected $connection = 'mysql_main';
    protected $table = 'usuarios_global';
    protected $fillable = [
        'email',
        'coop_codigo'
    ];
}
