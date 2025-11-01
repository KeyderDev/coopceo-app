<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\User;
use App\Models\RegistroCompra;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class MonthlyReportController extends Controller
{
    public function resumenMensual($mes)
    {
        // Fechas de inicio y fin del mes
        $fechaInicio = Carbon::parse($mes . '-01')->startOfMonth();
        $fechaFin = Carbon::parse($mes . '-01')->endOfMonth();

        // Ventas del mes
        $ventas = Sale::whereBetween('created_at', [$fechaInicio, $fechaFin])->get();

        $totalVentas = $ventas->sum('total') ?? 0;
        $totalEfectivo = $ventas->where('metodo_pago', 'efectivo')->sum('total') ?? 0;
        $totalAth = $ventas->where('metodo_pago', 'athmovil')->sum('total') ?? 0;

        $transaccionesEfectivo = $ventas->where('metodo_pago', 'efectivo')->count() ?? 0;
        $transaccionesAth = $ventas->where('metodo_pago', 'athmovil')->count() ?? 0;

        // Cajero con más transacciones
        $cajeroMasActivo = Sale::select('users.nombre', DB::raw('COUNT(sales.id) as total'))
            ->join('users', 'sales.cajero_id', '=', 'users.id')
            ->whereBetween('sales.created_at', [$fechaInicio, $fechaFin])
            ->groupBy('users.nombre')
            ->orderByDesc('total')
            ->first();

        $nombreCajero = $cajeroMasActivo ? mb_convert_encoding($cajeroMasActivo->nombre, 'UTF-8', 'UTF-8') : 'N/A';
        // Usuario que más compró en el mes
$usuarioMasCompras = Sale::select('users.nombre', DB::raw('SUM(sales.total) as total_comprado'))
    ->join('users', 'sales.cliente_id', '=', 'users.id')
    ->whereBetween('sales.created_at', [$fechaInicio, $fechaFin])
    ->groupBy('users.nombre')
    ->orderByDesc('total_comprado')
    ->first();


        $nombreUsuarioMasCompras = $usuarioMasCompras
            ? mb_convert_encoding($usuarioMasCompras->nombre, 'UTF-8', 'UTF-8')
            : 'N/A';

        // Total compras del mes (para calcular ganancia neta)
        $totalCompras = RegistroCompra::whereBetween('fecha_compra', [$fechaInicio, $fechaFin])
            ->sum('total') ?? 0;

        // Ganancia neta
        $gananciaNeta = $totalVentas - $totalCompras;

        // Preparar logo en Base64 para DomPDF
        $logoPath = public_path('images/coopceopng.png');
        $base64Logo = null;
        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            $base64Logo = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        // Datos para la vista
        $data = [
            'mes' => $fechaInicio->format('F Y'),
            'totalVentas' => $totalVentas,
            'totalEfectivo' => $totalEfectivo,
            'totalAth' => $totalAth,
            'transaccionesEfectivo' => $transaccionesEfectivo,
            'transaccionesAth' => $transaccionesAth,
            'cajeroMasActivo' => $nombreCajero,
            'usuarioMasCompras' => $nombreUsuarioMasCompras,
            'totalCompras' => $totalCompras,
            'gananciaNeta' => $gananciaNeta,
            'logoBase64' => $base64Logo,
        ];

        // Generar PDF
        $pdf = Pdf::loadView('pdf.resumen_mensual', $data);

        return $pdf->download("resumen_mensual_{$mes}.pdf");
    }
}
