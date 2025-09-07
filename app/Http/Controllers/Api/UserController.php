<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
    // Devuelve los datos del usuario autenticado
    public function me(Request $request)
    {
        $user = Auth::user();

        return response()->json([
            'id' => $user->id,
            'nombre' => $user->nombre,
            'apellido' => $user->apellido,
            'numero_socio' => $user->numero_socio,
            'admin' => $user->admin,
            'dividendos' => $user->dividendos ?? 0,
            'email' => $user->email,
        ]);
    }

    // Devuelve todos los usuarios
    public function index()
    {
        $users = User::all(); 
        return response()->json($users);
    }

public function destroy(Request $request, $id)
{
    $token = $request->bearerToken();
    $authUser = User::where('api_token', $token)->first();

    if (!$authUser) {
        return response()->json(['message' => 'No autorizado'], 401);
    }

    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'Usuario no encontrado'], 404);
    }

    DB::beginTransaction();

    try {
        // Ejemplo: eliminar relaciones HasMany
        $user->transactions()->delete();
        $user->posts()->delete();

        // Ejemplo: eliminar relaciones BelongsToMany
        $user->roles()->detach();

        // Eliminar usuario
        $user->delete();

        DB::commit();

        return response()->json(['message' => 'Usuario eliminado correctamente']);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Error al eliminar el usuario',
            'error' => $e->getMessage()
        ], 500);
    }
}



}
