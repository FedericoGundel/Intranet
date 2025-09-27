<?php 

// app/Http/Middleware/RoutePermissionMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class RoutePermissionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $routeName = Route::currentRouteName(); // p.ej. "dashboard"
        if ($routeName) {
            $perm = "ver.$routeName";
            if (! $request->user()?->can($perm)) {
                abort(403);
            }
        }
        return $next($request);
    }
}
