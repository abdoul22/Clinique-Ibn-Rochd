<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsApproved
{
    public function handle($request, Closure $next)
    {
        // Ne rien faire si l'utilisateur n'est pas connecté
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Les superadmins ont toujours accès, même s'ils ne sont pas approuvés
        if ($user->role && $user->role->name === 'superadmin') {
            return $next($request);
        }

        // Si l'utilisateur est connecté mais non approuvé
        if (!$user->is_approved) {
            // Ne redirige pas vers login directement pour éviter la boucle
            return redirect()->route('approval.waiting');
        }

        return $next($request);
    }
}
