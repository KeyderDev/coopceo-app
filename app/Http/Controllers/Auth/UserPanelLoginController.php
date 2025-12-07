<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UsuarioGlobal;
use App\Models\User;
use App\Services\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserPanelLoginController extends Controller
{
    protected $tenant;

    public function __construct(TenantManager $tenant)
    {
        $this->tenant = $tenant;
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $map = UsuarioGlobal::where('email', $request->email)->first();

        if (!$map) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        $coopCodigo = $map->coop_codigo;

        $this->tenant->setFromCoopCode($coopCodigo);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        $token = Str::random(60);

        $user->api_token = $token;
        $user->save();

        return response()->json([
            'token' => $token,
            'coop_codigo' => $coopCodigo
        ]);
    }
}
