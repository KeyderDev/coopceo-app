<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cooperativa extends Model
{
    protected $connection = 'mysql_main';
    protected $table = 'cooperativas';
    protected $fillable = [
        'nombre',
        'codigo',
        'db_name',
        'db_user',
        'db_pass'
    ];
}
