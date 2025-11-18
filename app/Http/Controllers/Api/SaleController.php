<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Mail\OrderCompletedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

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
            'productos.*.quantity' => 'required|integer|min:1',
            'total' => 'required|numeric',
            'metodo_pago' => 'required|string'
        ]);

        DB::beginTransaction();

        try {
            $sale = Sale::create([
                'cliente_id' => $request->cliente_id,
                'cajero_id' => $request->cajero_id,
                'total' => $request->total,
                'metodo_pago' => $request->metodo_pago
            ]);

            foreach ($request->productos as $producto) {
                $product = Product::find($producto['product_id']);

                if ($product->stock < $producto['quantity']) {
                    throw new \Exception("Stock insuficiente para el producto {$product->nombre}");
                }

                $subtotal = $product->precio * $producto['quantity'];

                $sale->products()->attach($product->id, [
                    'quantity' => $producto['quantity'],
                ]);

                $product->decrement('stock', $producto['quantity']);
            }

            if ($sale->cliente) {
                $dividendosSumar = $request->total * 0.3322785;
                $sale->cliente->dividendos += $dividendosSumar;
                $sale->cliente->save();

                Mail::to($sale->cliente->email)
                    ->send(new OrderCompletedMail($sale, $sale->cliente));
            }

            DB::commit();

            return response()->json([
                'message' => 'Venta registrada correctamente',
                'venta' => $sale->load('products', 'cliente', 'cajero'),
                'dividendos_actuales' => $sale->cliente ? $sale->cliente->dividendos : null
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $sale = Sale::with(['cliente', 'cajero', 'products'])->find($id);

        if (!$sale) {
            return response()->json(['error' => 'Venta no encontrada'], 404);
        }

        return response()->json([
            'sale' => $sale,
            'items' => $sale->products->map(function ($p) {
                return [
                    'id' => $p->id,
                    'nombre' => $p->nombre,
                    'quantity' => $p->pivot->quantity
                ];
            })
        ]);
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
