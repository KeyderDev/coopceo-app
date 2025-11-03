<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCuadre;

class ProductCuadreController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'cuadre' => 'required|array',
            'cuadre.*.id' => 'required|exists:products,id',
            'cuadre.*.stock' => 'required|integer|min:0',
            'cuadre.*.contado' => 'nullable|integer|min:0',
        ]);

        foreach ($request->cuadre as $item) {
            ProductCuadre::updateOrCreate(
                ['product_id' => $item['id']],
                ['stock' => $item['stock'], 'contado' => $item['contado']]
            );
        }

        return response()->json(['message' => 'Cuadre guardado correctamente']);
    }
}
