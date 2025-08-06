<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UpdateLastLoginAt
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Mettre à jour la date de dernière connexion seulement si l'utilisateur est connecté
        // et seulement sur certaines routes (pas sur toutes les requêtes)
        if (Auth::check() && $this->shouldUpdateLastLogin($request)) {
            User::where('id', Auth::id())->update(['last_login_at' => now()]);
        }

        return $response;
    }

    /**
     * Détermine si on doit mettre à jour last_login_at
     */
    private function shouldUpdateLastLogin(Request $request): bool
    {
        // Ne pas mettre à jour sur les requêtes AJAX, API, ou assets
        if ($request->ajax() || $request->is('api/*') || $request->is('assets/*')) {
            return false;
        }

        // Ne pas mettre à jour sur les requêtes de fichiers statiques
        if ($request->is('*.css') || $request->is('*.js') || $request->is('*.png') || $request->is('*.jpg') || $request->is('*.ico')) {
            return false;
        }

        // Ne mettre à jour que sur les vraies pages (pas les requêtes de fond)
        $route = $request->route();
        if (!$route) {
            return false;
        }

        // Mettre à jour seulement sur les routes principales
        $routeName = $route->getName();
        if ($routeName && (str_contains($routeName, 'api.') || str_contains($routeName, 'assets.'))) {
            return false;
        }

        return true;
    }
}
