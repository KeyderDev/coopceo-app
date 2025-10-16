<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use App\Mail\OrderCompletedMail;
use Illuminate\Support\Facades\Mail;

class SaleController extends Controller
{
    public function index()
    {
        return Sale::with(['cliente', 'cajero', 'products'])->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'nullable|exists:users,id',
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

        if ($sale->cliente) {
            $dividendosSumar = $request->total * 0.3322785;
            $sale->cliente->dividendos += $dividendosSumar;
            $sale->cliente->save();

            Mail::to($sale->cliente->email)
                ->send(new OrderCompletedMail($sale, $sale->cliente));
        }

        foreach ($request->productos as $producto) {
            $sale->products()->attach($producto['product_id']);
        }

        return response()->json([
            'venta' => $sale->load('products', 'cliente', 'cajero'),
            'dividendos_actuales' => $sale->cliente ? $sale->cliente->dividendos : null
        ], 201);
    }


    public function myTransactions(Request $request)
    {
        $user = $request->user();

        return Sale::with(['cajero', 'products'])
            ->where('cliente_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

}
