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

    // Enregistrement d'un nouvel admin
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
            'role_id' => 2, // Admin par défaut
            'is_approved' => false, // L'utilisateur doit être approuvé avant de se connecter
            'fonction' => null, // La fonction sera assignée lors de l'approbation
        ]);

        return redirect()->route('superadmin.admins.index')->with('success', 'Utilisateur créé avec succès. N\'oubliez pas de l\'approuver et de lui assigner une fonction avant qu\'il puisse se connecter.');
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
        $admin = User::with('role', 'medecin')->findOrFail($id);
        
        // Récupérer les médecins déjà associés à d'autres utilisateurs (sauf celui-ci)
        $medecinsAssocies = User::whereNotNull('medecin_id')
            ->where('id', '!=', $admin->id) // Exclure l'utilisateur actuel
            ->pluck('medecin_id')
            ->toArray();
        
        // Récupérer la liste des médecins disponibles (non déjà associés)
        $medecinsList = \App\Models\Medecin::where('statut', 'actif')
            ->whereNotIn('id', $medecinsAssocies)
            ->orderByRaw("FIELD(fonction, 'Pr', 'Dr', 'Tss', 'SGF', 'IDE')")
            ->orderBy('nom')
            ->get();
        
        return view('superadmin.admins.edit', compact('admin', 'medecinsList'));
    }

    // Mise à jour
    public function update(Request $request, $id)
    {
        $admin = User::with('role')->findOrFail($id);

        // Si c'est un superadmin, on ne modifie que le nom et l'email
        if ($admin->role && $admin->role->name === 'superadmin') {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $admin->id,
            ]);

            // Mettre à jour uniquement le nom et l'email
            $admin->name = $request->name;
            $admin->email = $request->email;
            // Les superadmins restent toujours approuvés
            $admin->is_approved = true;
            $admin->save();

            return redirect()->route('superadmin.admins.index')->with('success', 'Super Administrateur mis à jour avec succès.');
        }

        // Pour les admins et médecins
        $fonction = $request->input('fonction');
        $medecinId = $request->input('medecin_id');
        $userRole = $request->input('user_role'); // 'admin' ou 'medecin'

        // Validation conditionnelle : si user_role='medecin', medecin_id est requis
        $validationRules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'user_role' => 'required|in:admin,medecin',
        ];

        if ($userRole === 'medecin') {
            // Vérifier que le rôle 'medecin' existe
            $medecinRole = \App\Models\Role::where('name', 'medecin')->first();
            if (!$medecinRole) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['user_role' => 'Le rôle "medecin" n\'existe pas dans la base de données. Veuillez d\'abord créer ce rôle.']);
            }

            // Require medecin_id when user_role is 'medecin'
            $validationRules['medecin_id'] = 'required|exists:medecins,id';
        } else {
            $validationRules['medecin_id'] = 'nullable|exists:medecins,id';
        }

        $request->validate($validationRules);

        $isApproved = $request->has('is_approved');

        // Gérer le changement de rôle
        if ($userRole === 'medecin') {
            // Assigner le rôle medecin
            $medecinRole = \App\Models\Role::where('name', 'medecin')->first();

            if (!$medecinRole) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['user_role' => 'Le rôle "medecin" n\'existe pas dans la base de données.']);
            }

            $admin->role_id = $medecinRole->id;
            $admin->medecin_id = $medecinId;
            $admin->fonction = 'Médecin'; // Assigner la fonction "Médecin"
        } else {
            // Assigner le rôle admin
            $adminRole = \App\Models\Role::where('name', 'admin')->first();
            
            if (!$adminRole) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['user_role' => 'Le rôle "admin" n\'existe pas dans la base de données. Veuillez d\'abord créer ce rôle.']);
            }

            $admin->role_id = $adminRole->id;
            $admin->medecin_id = null; // Retirer l'association avec le profil médecin
            $admin->fonction = $fonction; // Assigner la fonction pour les admins (Caissier, etc.)
        }

        // Mettre à jour les informations de base
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->is_approved = $isApproved;
        $admin->save();

        // Synchroniser avec le personnel si ce n'est pas un médecin
        if ($userRole !== 'medecin' && $fonction) {
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

        return redirect()->route('superadmin.admins.index')->with('success', 'Utilisateur et personnel associé supprimés.');
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
            $admin->fonction = 'Médecin'; // IMPORTANT : Assigner la fonction "Médecin"
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
            $admin->fonction = $fonction; // Assigner la fonction pour les admins (Caissier, Secrétaire, etc.)
        }

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
        $admin = User::with('role')->findOrFail($id);
        
        // Vérifier que l'utilisateur a bien un rôle assigné
        if (!$admin->role_id) {
            return redirect()->back()->with('error', 'Impossible d\'approuver cet utilisateur : aucun rôle n\'a été assigné. Veuillez d\'abord lui assigner un rôle.');
        }
        
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
