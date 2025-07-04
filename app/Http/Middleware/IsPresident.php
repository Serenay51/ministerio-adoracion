<?php

// app/Http/Middleware/IsPresident.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsPresident
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->is_president) {
            return $next($request);
        }

        abort(403, 'Acceso solo para el presidente del ministerio.');
    }
}
