<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class ApiTokenMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token || !($user = User::where('api_token', $token)->first())) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        // Loguear usuario para $request->user()
        $request->setUserResolver(fn() => $user);

        return $next($request);
    }
}
