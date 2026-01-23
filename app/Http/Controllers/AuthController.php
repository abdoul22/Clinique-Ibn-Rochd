<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            /** @var User $user */
            $user = Auth::user();
            $user->load('role');

            // Vérifier si l'utilisateur est approuvé
            if (!$user->is_approved) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Votre compte est en attente d\'approbation.');
            }

            // Vérifier si l'utilisateur a un rôle assigné
            if (!$user->role || !$user->role_id) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Votre compte a été approuvé mais aucun rôle ne vous a été assigné. Veuillez contacter l\'administrateur.');
            }

            // Vérifier si l'utilisateur a une fonction assignée (sauf pour les superadmins)
            if ($user->role->name !== 'superadmin' && !$user->fonction) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('error', 'Votre compte a été approuvé mais aucune fonction ne vous a été assignée. Veuillez contacter l\'administrateur.');
            }

            // Mettre à jour la date de dernière connexion
            $user->last_login_at = now();
            $user->save();

            $role = $user->role->name;

            return match ($role) {
                'superadmin' => redirect()->route('dashboard.superadmin'),
                'admin'      => redirect()->route('dashboard.admin'),
                'medecin'    => redirect()->route('medecin.dashboard'),
                'patient'    => redirect()->route('dashboard.patient'),
                default      => redirect('/')->with('error', 'Rôle non reconnu'),
            };
        }

        return back()->withErrors([
            'email' => 'Les identifiants sont incorrects.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Déconnexion réussie.');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        $role = Role::where('name', 'admin')->first();

        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'role_id'     => $role?->id ?? 2,  // ID par défaut pour admin
            'is_approved' => false, // En attente d'approbation par le superadmin
        ]);

        return redirect()->route('login')->with('success', 'Inscription réussie. Veuillez attendre l\'approbation d\'un administrateur.');
    }
}
