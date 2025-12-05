<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{
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
            'posicion' => $user->posicion,
        ]);
    }

    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'nombre' => 'nullable|string|max:255',
                'apellido' => 'nullable|string|max:255',
                'telefono' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'admin' => 'nullable|boolean',
                'numero_socio' => 'nullable|numeric|unique:users,numero_socio,' . $id,
            ]);

            $user->update($validated);

            return response()->json([
                'message' => 'Usuario actualizado correctamente',
                'user' => $user
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error al actualizar usuario: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al actualizar usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function show($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json(['error' => 'Usuario no encontrado'], 404);
            }

            return response()->json($user, 200);
        } catch (\Exception $e) {
            \Log::error('Error al obtener usuario: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
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
            $user->sales()->delete();
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
