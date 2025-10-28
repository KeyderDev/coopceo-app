<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroCompra extends Model
{
    use HasFactory;

    protected $table = 'registro_compras';

    protected $fillable = [
        'descripcion',
        'total',
        'fecha_compra',
        'proveedor',
        'metodo_pago',
    ];

    protected $casts = [
        'fecha_compra' => 'date',
        'total' => 'decimal:2',
    ];
}
