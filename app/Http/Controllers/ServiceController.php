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
        $query = Service::query()->whereNull('pharmacie_id'); // masquer les lignes liées aux médicaments

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
            // Afficher le nom du médicament si service pharmacie/medicament
            if ($service->type_service === 'PHARMACIE' && $service->pharmacie) {
                $service->nom_affichage = $service->pharmacie->nom_medicament;
                $service->observation_affichage = $service->observation;
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
            'type_service' => 'required|in:LABORATOIRE,PHARMACIE,MEDECINE DENTAIRE,IMAGERIE MEDICALE,CONSULTATIONS EXTERNES,HOSPITALISATION,BLOC OPERATOIRE,INFIRMERIE,EXPLORATIONS FONCTIONNELLES',
            'pharmacie_id' => 'nullable|exists:pharmacies,id',
            'prix' => 'nullable|numeric|min:0',
            'quantite_defaut' => 'nullable|integer|min:1'
        ]);

        $data = $request->only(['nom', 'observation', 'type_service', 'pharmacie_id', 'prix', 'quantite_defaut']);

        // La création de médicaments ne doit pas créer automatiquement un service ici

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
        $page = request('page', 1); // Récupérer le paramètre page

        return view('services.edit', compact('service', 'medicaments', 'page'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'type_service' => 'nullable|in:LABORATOIRE,PHARMACIE,MEDECINE DENTAIRE,IMAGERIE MEDICALE,CONSULTATIONS EXTERNES,HOSPITALISATION,BLOC OPERATOIRE,INFIRMERIE,EXPLORATIONS FONCTIONNELLES',
            'pharmacie_id' => 'nullable|exists:pharmacies,id',
            'prix' => 'nullable|numeric|min:0',
            'quantite_defaut' => 'nullable|integer|min:1'
        ]);

        $service = Service::findOrFail($id);
        $data = $request->only(['nom', 'observation', 'pharmacie_id', 'prix', 'quantite_defaut']);
        if ($request->filled('type_service')) {
            $data['type_service'] = $request->input('type_service');
        }

        // Pas de synchronisation automatique depuis pharmacie ici

        $service->update($data);
        
        // Conserver le paramètre de pagination
        $page = $request->input('return_page', 1);
        return redirect()->route('services.index', ['page' => $page])->with('success', 'Service mis à jour.');
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
            // Si c'est un service de type medicament/pharmacie lié à la pharmacie
            if ($service->type_service === 'PHARMACIE' && $service->pharmacie) {
                $service->nom_affichage = $service->pharmacie->nom_medicament;
                $service->observation_affichage = $service->observation;
            } else {
                $service->nom_affichage = $service->nom;
                $service->observation_affichage = $service->observation;
            }
            return $service;
        });

        $pdf = Pdf::loadView('services.export_pdf', compact('services'));
        return $pdf->download('services.pdf');
    }

    public function print()
    {
        $services = Service::with('pharmacie')->get();

        // Traiter les données pour l'affichage
        $services->transform(function ($service) {
            if ($service->type_service === 'PHARMACIE' && $service->pharmacie) {
                $service->nom_affichage = $service->pharmacie->nom_medicament;
                $service->observation_affichage = $service->observation;
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
        return response()->json([]);
    }
}
