<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Devuelve los datos del usuario autenticado
    public function me(Request $request)
    {
        $user = Auth::user();

        // Puedes agregar balance u otros campos aquí
        return response()->json([
            'id' => $user->id,
            'nombre' => $user->nombre,
            'apellido' => $user->apellido,
            'numero_socio' => $user->numero_socio,
            'dividendos' => $user->dividendos ?? 0,
            'email' => $user->email,
        ]);
    }
}
