<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Http\Controllers\Modules\Flight\Admin\OfferController;

class EnsureOfferCreated
{
    public function handle(Request $request, Closure $next)
    {
        // Vérifie si une offre a été créée dans la session
        if (!Session::has('offer_created')) {
            return redirect()->action([OfferController::class, 'create'])->with('warning', 'Veuillez d\'abord créer une offre.');
            
        }

        return $next($request);
    }
}
