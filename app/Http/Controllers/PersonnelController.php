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
                'type' => $personnel->user_id ? 'user' : 'personnel',
                'user_id' => $personnel->user_id
            ]);
        }

        // Ajouter les utilisateurs avec fonction qui n'ont pas encore de personnel créé
        foreach ($usersWithFunction as $user) {
            $existingPersonnel = $personnels->where('user_id', $user->id)->first();
            if (!$existingPersonnel) {
                $existingPersonnel = $personnels->where('nom', $user->name)->first();
            }

            if (!$existingPersonnel) {
                $allPersonnel->push([
                    'id' => 'user_' . $user->id,
                    'nom' => $user->name,
                    'fonction' => $user->fonction,
                    'salaire' => 0,
                    'telephone' => null,
                    'adresse' => null,
                    'is_approved' => $user->is_approved,
                    'credit' => 0,
                    'type' => 'user',
                    'user_id' => $user->id
                ]);
            }
        }

        $perPage = 10;
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedPersonnel = $allPersonnel->slice($offset, $perPage);

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

        $data = $request->all();
        $data['is_approved'] = true;

        Personnel::create($data);
        return redirect()->route('personnels.index')->with('success', 'Personnel ajouté et approuvé automatiquement.');
    }

    public function show(Personnel $personnel, $id)
    {
        if (str_starts_with($id, 'user_')) {
            $userId = str_replace('user_', '', $id);
            $user = \App\Models\User::findOrFail($userId);

            return redirect()->route('superadmin.admins.show', $userId)
                ->with('info', 'Cette entrée est gérée depuis le module utilisateurs.');
        }

        $personnel = Personnel::findOrFail($id);
        $isLinkedToUser = (bool) $personnel->user_id;

        return view('personnels.show', compact('personnel', 'isLinkedToUser'));
    }

    public function edit(Personnel $personnel, $id)
    {
        if (str_starts_with($id, 'user_')) {
            $userId = str_replace('user_', '', $id);
            $user = \App\Models\User::findOrFail($userId);

            return redirect()->route('superadmin.admins.edit', $userId)
                ->with('info', 'Cette entrée est gérée depuis le module utilisateurs.');
        }

        $personnel = Personnel::findOrFail($id);
        $isLinkedToUser = (bool) $personnel->user_id;

        return view('personnels.edit', compact('personnel', 'isLinkedToUser'));
    }

    public function update(Request $request, $id)
    {
        if (str_starts_with($id, 'user_')) {
            return redirect()->route('personnels.index')
                ->with('error', 'Les entrées gérées par les utilisateurs ne peuvent pas être modifiées depuis ce module.');
        }

        $personnel = Personnel::findOrFail($id);
        $isLinkedToUser = (bool) $personnel->user_id;

        if ($isLinkedToUser) {
            $request->validate([
                'salaire' => 'required|numeric',
                'telephone' => 'nullable',
                'adresse' => 'nullable',
            ]);

            $personnel->update([
                'salaire' => $request->salaire,
                'telephone' => $request->telephone,
                'adresse' => $request->adresse,
            ]);

            return redirect()->route('personnels.show', $personnel)
                ->with('success', 'Informations personnel mises à jour (salaire, téléphone, adresse).');
        } else {
            $request->validate([
                'nom' => 'required',
                'fonction' => 'required',
                'salaire' => 'required|numeric',
                'telephone' => 'nullable',
                'adresse' => 'nullable',
                'is_approved' => 'boolean',
            ]);

            $data = $request->all();
            $data['is_approved'] = $request->has('is_approved');

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
        if (str_starts_with($id, 'user_')) {
            return redirect()->route('personnels.index')
                ->with('error', 'Les entrées gérées par les utilisateurs ne peuvent pas être supprimées depuis ce module. Supprimez l\'utilisateur depuis le module utilisateurs.');
        }

        $personnel = Personnel::findOrFail($id);
        $personnel->delete();
        return redirect()->route('personnels.index')->with('success', 'Personnel supprimé.');
    }

    private function syncWithUser(Personnel $personnel, array $data)
    {
        $user = \App\Models\User::where('name', $personnel->nom)->first();

        if ($user) {
            $user->update([
                'fonction' => $data['fonction'],
                'is_approved' => $data['is_approved']
            ]);
        }
    }
}
