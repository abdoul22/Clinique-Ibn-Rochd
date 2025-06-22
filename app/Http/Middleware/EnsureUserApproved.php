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

        if ($user && (!isset($user->is_approved) || !$user->is_approved)) {
            abort(403, 'Votre compte n\'a pas encore été approuvé par un Admin.');
        }

        return $next($request);
    }
}
