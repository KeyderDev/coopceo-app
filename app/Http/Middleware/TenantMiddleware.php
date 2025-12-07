<?php

namespace App\Http\Middleware;

use App\Services\TenantManager;
use Closure;

class TenantMiddleware
{
    protected $tenant;

    public function __construct(TenantManager $tenant)
    {
        $this->tenant = $tenant;
    }

    public function handle($request, Closure $next)
    {
        if (!$request->is('api/*')) {
            return $next($request);
        }

        if ($request->is('api/register') || $request->is('api/login')) {
            return $next($request);
        }

        \Log::info('HEADERS RECIBIDOS', $request->headers->all());

        $codigo = $request->header('X-Coop-Code');

        if ($codigo) {
            $this->tenant->setFromCoopCode($codigo);
        } else {
            \Log::warning("⚠ No llegó X-Coop-Code en una ruta que sí usa tenant");
        }

        return $next($request);
    }
}
