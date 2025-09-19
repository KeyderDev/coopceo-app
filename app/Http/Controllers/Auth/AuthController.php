<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Log; 

class AuthController extends Controller
{
public function register(Request $request)
{
    $request->validate([
        'email' => 'required|email|unique:users,email',
        'nombre' => 'required|string|max:255',
        'apellido' => 'required|string|max:255',
        'telefono' => 'nullable|string|max:50',
        'password' => 'required|string|min:6|confirmed',
    ]);

    $user = User::create([
        'email' => $request->email,
        'nombre' => $request->nombre,
        'apellido' => $request->apellido,
        'telefono' => $request->telefono,
        'numero_socio' => $request->numero_socio,
        'dividendos' => 0,
        'password' => Hash::make($request->password),
        'api_token' => Str::random(60),
    ]);

    Log::create([
        'user_id' => $user->id,
        'event' => 'register', 
        'ip_address' => $request->ip(),
        'user_agent' => $request->header('User-Agent')
    ]);

    return response()->json([
        'message' => 'Usuario registrado correctamente',
        'user' => $user,
        'access_token' => $user->api_token,
    ], 201);
}
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales inválidas'
            ], 401);
        }

        $user->api_token = Str::random(60);
        $user->save();

        Log::create([
            'user_id' => $user->id,
            'event' => 'login',
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent')
        ]);

        return response()->json([
            'access_token' => $user->api_token,
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->api_token = null;
            $user->save();
        }

        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
