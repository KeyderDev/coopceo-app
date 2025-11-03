<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class ProductController extends Controller
{
    public function index()
    {
        return Product::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric',
            'categoria' => 'required|string|max:255',
        ]);

        $producto = Product::create($request->all());
        return response()->json($producto, 201);
    }

    public function updateStock(Request $request, Product $product) {
    $request->validate([
        'stock' => 'required|integer|min:0'
    ]);

    $product->stock = $request->stock;
    $product->save();

    return response()->json($product);
}

    public function destroy($id)
{
    $producto = Product::find($id);

    if (!$producto) {
        return response()->json(['message' => 'Producto no encontrado'], 404);
    }

    $producto->delete();

    return response()->json(['message' => 'Producto eliminado correctamente']);
}

}
