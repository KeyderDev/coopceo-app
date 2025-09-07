<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index()
    {
        return Sale::with(['cliente', 'cajero', 'products'])->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:users,id',
            'cajero_id' => 'required|exists:users,id',
            'productos' => 'required|array|min:1',
            'productos.*.product_id' => 'required|exists:products,id',
            'total' => 'required|numeric',
            'metodo_pago' => 'required|string'
        ]);

        $sale = Sale::create([
            'cliente_id' => $request->cliente_id,
            'cajero_id' => $request->cajero_id,
            'total' => $request->total,
            'metodo_pago' => $request->metodo_pago
        ]);

        $productIds = collect($request->productos)->pluck('product_id');
        $sale->products()->sync($productIds);

        $cliente = $sale->cliente; // relación cliente en el modelo Sale
        $dividendosSumar = $request->total * 0.3322785;
        $cliente->dividendos += $dividendosSumar;
        $cliente->save();

        return response()->json([
            'venta' => $sale->load('products', 'cliente', 'cajero'),
            'dividendos_actuales' => $cliente->dividendos
        ], 201);
    }


}
