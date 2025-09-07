<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CashReconciliation;
use App\Models\Sale;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CashReconciliationController extends Controller
{
    // Guardar cuadre de caja
    public function store(Request $request)
    {
        $data = $request->validate([
            'petty' => 'required|numeric',
            'bill_20' => 'required|integer',
            'bill_10' => 'required|integer',
            'bill_5' => 'required|integer',
            'bill_1' => 'required|integer',
            'coin_10' => 'required|integer',
            'coin_5' => 'required|integer',
            'coin_1' => 'required|integer',
            'coin_25' => 'required|integer',
            'total_counted' => 'required|numeric',
            'total_sales_cash' => 'required|numeric',
            'difference' => 'required|numeric',
        ]);

        $today = Carbon::today()->toDateString();

        // Evitar duplicados
        $existing = CashReconciliation::whereDate('created_at', $today)->first();
        if ($existing) {
            return response()->json([
                'message' => 'Ya existe un cuadre registrado para el día de hoy.',
                'data' => $existing
            ], 400);
        }

        $reconciliation = CashReconciliation::create($data);

        return response()->json([
            'message' => 'Cuadre guardado correctamente',
            'data' => $reconciliation
        ]);
    }
    public function index()
    {
        // Traer todos los cuadres
        $cuadres = CashReconciliation::orderBy('created_at', 'desc')->get();
        return response()->json($cuadres);
    }
    // Traer total de ventas en efectivo del día actual
    public function totalSales(Request $request)
    {
        try {
            $date = Carbon::today('America/Puerto_Rico');
            $start = $date->copy()->startOfDay();
            $end = $date->copy()->endOfDay();

            $total = Sale::where('metodo_pago', 'efectivo')
                ->whereBetween('created_at', [$start, $end])
                ->sum('total');

            $sales = Sale::where('metodo_pago', 'efectivo')
                ->whereBetween('created_at', [$start, $end])
                ->get();

            Log::info('Ventas de hoy:', $sales->map(fn($s) => [
                'total' => $s->total,
                'metodo_pago' => $s->metodo_pago,
                'hora' => $s->created_at->toDateTimeString()
            ])->toArray());

            return response()->json([
                'total_cash' => round((float) $total, 2)
            ]);

        } catch (\Exception $e) {
            Log::error('Error en totalSales: ' . $e->getMessage());
            return response()->json([
                'message' => 'Ocurrió un error al calcular el total de ventas.'
            ], 500);
        }
    }

}

