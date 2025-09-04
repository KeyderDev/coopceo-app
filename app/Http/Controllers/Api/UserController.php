<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
}
