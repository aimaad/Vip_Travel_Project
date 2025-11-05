<?php



namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleCheck
{
    public function handle(Request $request, Closure $next)
{
    $user = Auth::user();

    if ($user && $user->role_id != 1) {
        return redirect('/')->with('error', 'Vous n\'avez pas les autorisations nécessaires pour accéder à cette page.');
    }

    return $next($request);
}

}
