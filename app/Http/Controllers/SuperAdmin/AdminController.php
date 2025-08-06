<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $superadmins = User::whereHas('role', function ($query) {
            $query->where('name', 'superadmin');
        })->with('role')->get();

        $admins = User::whereHas('role', function ($query) {
            $query->where('name', 'admin');
        })->with('role')->get();

        return view('superadmin.admins.index', compact('superadmins', 'admins'));
    }


    // Formulaire de création
    public function create()
    {
        return view('superadmin.admins.create');
    }

    // Enregistrement d’un nouvel admin
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => 2, // Admin
            'is_approved' => true,
        ]);

        return redirect()->route('superadmin.users.index')->with('success', 'Admin créé avec succès.');
    }

    // Affichage d’un admin
    public function show($id)
    {
        $admin = User::findOrFail($id);
        return view('superadmin.admins.show', compact('admin'));
    }

    // Formulaire de modification
    public function edit($id)
    {
        $admin = User::findOrFail($id);
        return view('superadmin.admins.edit', compact('admin'));
    }

    // Mise à jour
    public function update(Request $request, $id)
    {
        $admin = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
        ]);

        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('superadmin.users.index')->with('success', 'Admin mis à jour.');
    }

    // Suppression
    public function destroy($id)
    {
        $admin = User::findOrFail($id);
        $admin->delete();

        return redirect()->route('superadmin.users.index')->with('success', 'Admin supprimé.');
    }

    public function assignRole(Request $request, $id)
    {
        $admin = User::findOrFail($id);
        $fonction = $request->input('fonction');

        // Mettre à jour la fonction de l'utilisateur
        $admin->fonction = $fonction;
        $admin->save();

        // Synchroniser avec le module personnel
        $this->syncWithPersonnel($admin, $fonction);

        // Si l'utilisateur est approuvé et qu'on vient de lui attribuer une fonction,
        // créer automatiquement un personnel
        if ($admin->is_approved && $fonction) {
            $this->createPersonnelFromUser($admin);
        }

        return redirect()->back()->with('success', 'Fonction attribuée avec succès.');
    }

    /**
     * Synchroniser l'utilisateur avec le module personnel
     */
    private function syncWithPersonnel(User $user, string $fonction)
    {
        // Chercher si un personnel existe déjà avec ce nom
        $personnel = \App\Models\Personnel::where('nom', $user->name)->first();

        if ($personnel) {
            // Mettre à jour la fonction du personnel existant
            $personnel->update(['fonction' => $fonction]);
        } else {
            // Créer un nouveau personnel
            \App\Models\Personnel::create([
                'nom' => $user->name,
                'fonction' => $fonction,
                'telephone' => null, // À remplir manuellement si nécessaire
                'salaire' => 0, // À définir selon la fonction
                'is_approved' => $user->is_approved,
                'created_by' => \Illuminate\Support\Facades\Auth::id()
            ]);
        }
    }

    public function approve($id)
    {
        $admin = User::findOrFail($id);
        $admin->is_approved = true;
        $admin->save();

        // Créer automatiquement un personnel si l'utilisateur a une fonction
        if ($admin->fonction) {
            $this->createPersonnelFromUser($admin);
        }

        return redirect()->back()->with('success', 'Utilisateur approuvé avec succès.');
    }

    /**
     * Créer un personnel à partir d'un utilisateur approuvé
     */
    private function createPersonnelFromUser(User $user)
    {
        // Vérifier si un personnel existe déjà avec ce nom
        $existingPersonnel = \App\Models\Personnel::where('nom', $user->name)->first();

        if (!$existingPersonnel) {
            \App\Models\Personnel::create([
                'nom' => $user->name,
                'fonction' => $user->fonction,
                'telephone' => null, // À remplir manuellement
                'salaire' => 0, // À définir selon la fonction
                'is_approved' => $user->is_approved,
                'created_by' => \Illuminate\Support\Facades\Auth::id()
            ]);
        }
    }

    public function reject($id)
    {
        $admin = User::findOrFail($id);
        $admin->delete();

        return redirect()->back()->with('success', 'Utilisateur rejeté et supprimé.');
    }
}
