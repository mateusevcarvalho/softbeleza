<?php

namespace App\Http\Middleware;

use App\Managers\TenantManager;
use Closure;

class Tenant
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        TenantManager::set(auth()->user());
        return $next($request);
    }
}
