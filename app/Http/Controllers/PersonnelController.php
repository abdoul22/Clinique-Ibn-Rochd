<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use Illuminate\Http\Request;

class PersonnelController extends Controller
{
    public function index()
    {
        // Récupérer le personnel existant avec les crédits
        $personnels = Personnel::with('credits')->latest()->get();

        // Récupérer les utilisateurs qui ont une fonction attribuée
        $usersWithFunction = \App\Models\User::whereNotNull('fonction')
            ->where('fonction', '!=', '')
            ->with('role')
            ->get();

        // Combiner les deux collections
        $allPersonnel = collect();

        // Ajouter le personnel existant
        foreach ($personnels as $personnel) {
            $allPersonnel->push([
                'id' => $personnel->id,
                'nom' => $personnel->nom,
                'fonction' => $personnel->fonction,
                'salaire' => $personnel->salaire,
                'telephone' => $personnel->telephone,
                'adresse' => $personnel->adresse,
                'is_approved' => $personnel->is_approved,
                'credit' => $personnel->credit, // Utilise l'accesseur getCreditAttribute()
                'type' => $personnel->user_id ? 'user' : 'personnel', // Détecter si lié à un utilisateur
                'user_id' => $personnel->user_id
            ]);
        }

        // Ajouter les utilisateurs avec fonction qui n'ont pas encore de personnel créé
        foreach ($usersWithFunction as $user) {
            // Vérifier si un personnel existe déjà lié à cet utilisateur
            $existingPersonnel = $personnels->where('user_id', $user->id)->first();

            // Si pas trouvé par user_id, vérifier par nom (compatibilité)
            if (!$existingPersonnel) {
                $existingPersonnel = $personnels->where('nom', $user->name)->first();
            }

            if (!$existingPersonnel) {
                // Calculer le crédit pour cet utilisateur
                $userCredits = \App\Models\Credit::where('source_type', \App\Models\Personnel::class)
                    ->where('source_id', $user->id)
                    ->get();
                $userCredit = $userCredits->sum('montant') - $userCredits->sum('montant_paye');

                $allPersonnel->push([
                    'id' => 'user_' . $user->id,
                    'nom' => $user->name,
                    'fonction' => $user->fonction,
                    'salaire' => 0, // Par défaut, à modifier manuellement
                    'telephone' => null, // À remplir manuellement
                    'adresse' => null, // À remplir manuellement
                    'is_approved' => $user->is_approved,
                    'credit' => $userCredit, // Calculer le crédit réel
                    'type' => 'user',
                    'user_id' => $user->id
                ]);
            }
        }

        // Paginer manuellement
        $perPage = 10;
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedPersonnel = $allPersonnel->slice($offset, $perPage);

        // Créer un objet de pagination personnalisé
        $personnels = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedPersonnel,
            $allPersonnel->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'page']
        );

        return view('personnels.index', compact('personnels'));
    }

    public function create()
    {
        return view('personnels.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required',
            'fonction' => 'required',
            'salaire' => 'required|numeric',
            'telephone' => 'nullable',
            'adresse' => 'nullable',
        ]);

        // Le personnel créé manuellement est automatiquement approuvé
        $data = $request->all();
        $data['is_approved'] = true; // Personnel créé manuellement = automatiquement approuvé
        $data['created_by'] = 'superadmin'; // Marquer comme créé par superadmin

        Personnel::create($data);
        return redirect()->route('personnels.index')->with('success', 'Personnel ajouté et approuvé automatiquement.');
    }

    public function show(Personnel $personnel, $id)
    {
        // Gestion des entrées de type 'user_X'
        if (str_starts_with($id, 'user_')) {
            $userId = str_replace('user_', '', $id);
            $user = \App\Models\User::findOrFail($userId);

            // Rediriger vers la gestion des utilisateurs pour les entrées user
            return redirect()->route('superadmin.admins.show', $userId)
                ->with('info', 'Cette entrée est gérée depuis le module utilisateurs.');
        }

        $personnel = Personnel::findOrFail($id);
        $isLinkedToUser = (bool) $personnel->user_id;

        return view('personnels.show', compact('personnel', 'isLinkedToUser'));
    }

    public function edit(Personnel $personnel, $id)
    {
        // Gestion des entrées de type 'user_X'
        if (str_starts_with($id, 'user_')) {
            $userId = str_replace('user_', '', $id);
            $user = \App\Models\User::findOrFail($userId);

            // Rediriger vers la gestion des utilisateurs pour les entrées user
            return redirect()->route('superadmin.admins.edit', $userId)
                ->with('info', 'Cette entrée est gérée depuis le module utilisateurs.');
        }

        $personnel = Personnel::findOrFail($id);

        // Permettre l'édition partielle des personnels liés aux utilisateurs
        // Un personnel est considéré comme "lié à un utilisateur" seulement s'il a été créé automatiquement
        // via le système d'utilisateurs, pas s'il a été créé manuellement puis lié
        $isLinkedToUser = (bool) $personnel->user_id && $personnel->created_by === 'system';

        return view('personnels.edit', compact('personnel', 'isLinkedToUser'));
    }

    public function update(Request $request, $id)
    {
        // Gestion des entrées de type 'user' - ne devrait pas arriver grâce aux redirections
        if (str_starts_with($id, 'user_')) {
            return redirect()->route('personnels.index')
                ->with('error', 'Les entrées gérées par les utilisateurs ne peuvent pas être modifiées depuis ce module.');
        }

        $personnel = Personnel::findOrFail($id);
        // Un personnel est considéré comme "lié à un utilisateur" seulement s'il a été créé automatiquement
        // via le système d'utilisateurs, pas s'il a été créé manuellement puis lié
        $isLinkedToUser = (bool) $personnel->user_id && $personnel->created_by === 'system';

        if ($isLinkedToUser) {
            // Édition partielle pour personnel lié à un utilisateur
            $request->validate([
                'salaire' => 'required|numeric',
                'telephone' => 'nullable',
                'adresse' => 'nullable',
            ]);

            // Mettre à jour uniquement les champs autorisés
            $personnel->update([
                'salaire' => $request->salaire,
                'telephone' => $request->telephone,
                'adresse' => $request->adresse,
            ]);

            return redirect()->route('personnels.show', $personnel)
                ->with('success', 'Informations personnel mises à jour (salaire, téléphone, adresse).');
        } else {
            // Édition complète pour personnel normal
            $request->validate([
                'nom' => 'required',
                'fonction' => 'required',
                'salaire' => 'required|numeric',
                'telephone' => 'nullable',
                'adresse' => 'nullable',
                'is_approved' => 'boolean',
            ]);

            $data = $request->all();

            // Gérer le statut d'approbation
            $data['is_approved'] = $request->has('is_approved');

            // Si le personnel est approuvé et qu'il a une fonction, synchroniser avec l'utilisateur
            if ($data['is_approved'] && $data['fonction']) {
                $this->syncWithUser($personnel, $data);
            }

            $personnel->update($data);

            $message = $data['is_approved'] ? 'Personnel mis à jour et approuvé.' : 'Personnel mis à jour.';
            return redirect()->route('personnels.show', $personnel)->with('success', $message);
        }
    }

    public function destroy(Personnel $personnel, $id)
    {
        // Gestion des entrées de type 'user'
        if (str_starts_with($id, 'user_')) {
            return redirect()->route('personnels.index')
                ->with('error', 'Les entrées gérées par les utilisateurs ne peuvent pas être supprimées depuis ce module. Supprimez l\'utilisateur depuis le module utilisateurs.');
        }

        $personnel = Personnel::findOrFail($id);
        $personnel->delete();
        return redirect()->route('personnels.index')->with('success', 'Personnel supprimé.');
    }

    /**
     * Synchroniser le personnel avec un utilisateur existant
     */
    private function syncWithUser(Personnel $personnel, array $data)
    {
        // Chercher un utilisateur avec le même nom
        $user = \App\Models\User::where('name', $personnel->nom)->first();

        if ($user) {
            // Mettre à jour l'utilisateur avec la fonction et le statut d'approbation
            $user->update([
                'fonction' => $data['fonction'],
                'is_approved' => $data['is_approved']
            ]);
        }
        // Note: La création automatique d'utilisateurs admin depuis le module personnel est désactivée.
        // Les utilisateurs admin doivent créer leur compte via /register et être approuvés par le superadmin.
    }
}
