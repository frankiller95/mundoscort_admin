<?
// En tu directorio de app/Http/Middleware

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAuth
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            // Si el usuario está autenticado, redirige a /inicio
            return redirect('/inicio');
        }

        // Si el usuario no está autenticado, permite continuar con la solicitud
        return $next($request);
    }
}
