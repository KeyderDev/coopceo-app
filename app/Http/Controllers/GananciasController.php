<?php

namespace App\Http\Controllers;

use App\Models\RegistroCompra;
use Illuminate\Http\Request;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class GananciasController extends Controller
{
    public function index(Request $request)
    {
        $fecha = $request->query('fecha');

        $comprasQuery = RegistroCompra::query();
        if ($fecha) {
            $comprasQuery->whereDate('fecha_compra', $fecha);
        }
        $totalCompras = $comprasQuery->sum('total');

        
        $ventasQuery = Sale::query();
        if ($fecha) {
            $ventasQuery->whereDate('created_at', $fecha);
        }
        $totalVentas = $ventasQuery->sum('total');

        $totalEfectivo = (clone $ventasQuery)->where('metodo_pago', 'efectivo')->sum('total');
        $totalAthMovil = (clone $ventasQuery)->where('metodo_pago', 'athmovil')->sum('total');

        $transaccionesEfectivo = (clone $ventasQuery)->where('metodo_pago', 'efectivo')->count();
        $transaccionesAthMovil = (clone $ventasQuery)->where('metodo_pago', 'athmovil')->count();

        $gananciaNeta = $totalVentas - $totalCompras;

        $prodMasVendido = DB::table('sale_product')
            ->select('product_id', DB::raw('SUM(quantity) as total_vendidos'))
            ->groupBy('product_id')
            ->orderByDesc('total_vendidos')
            ->first();

        if ($prodMasVendido) {
            $productoGeneral = Product::find($prodMasVendido->product_id);
            $productoMasVendido = [
                "producto" => $productoGeneral ? $productoGeneral->nombre : "Desconocido",
                "cantidad" => $prodMasVendido->total_vendidos
            ];
        } else {
            $productoMasVendido = null;
        }

        if ($fecha) {
            $prodMasVendidoDia = DB::table('sale_product')
                ->join('sales', 'sales.id', '=', 'sale_product.sale_id')
                ->select('product_id', DB::raw('SUM(quantity) as total_vendidos'))
                ->whereDate('sales.created_at', $fecha)
                ->groupBy('product_id')
                ->orderByDesc('total_vendidos')
                ->first();

            if ($prodMasVendidoDia) {
                $productoDia = Product::find($prodMasVendidoDia->product_id);
                $productoMasVendidoDia = [
                    "producto" => $productoDia ? $productoDia->nombre : "Desconocido",
                    "cantidad" => $prodMasVendidoDia->total_vendidos
                ];
            } else {
                $productoMasVendidoDia = null;
            }
        } else {
            $productoMasVendidoDia = null;
        }

        return response()->json([
            'total_compras' => $totalCompras,
            'total_ventas' => $totalVentas,
            'total_efectivo' => $totalEfectivo,
            'total_ath_movil' => $totalAthMovil,
            'transacciones_efectivo' => $transaccionesEfectivo,
            'transacciones_ath_movil' => $transaccionesAthMovil,
            'ganancia_neta' => $gananciaNeta,

            'producto_mas_vendido' => $productoMasVendido,
            'producto_mas_vendido_dia' => $productoMasVendidoDia,
        ]);
    }
}
