<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Log;
use App\Models\UsuarioGlobal;
use App\Services\TenantManager;

class AuthController extends Controller
{
    protected $tenant;

    public function __construct(TenantManager $tenant)
    {
        $this->tenant = $tenant;
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:50',
            'password' => 'required|string|min:6|confirmed',
            'coop_codigo' => 'required|string',
            'numero_socio' => 'nullable|numeric'
        ]);

        $map = UsuarioGlobal::where('email', $request->email)->first();

        if ($map) {
            return response()->json(['message' => 'El correo ya está registrado en otra cooperativa'], 422);
        }

        UsuarioGlobal::create([
            'email' => $request->email,
            'coop_codigo' => $request->coop_codigo
        ]);

        $this->tenant->setFromCoopCode($request->coop_codigo);

        $user = User::create([
            'email' => $request->email,
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'telefono' => $request->telefono,
            'numero_socio' => $request->numero_socio,
            'dividendos' => 0,
            'password' => Hash::make($request->password),
            'api_token' => Str::random(60)
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
            'coop_codigo' => $request->coop_codigo,
            'admin' => (int) ($user->admin ?? 0)
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $map = UsuarioGlobal::where('email', $request->email)->first();

        if (!$map) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        $coop = $map->coop_codigo;

        $this->tenant->setFromCoopCode($coop);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        $token = Str::random(60);
        $user->api_token = $token;
        $user->save();

        Log::create([
            'user_id' => $user->id,
            'event' => 'login',
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent')
        ]);

        return response()->json([
            'access_token' => $token,
            'user' => $user,
            'admin' => (int) ($user->admin ?? 0),
            'coop_codigo' => $coop
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
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        return response()->json([
            'id' => $user->id,
            'nombre' => $user->nombre,
            'apellido' => $user->apellido,
            'email' => $user->email,
            'numero_socio' => $user->numero_socio,
            'admin' => $user->admin,
            'dividendos' => $user->dividendos ?? 0
        ]);
    }
}
