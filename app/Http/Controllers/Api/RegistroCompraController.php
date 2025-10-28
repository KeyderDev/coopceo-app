<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\RegistroCompra;
use Illuminate\Http\Request;

class RegistroCompraController extends Controller
{
    /**
     * Mostrar todas las compras
     */
    public function index()
    {
        $compras = RegistroCompra::orderBy('fecha_compra', 'desc')->get();
        return response()->json($compras);
    }

    /**
     * Guardar una nueva compra
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'descripcion' => 'nullable|string|max:255',
            'total' => 'required|numeric|min:0',
            'fecha_compra' => 'nullable|date',
            'proveedor' => 'nullable|string|max:255',
            'metodo_pago' => 'nullable|string|max:255',
        ]);

        $compra = RegistroCompra::create($validated);

        return response()->json([
            'message' => 'Registro de compra creado correctamente.',
            'data' => $compra
        ], 201);
    }

    /**
     * Mostrar una compra específica
     */
    public function show($id)
    {
        $compra = RegistroCompra::find($id);

        if (!$compra) {
            return response()->json(['message' => 'Compra no encontrada'], 404);
        }

        return response()->json($compra);
    }

    /**
     * Actualizar una compra
     */
    public function update(Request $request, $id)
    {
        $compra = RegistroCompra::find($id);

        if (!$compra) {
            return response()->json(['message' => 'Compra no encontrada'], 404);
        }

        $validated = $request->validate([
            'descripcion' => 'nullable|string|max:255',
            'total' => 'nullable|numeric|min:0',
            'fecha_compra' => 'nullable|date',
            'proveedor' => 'nullable|string|max:255',
            'metodo_pago' => 'nullable|string|max:255',
        ]);

        $compra->update($validated);

        return response()->json([
            'message' => 'Registro de compra actualizado correctamente.',
            'data' => $compra
        ]);
    }

    /**
     * Eliminar una compra
     */
    public function destroy($id)
    {
        $compra = RegistroCompra::find($id);

        if (!$compra) {
            return response()->json(['message' => 'Compra no encontrada'], 404);
        }

        $compra->delete();

        return response()->json(['message' => 'Registro de compra eliminado correctamente.']);
    }
}
