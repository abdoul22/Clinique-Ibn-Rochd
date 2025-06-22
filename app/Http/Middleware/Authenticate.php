<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            if (Auth::check()) {
                $role = Auth::user()?->role?->name;

                return match ($role) {
                    'superadmin' => route('dashboard.superadmin'),
                    'admin' => route('dashboard.admin'),
                    // 'medecin' => route('dashboard.medecin'), ⚠️ Tu peux supprimer si inutile
                    default => route('login'),
                };
            }

            return route('login');
        }

        return null;
    }
}
