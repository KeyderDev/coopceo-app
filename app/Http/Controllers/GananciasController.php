<?php

namespace App\Http\Controllers;

use App\Models\RegistroCompra;
use Illuminate\Http\Request;
use App\Models\Sale; // tu modelo de ventas

class GananciasController extends Controller
{
    public function index()
    {
        // Total de compras
        $totalCompras = RegistroCompra::sum('total');

        // Total de ventas
        $totalVentas = Sale::sum('total');

        // Ganancia o pérdida
        $gananciaNeta = $totalVentas - $totalCompras;

        return response()->json([
            'total_compras' => $totalCompras,
            'total_ventas' => $totalVentas,
            'ganancia_neta' => $gananciaNeta
        ]);
    }
}
