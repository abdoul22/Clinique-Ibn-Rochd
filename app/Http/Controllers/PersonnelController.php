<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use Illuminate\Http\Request;

class PersonnelController extends Controller
{
    public function index()
    {
        // Récupérer le personnel existant
        $personnels = Personnel::latest()->get();

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
                'credit' => $personnel->credit,
                'type' => 'personnel',
                'user_id' => null
            ]);
        }

        // Ajouter les utilisateurs avec fonction
        foreach ($usersWithFunction as $user) {
            // Vérifier si un personnel existe déjà avec ce nom
            $existingPersonnel = $personnels->where('nom', $user->name)->first();

            if (!$existingPersonnel) {
                $allPersonnel->push([
                    'id' => 'user_' . $user->id,
                    'nom' => $user->name,
                    'fonction' => $user->fonction,
                    'salaire' => 0, // Par défaut, à modifier manuellement
                    'telephone' => null, // À remplir manuellement
                    'adresse' => null, // À remplir manuellement
                    'is_approved' => $user->is_approved,
                    'credit' => 0, // Pas de crédit par défaut
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

        Personnel::create($data);
        return redirect()->route('personnels.index')->with('success', 'Personnel ajouté et approuvé automatiquement.');
    }

    public function show(Personnel $personnel, $id)
    {
        $personnel = Personnel::findorfail($id);

        return view('personnels.show', compact('personnel'));
    }

    public function edit(Personnel $personnel, $id)
    {
        $personnel = Personnel::findorfail($id);
        return view('personnels.edit', compact('personnel'));
    }

    public function update(Request $request, $id)
    {
        $personnel = Personnel::findOrFail($id);
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

    public function destroy(Personnel $personnel, $id)
    {
        $personnel = Personnel::findorfail($id);
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
        } else {
            // Créer un nouvel utilisateur si nécessaire
            \App\Models\User::create([
                'name' => $personnel->nom,
                'email' => strtolower(str_replace(' ', '.', $personnel->nom)) . '@clinique.com',
                'password' => bcrypt('password123'), // Mot de passe temporaire
                'role_id' => 2, // ID par défaut pour admin
                'fonction' => $data['fonction'],
                'is_approved' => $data['is_approved']
            ]);
        }
    }
}
