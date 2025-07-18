<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Pharmacie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;


class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Service::query();

        if ($search) {
            $query->where('nom', 'like', "%{$search}%")
                ->orWhere('observation', 'like', "%{$search}%");
        }

        // Ajouter le filtre par type de service
        if ($request->filled('type_service')) {
            $query->where('type_service', $request->type_service);
        }

        $services = $query->with('pharmacie')->orderBy('created_at', 'desc')->paginate(10);

        // Traiter les données pour l'affichage
        $services->getCollection()->transform(function ($service) {
            // Si c'est un service de type médicament lié à la pharmacie
            if ($service->type_service === 'medicament' && $service->pharmacie) {
                $service->nom_affichage = 'Pharmacie';
                $service->observation_affichage = $service->pharmacie->nom_medicament;
            } else {
                $service->nom_affichage = $service->nom;
                $service->observation_affichage = $service->observation;
            }
            return $service;
        });

        return view('services.index', ['services' => $services]);
    }

    public function create()
    {
        $medicaments = Pharmacie::where('stock', '>', 0)
            ->where('statut', 'actif')
            ->orderBy('nom_medicament')
            ->get();

        return view('services.create', compact('medicaments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'type_service' => 'required|in:consultations,examens,pharmacie,infirmerie,bloc,laboratoire,hospitalisation,dentaire',
            'pharmacie_id' => 'nullable|exists:pharmacies,id',
            'prix' => 'nullable|numeric|min:0',
            'quantite_defaut' => 'nullable|integer|min:1'
        ]);

        $data = $request->only(['nom', 'observation', 'type_service', 'pharmacie_id', 'prix', 'quantite_defaut']);

        // Si c'est un médicament, récupérer le prix depuis la pharmacie
        if ($data['type_service'] === 'medicament' && $data['pharmacie_id']) {
            $medicament = Pharmacie::find($data['pharmacie_id']);
            if ($medicament) {
                $data['prix'] = $medicament->prix_vente;
                $data['quantite_defaut'] = $medicament->quantite;
            }
        }

        Service::create($data);
        return redirect('services')->with('success', 'Service ajouté avec succès.');
    }

    public function show($id)
    {
        $service = Service::with('pharmacie')->findOrFail($id);
        return view('services.show', ['service' => $service]);
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);
        $medicaments = Pharmacie::where('stock', '>', 0)
            ->where('statut', 'actif')
            ->orderBy('nom_medicament')
            ->get();

        return view('services.edit', compact('service', 'medicaments'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'type_service' => 'required|in:consultations,examens,pharmacie,infirmerie,bloc,laboratoire,hospitalisation,dentaire',
            'pharmacie_id' => 'nullable|exists:pharmacies,id',
            'prix' => 'nullable|numeric|min:0',
            'quantite_defaut' => 'nullable|integer|min:1'
        ]);

        $service = Service::findOrFail($id);
        $data = $request->only(['nom', 'observation', 'type_service', 'pharmacie_id', 'prix', 'quantite_defaut']);

        // Si c'est un médicament, récupérer le prix depuis la pharmacie
        if ($data['type_service'] === 'medicament' && $data['pharmacie_id']) {
            $medicament = Pharmacie::find($data['pharmacie_id']);
            if ($medicament) {
                $data['prix'] = $medicament->prix_vente;
                $data['quantite_defaut'] = $medicament->quantite;
            }
        }

        $service->update($data);
        return redirect('services')->with('success', 'Service mis à jour.');
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();
        return redirect('services')->with('success', 'Service supprimé.');
    }

    public function exportPdf()
    {
        $services = Service::with('pharmacie')->get();

        // Traiter les données pour l'affichage
        $services->transform(function ($service) {
            // Si c'est un service de type médicament lié à la pharmacie
            if ($service->type_service === 'medicament' && $service->pharmacie) {
                $service->nom_affichage = 'Pharmacie';
                $service->observation_affichage = $service->pharmacie->nom_medicament;
            } else {
                $service->nom_affichage = $service->nom;
                $service->observation_affichage = $service->observation;
            }
            return $service;
        });

        $pdf = PDF::loadView('services.export_pdf', compact('services'));
        return $pdf->download('services.pdf');
    }

    public function print()
    {
        $services = Service::with('pharmacie')->get();

        // Traiter les données pour l'affichage
        $services->transform(function ($service) {
            // Si c'est un service de type médicament lié à la pharmacie
            if ($service->type_service === 'medicament' && $service->pharmacie) {
                $service->nom_affichage = 'Pharmacie';
                $service->observation_affichage = $service->pharmacie->nom_medicament;
            } else {
                $service->nom_affichage = $service->nom;
                $service->observation_affichage = $service->observation;
            }
            return $service;
        });

        return view('services.print', compact('services'));
    }

    /**
     * API pour récupérer les services de type médicament
     */
    public function getServicesMedicaments()
    {
        $services = Service::where('type_service', 'medicament')
            ->whereHas('pharmacie', function ($query) {
                $query->where('stock', '>', 0)->where('statut', 'actif');
            })
            ->with('pharmacie')
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'nom' => $service->nom,
                    'pharmacie_id' => $service->pharmacie_id,
                    'medicament' => $service->pharmacie->nom_medicament,
                    'prix' => $service->pharmacie->prix_vente,
                    'stock' => $service->pharmacie->stock,
                    'quantite_defaut' => $service->quantite_defaut
                ];
            });

        return response()->json($services);
    }
}
