<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ApiTokenMiddleware
{
    public function handle($request, Closure $next)
    {
        if ($request->is('api/login') || $request->is('api/register')) {
            return $next($request);
        }

        $token = $request->bearerToken();

        Log::info("APITOKEN | Token recibido", [
            'token' => $token,
            'X-Coop-Code' => $request->header('X-Coop-Code'),
            'tenant_db' => config('database.connections.tenant.database'),
            'default_db' => config('database.default'),
        ]);

        if (!$token) {
            Log::warning("APITOKEN | No se envió token");
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $user = User::where('api_token', $token)->first();

        Log::info("APITOKEN | Resultado consulta user", [
            'found' => $user ? true : false,
            'user_id' => $user ? $user->id : null,
            'user_email' => $user ? $user->email : null,
            'connection_used' => $user ? $user->getConnectionName() : 'none'
        ]);

        if (!$user) {
            Log::warning("APITOKEN | Token inválido en tenant", [
                'token' => $token,
                'tenant_db' => config('database.connections.tenant.database'),
            ]);
            return response()->json(['message' => 'No autenticado'], 401);
        }

        auth()->setUser($user);

        Log::info("APITOKEN | Usuario autenticado OK", [
            'user_id' => $user->id,
            'tenant_db' => config('database.connections.tenant.database'),
        ]);

        return $next($request);
    }
}
