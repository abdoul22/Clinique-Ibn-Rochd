<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserApproved
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Les superadmins ont toujours accès, même s'ils ne sont pas approuvés
        if ($user && $user->role && $user->role->name === 'superadmin') {
            return $next($request);
        }

        if ($user && (!isset($user->is_approved) || !$user->is_approved)) {
            // Rediriger vers la page d'attente d'approbation au lieu d'afficher une erreur 403
            return redirect()->route('approval.waiting');
        }

        return $next($request);
    }
}
