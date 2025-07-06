<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        // Utiliser Auth::check() au lieu de auth()->check()
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Vérifier que l'utilisateur a un rôle
        if (!$user->role) {
            abort(403, 'Aucun rôle assigné à cet utilisateur.');
        }

        // Vérifier l'approbation
        if (!$user->is_approved) {
            abort(403, 'Votre compte n\'a pas encore été approuvé par un Admin.');
        }

        // Vérifier le rôle - gérer plusieurs rôles séparés par des virgules
        $allowedRoles = explode(',', $role);

        if (!in_array($user->role->name, $allowedRoles)) {
            // Au lieu de abort(), rediriger vers le bon dashboard
            return match ($user->role->name) {
                'superadmin' => redirect()->route('dashboard.superadmin'),
                'admin' => redirect()->route('dashboard.admin'),
                'user' => redirect()->route('dashboard.user'),
                default => abort(403, 'Rôle non reconnu.')
            };
        }

        return $next($request);
    }
}
