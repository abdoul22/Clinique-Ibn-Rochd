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
        })->with('role', 'medecin')->get();

        // NOUVEAU : Récupérer aussi les utilisateurs médecins
        $medecins = User::whereHas('role', function ($query) {
            $query->where('name', 'medecin');
        })->with('role', 'medecin')->get();

        // NOUVEAU : Récupérer la liste de tous les médecins pour le dropdown
        $medecinsList = \App\Models\Medecin::where('statut', 'actif')
            ->orderByRaw("FIELD(fonction, 'Pr', 'Dr', 'Tss', 'SGF', 'IDE')")
            ->orderBy('nom')
            ->get();

        return view('superadmin.admins.index', compact('superadmins', 'admins', 'medecins', 'medecinsList'));
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

        return redirect()->route('superadmin.admins.index')->with('success', 'Admin créé avec succès.');
    }

    // Affichage d'un admin
    public function show($id)
    {
        $admin = User::with('role', 'medecin')->findOrFail($id);
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

        $fonction = $request->input('fonction');
        $medecinId = $request->input('medecin_id');

        // Validation conditionnelle : si fonction='Médecin', medecin_id est requis
        $validationRules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
        ];

        if ($fonction === 'Médecin') {
            // Vérifier que le rôle 'medecin' existe
            $medecinRole = \App\Models\Role::where('name', 'medecin')->first();
            if (!$medecinRole) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['fonction' => 'Le rôle "medecin" n\'existe pas dans la base de données. Veuillez d\'abord créer ce rôle.']);
            }

            // Require medecin_id when fonction is 'Médecin'
            $validationRules['medecin_id'] = 'required|exists:medecins,id';
        } else {
            $validationRules['medecin_id'] = 'nullable|exists:medecins,id';
        }

        $request->validate($validationRules);

        $isApproved = $request->has('is_approved');

        // Gérer le changement de rôle en fonction de la fonction
        if ($fonction === 'Médecin') {
            // Si la fonction est "Médecin", assigner le rôle medecin
            $medecinRoleId = \App\Models\Role::where('name', 'medecin')->first()?->id;

            // Cette vérification ne devrait jamais échouer car on l'a déjà vérifiée dans la validation
            if (!$medecinRoleId) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['fonction' => 'Le rôle "medecin" n\'existe pas dans la base de données.']);
            }

            $admin->role_id = $medecinRoleId;
            // medecin_id est maintenant garanti d'être présent grâce à la validation
            $admin->medecin_id = $medecinId;
        } else {
            // Si la fonction n'est PAS "Médecin" et que l'utilisateur avait le rôle medecin
            // Le repasser en admin et retirer l'association medecin_id
            if ($admin->role && $admin->role->name === 'medecin') {
                $adminRole = \App\Models\Role::where('name', 'admin')->first();
                
                if (!$adminRole) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['fonction' => 'Le rôle "admin" n\'existe pas dans la base de données. Veuillez d\'abord créer ce rôle.']);
                }

                $admin->role_id = $adminRole->id;
                $admin->medecin_id = null; // Retirer l'association avec le profil médecin
            }
        }

        // Mettre à jour les informations de base
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->fonction = $fonction;
        $admin->is_approved = $isApproved;
        $admin->save();

        // Synchroniser avec le personnel si ce n'est pas un médecin
        if ($fonction && $fonction !== 'Médecin') {
            $this->syncWithPersonnel($admin, $fonction);
        }

        return redirect()->route('superadmin.admins.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    // Suppression
    public function destroy($id)
    {
        $admin = User::findOrFail($id);

        // Supprimer automatiquement le personnel associé s'il existe
        // D'abord chercher par user_id, puis par nom pour compatibilité
        $personnel = \App\Models\Personnel::where('user_id', $admin->id)->first();
        if (!$personnel) {
            $personnel = \App\Models\Personnel::where('nom', $admin->name)->whereNull('user_id')->first();
        }

        if ($personnel) {
            $personnel->delete();
        }

        $admin->delete();

        return redirect()->route('superadmin.admins.index')->with('success', 'Admin et personnel associé supprimés.');
    }

    public function assignRole(Request $request, $id)
    {
        $admin = User::findOrFail($id);
        $fonction = $request->input('fonction');
        $medecinId = $request->input('medecin_id'); // Nouveau champ optionnel
        $userRole = $request->input('user_role'); // Nouveau : 'admin' ou 'medecin'

        // NOUVELLE LOGIQUE : Si la fonction est "Médecin" OU si user_role = 'medecin'
        // Alors on change le rôle vers "medecin" et on assigne un medecin_id
        if ($fonction === 'Médecin' || $userRole === 'medecin') {
            // Obtenir l'ID du rôle "medecin"
            $medecinRole = \App\Models\Role::where('name', 'medecin')->first();

            if (!$medecinRole) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['user_role' => 'Le rôle "medecin" n\'existe pas dans la base de données. Veuillez d\'abord créer ce rôle.']);
            }

            // Vérifier que medecin_id est fourni
            if (!$medecinId) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['medecin_id' => 'Un médecin doit être sélectionné lorsque la fonction est "Médecin".']);
            }

            $admin->role_id = $medecinRole->id;
            $admin->medecin_id = $medecinId;
        } else {
            // Si user_role = 'admin' ou fonction n'est pas "Médecin"
            // Changer le rôle vers "admin" et retirer l'association medecin_id
            $adminRole = \App\Models\Role::where('name', 'admin')->first();
            
            if (!$adminRole) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['user_role' => 'Le rôle "admin" n\'existe pas dans la base de données. Veuillez d\'abord créer ce rôle.']);
            }

            $admin->role_id = $adminRole->id;
            $admin->medecin_id = null; // Retirer l'association avec le profil médecin
        }

        // Mettre à jour la fonction de l'utilisateur (compatibilité avec l'ancien système)
        // Seulement si le rôle a été correctement assigné
        $admin->fonction = $fonction;
        $admin->save();

        // Synchroniser avec le module personnel SEULEMENT si ce n'est pas un médecin
        // (les médecins ont leur propre table 'medecins')
        if ($fonction !== 'Médecin' && $userRole !== 'medecin') {
            $this->syncWithPersonnel($admin, $fonction);
        }

        return redirect()->back()->with('success', 'Rôle et fonction attribués avec succès.');
    }

    /**
     * Synchroniser l'utilisateur avec le module personnel
     */
    private function syncWithPersonnel(User $user, string $fonction)
    {
        // Chercher si un personnel existe déjà lié à cet utilisateur
        $personnel = \App\Models\Personnel::where('user_id', $user->id)->first();

        // Si pas trouvé par user_id, chercher par nom (pour la compatibilité avec les anciens enregistrements)
        if (!$personnel) {
            $personnel = \App\Models\Personnel::where('nom', $user->name)->whereNull('user_id')->first();
        }

        if ($personnel) {
            // Mettre à jour la fonction et le statut du personnel existant
            $personnel->update([
                'fonction' => $fonction,
                'is_approved' => $user->is_approved,
                'user_id' => $user->id // Lier à l'utilisateur si ce n'était pas déjà fait
            ]);
        } elseif ($user->is_approved) {
            // Créer un nouveau personnel SEULEMENT si l'utilisateur est approuvé
            \App\Models\Personnel::create([
                'nom' => $user->name,
                'fonction' => $fonction,
                'telephone' => null, // À remplir manuellement si nécessaire
                'salaire' => 0, // À définir selon la fonction
                'is_approved' => $user->is_approved,
                'created_by' => \Illuminate\Support\Facades\Auth::id(),
                'user_id' => $user->id // Lier directement à l'utilisateur
            ]);
        }
        // Si l'utilisateur n'est pas approuvé et qu'il n'y a pas de personnel existant,
        // on ne fait rien - le personnel sera créé lors de l'approbation
    }

    public function approve($id)
    {
        $admin = User::findOrFail($id);
        $admin->is_approved = true;
        $admin->save();

        // Synchroniser avec le personnel existant ou créer un nouveau personnel si l'utilisateur a une fonction
        if ($admin->fonction) {
            $this->syncWithPersonnel($admin, $admin->fonction);
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
