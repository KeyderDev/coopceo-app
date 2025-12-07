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
        \Log::info('UserController me() DB', [
            'db' => config('database.connections.tenant.database'),
            'user' => config('database.connections.tenant.username'),
        ]);

        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token faltante'], 401);
        }

        $user = User::on('tenant')
            ->where('api_token', $token)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

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
        $users = User::on('tenant')->get();
        return response()->json($users);
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::on('tenant')->findOrFail($id);

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
            $user = User::on('tenant')->find($id);

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

        $tenantUserAuth = User::on('tenant')->where('api_token', $token)->first();

        if (!$tenantUserAuth) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        $mainDB = DB::connection('mysql_main');
        $tenantDB = DB::connection('tenant');

        $tenantUser = User::on('tenant')->find($id);

        if (!$tenantUser) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $globalUser = $mainDB->table('usuarios_global')->where('email', $tenantUser->email)->first();

        if (!$globalUser) {
            return response()->json(['message' => 'Usuario no existe en jucoop_main'], 404);
        }

        DB::beginTransaction();
        $tenantDB->beginTransaction();

        try {
            if (method_exists($tenantUser, 'sales')) {
                $tenantUser->sales()->delete();
            }

            $tenantUser->delete();

            $mainDB->table('usuarios_global')->where('email', $tenantUser->email)->delete();

            DB::commit();
            $tenantDB->commit();

            return response()->json(['message' => 'Usuario eliminado correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            $tenantDB->rollBack();
            return response()->json(['message' => 'Error al eliminar el usuario']);
        }
    }

}
