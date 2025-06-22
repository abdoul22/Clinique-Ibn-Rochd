<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medecin;
use Illuminate\Support\Facades\Auth;

class MedecinController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Medecin::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                    ->orWhere('prenom', 'like', "%{$search}%")
                    ->orWhere('specialite', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        $medecins = $query->orderBy('created_at', 'desc')->paginate(6);

        $viewPath = $this->resolveViewPath('index');
        return view($viewPath, compact('medecins'));
    }

    public function create()
    {
        $viewPath = $this->resolveViewPath('create');
        return view($viewPath);
    }
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'specialite' => 'required',
            'email' => 'nullable|email|unique:medecins,email',
        ]);

        $medecin = Medecin::create($request->only([
            'nom',
            'prenom',
            'specialite',
            'telephone',
            'tarif_consultation',
            'part_medecin',
            'email',
            'adresse',
            'date_embauche',
            'experience',
            'statut',
        ]));

        $role = Auth::user()->role->name;

        if ($medecin) {
            return redirect()->route("{$role}.medecins.index")->with('success', 'Médecin ajouté avec succès.');
        }

        return back()->with('error', 'Erreur lors de la sauvegarde.');
    }


    public function edit($id)
    {
        $medecin = Medecin::findOrFail($id);
        $viewPath = $this->resolveViewPath('edit');
        return view($viewPath, compact('medecin'));
    }

    public function update(Request $request, $id)
    {
        $medecin = Medecin::findOrFail($id);

        $request->validate([
            'nom' => 'required',
            'prenom' => 'required',
            'specialite' => 'required',
            'email' => 'nullable|email|unique:medecins,email,' . $medecin->id,
        ]);

        $medecin->update($request->only([
            'nom',
            'prenom',
            'specialite',
            'telephone',
            'email',
            'adresse',
            'date_embauche',
            'experience',
            'statut',
            'tarif_consultation',
            'part_medecin',
        ]));

        return redirect()->route(Auth::user()->role->name . '.medecins.index')->with('success', 'Médecin mis à jour avec succès.');
    }
    public function show($id)
    {
        $medecin = Medecin::with(['caisses', 'caisses.examen'])->findOrFail($id);
        return view('medecins.show', compact('medecin'));
    }

    public function destroy($id)
    {
        $medecin = Medecin::findOrFail($id);

        if ($medecin->delete()) {
            return redirect()->route(Auth::user()->role->name . '.medecins.index')->with('success', 'Médecin supprimé avec succès.');
        }

        return back()->with('error', 'Erreur lors de la suppression.');
    }

    private function resolveViewPath($view)
    {
        $role = Auth::user()->role->name;

        return match ($role) {
            'superadmin' => "medecins.$view",
            'admin' => "medecins.$view",
            default => "medecins.$view",
        };
    }

    private function resolveRoute($routeName)
    {
        $role = Auth::user()->role->name;

        return match ($role) {
            'superadmin' => "medecins.$routeName",
            'admin' => "medecins.$routeName",
            default => "medecins.$routeName",
        };
    }
}
