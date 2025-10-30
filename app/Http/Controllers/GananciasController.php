<?php

namespace App\Http\Controllers;

use App\Models\RegistroCompra;
use Illuminate\Http\Request;
use App\Models\Sale; // tu modelo de ventas

class GananciasController extends Controller
{
    public function index(Request $request)
    {
        $fecha = $request->query('fecha'); // obtener fecha si viene en query

        // Filtrar compras por fecha_compra si se especifica
        $comprasQuery = RegistroCompra::query();
        if ($fecha) {
            $comprasQuery->whereDate('fecha_compra', $fecha);
        }
        $totalCompras = $comprasQuery->sum('total');

        // Filtrar ventas por fecha si se especifica
        $ventasQuery = Sale::query();
        if ($fecha) {
            $ventasQuery->whereDate('created_at', $fecha); // aquí sigues usando created_at para ventas
        }
        $totalVentas = $ventasQuery->sum('total');

        // Ventas por método
        $totalEfectivo = (clone $ventasQuery)->where('metodo_pago', 'efectivo')->sum('total');
        $totalAthMovil = (clone $ventasQuery)->where('metodo_pago', 'athmovil')->sum('total');

        // Número de transacciones por método
        $transaccionesEfectivo = (clone $ventasQuery)->where('metodo_pago', 'efectivo')->count();
        $transaccionesAthMovil = (clone $ventasQuery)->where('metodo_pago', 'athmovil')->count();

        $gananciaNeta = $totalVentas - $totalCompras;

        return response()->json([
            'total_compras' => $totalCompras,
            'total_ventas' => $totalVentas,
            'total_efectivo' => $totalEfectivo,
            'total_ath_movil' => $totalAthMovil,
            'transacciones_efectivo' => $transaccionesEfectivo,
            'transacciones_ath_movil' => $transaccionesAthMovil,
            'ganancia_neta' => $gananciaNeta
        ]);
    }
}
