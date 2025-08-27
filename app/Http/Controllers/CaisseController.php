<?php

namespace App\Http\Controllers;

use App\Models\{Caisse, EtatCaisse, GestionPatient, Medecin, Prescripteur, Examen, Service, ModePaiement};
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CaisseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $period = $request->input('period', 'day');
        $date = $request->input('date');
        $week = $request->input('week');
        $month = $request->input('month');
        $year = $request->input('year');
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');
        $patientFilter = $request->input('patient_filter');
        $numeroPatientFilter = $request->input('numero_patient_filter');
        $medecinFilter = $request->input('medecin_filter');

        $query = Caisse::with(['patient', 'medecin', 'prescripteur', 'examen', 'service']);

        // Filtrage par recherche générale
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('numero_entre', 'like', "%{$search}%")
                    ->orWhereHas('patient', function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('medecin', function ($q) use ($search) {
                        $q->where('nom', 'like', "%{$search}%");
                    });
            });
        }

        // Filtrage par patient spécifique
        if ($patientFilter) {
            $query->whereHas('patient', function ($q) use ($patientFilter) {
                $q->where('first_name', 'like', "%{$patientFilter}%")
                    ->orWhere('last_name', 'like', "%{$patientFilter}%");
            });
        }

        // Filtrage par numéro de patient
        if ($numeroPatientFilter) {
            $query->whereHas('patient', function ($q) use ($numeroPatientFilter) {
                $q->where('phone', 'like', "%{$numeroPatientFilter}%");
            });
        }

        // Filtrage par médecin spécifique
        if ($medecinFilter) {
            $query->whereHas('medecin', function ($q) use ($medecinFilter) {
                $q->where('nom', 'like', "%{$medecinFilter}%");
            });
        }

        // Filtrage par période
        if ($period === 'day' && $date) {
            $query->whereDate('date_examen', $date);
        } elseif ($period === 'week' && $week) {
            $parts = explode('-W', $week);
            if (count($parts) === 2) {
                $yearW = (int)$parts[0];
                $weekW = (int)$parts[1];
                $startOfWeek = \Carbon\Carbon::now()->setISODate($yearW, $weekW)->startOfWeek();
                $endOfWeek = \Carbon\Carbon::now()->setISODate($yearW, $weekW)->endOfWeek();
                $query->whereBetween('date_examen', [$startOfWeek, $endOfWeek]);
            }
        } elseif ($period === 'month' && $month) {
            $parts = explode('-', $month);
            if (count($parts) === 2) {
                $yearM = (int)$parts[0];
                $monthM = (int)$parts[1];
                $query->whereYear('date_examen', $yearM)->whereMonth('date_examen', $monthM);
            }
        } elseif ($period === 'year' && $year) {
            $query->whereYear('date_examen', $year);
        } elseif ($period === 'range' && $dateStart && $dateEnd) {
            $query->whereBetween('date_examen', [$dateStart, $dateEnd]);
        }

        $caisses = $query->orderBy('created_at', 'desc')->paginate(10);

        // Récupérer les listes pour les filtres
        $patients = GestionPatient::select('id', 'first_name', 'last_name', 'phone')
            ->orderBy('first_name')
            ->get();
        $medecins = Medecin::select('id', 'nom')
            ->orderBy('nom')
            ->get();

        return view('caisses.index', compact('caisses', 'patients', 'medecins'));
    }

    public function create(Request $request)
    {
        $patients = GestionPatient::all();
        // Récupérer seulement les médecins actifs avec noms complets
        $medecins = Medecin::where('statut', 'actif')
            ->select('id', 'nom', 'prenom', 'specialite')
            ->get()
            ->map(function ($medecin) {
                $medecin->nom_complet = trim($medecin->prenom . ' ' . $medecin->nom);
                return $medecin;
            });
        $prescripteurs = Prescripteur::all();
        $services = Service::all();
        // Exclure les examens d'hospitalisation automatiques des listes de sélection
        $exam_types = Examen::with('service.pharmacie')
            ->where('nom', 'NOT LIKE', 'Hospitalisation - %')
            ->get();
        $assurances = \App\Models\Assurance::all();

        // Variables pour pré-remplissage depuis un rendez-vous
        $fromRdv = null;
        $prefilledPatient = null;
        $prefilledMedecin = null;
        $prefilledNumeroEntree = null;

        // Si on vient d'un rendez-vous, récupérer les données
        if ($request->has('from_rdv')) {
            $fromRdv = \App\Models\RendezVous::find($request->from_rdv);
            if ($fromRdv) {
                $prefilledPatient = $fromRdv->patient;
                $prefilledMedecin = $fromRdv->medecin;
                $prefilledNumeroEntree = $fromRdv->numero_entree;
            }
        }

        // Calculer le numéro prévu pour chaque médecin (par jour, tous les numéros utilisés)
        $today = now()->startOfDay();
        $numeros_par_medecin = [];
        foreach ($medecins as $medecin) {
            // Récupérer tous les numéros d'entrée utilisés aujourd'hui pour ce médecin
            $numerosCaisses = Caisse::where('medecin_id', $medecin->id)
                ->whereDate('created_at', $today)
                ->pluck('numero_entre')
                ->toArray();

            $numerosRendezVous = \App\Models\RendezVous::where('medecin_id', $medecin->id)
                ->whereDate('created_at', $today)
                ->pluck('numero_entree')
                ->toArray();

            // Fusionner et trier tous les numéros utilisés
            $numerosUtilises = array_merge($numerosCaisses, $numerosRendezVous);
            sort($numerosUtilises);

            // Trouver le prochain numéro disponible
            $prochainNumero = 1;
            foreach ($numerosUtilises as $numero) {
                if ($numero >= $prochainNumero) {
                    $prochainNumero = $numero + 1;
                }
            }

            $numeros_par_medecin[$medecin->id] = $prochainNumero;
        }

        // Numéro par défaut
        $numero_prevu = $prefilledNumeroEntree ?? 1;

        return view('caisses.create', compact(
            'numero_prevu',
            'numeros_par_medecin',
            'patients',
            'medecins',
            'prescripteurs',
            'exam_types',
            'services',
            'assurances',
            'fromRdv',
            'prefilledPatient',
            'prefilledMedecin',
            'prefilledNumeroEntree'
        ));
    }

    public function store(Request $request)
    {
        // Validation de base
        $rules = [
            'gestion_patient_id' => 'required|exists:gestion_patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'prescripteur_id' => 'nullable|string',
            'date_examen' => 'required|date',
            'total' => 'required|numeric',
            // accepter temporairement 'especes' puis normaliser
            'type' => 'nullable|string|in:espèces,especes,bankily,masrivi,sedad',
            'assurance_id' => 'nullable|exists:assurances,id',
            'couverture' => 'nullable|numeric|min:0|max:100',
        ];

        // Si examens multiples, valider les données JSON, sinon valider l'examen unique
        if ($request->examens_multiple === 'true' && $request->filled('examens_data')) {
            $rules['examens_data'] = 'required|json';
        } else {
            $rules['examen_id'] = 'required|exists:examens,id';
            $rules['quantite_medicament'] = 'nullable|integer|min:1';
        }

        $request->validate($rules);

        $dernierNumero = Caisse::max('numero_facture') ?? 0;
        $prochainNumero = $dernierNumero + 1;

        // Déterminer le numéro d'entrée
        $numeroEntree = null;

        // Si on vient d'un rendez-vous, utiliser le numéro d'entrée du rendez-vous
        if ($request->filled('from_rdv')) {
            $rendezVous = \App\Models\RendezVous::find($request->from_rdv);
            if ($rendezVous) {
                $numeroEntree = $rendezVous->numero_entree;
            }
        }

        // Si pas de numéro d'entrée du rendez-vous, générer un nouveau
        if (!$numeroEntree) {
            $numeroEntree = $this->getNextNumeroEntree($request->medecin_id);
        }

        // Préparer les données de base de la facture
        $data = $request->only([
            'gestion_patient_id',
            'medecin_id',
            'prescripteur_id',
            'date_examen',
            'total',
            'assurance_id',
            'couverture'
        ]);
        $data['numero_entre'] = $numeroEntree;
        $data['nom_caissier'] = Auth::user()->name;
        $data['numero_facture'] = $prochainNumero;
        $data['couverture'] = $request->couverture ?? 0;
        $data['assurance_id'] = $request->filled('assurance_id') ? $request->assurance_id : null;

        // Gérer la valeur "extern" pour prescripteur_id
        if ($request->prescripteur_id === 'extern') {
            $data['prescripteur_id'] = null; // Stocker comme null dans la DB
        }

        // Gérer les examens (simple ou multiple)
        if ($request->examens_multiple === 'true' && $request->filled('examens_data')) {
            // Mode examens multiples
            $examensData = json_decode($request->examens_data, true);
            if (empty($examensData)) {
                return back()->withErrors(['examens_data' => 'Aucun examen sélectionné.']);
            }

            // Prendre le premier examen pour la relation principale (compatibilité)
            $premierExamen = Examen::find($examensData[0]['id']);
            $data['examen_id'] = $premierExamen->id;
            $data['service_id'] = $premierExamen->idsvc;

            // Stocker les examens multiples en JSON dans un champ dédié
            $data['examens_data'] = $request->examens_data;
        } else {
            // Mode examen unique (existant)
            $examen = Examen::findOrFail($request->examen_id);
            $data['examen_id'] = $examen->id;
            $data['service_id'] = $examen->idsvc ?? $examen->service_id ?? null;
        }

        $caisse = Caisse::create($data);

        // Gestion de la déduction du stock de pharmacie
        if ($request->examens_multiple === 'true' && $request->filled('examens_data')) {
            // Déduire les stocks pour chaque médicament
            $examensData = json_decode($request->examens_data, true);
            foreach ($examensData as $examenData) {
                $examen = Examen::find($examenData['id']);
                $service = Service::find($examen->idsvc);

                if ($service && (in_array($service->type_service, ['medicament', 'PHARMACIE']) || $service->pharmacie_id) && $service->pharmacie_id && $examenData['quantite'] > 0) {
                    $medicament = \App\Models\Pharmacie::find($service->pharmacie_id);

                    if ($medicament && $medicament->stockSuffisant($examenData['quantite'])) {
                        $medicament->deduireStock($examenData['quantite']);
                    } else {
                        $caisse->delete();
                        return back()->withErrors(['examens_data' => "Stock insuffisant pour {$examen->nom}. Stock disponible: " . ($medicament ? $medicament->stock : 0)]);
                    }
                }
            }
        } else {
            // Déduire le stock pour un seul médicament
            $examen = Examen::findOrFail($request->examen_id);
            $service = Service::find($examen->idsvc);
            $quantite = $request->quantite_medicament ?? 1;

            if ($service && (in_array($service->type_service, ['medicament', 'PHARMACIE']) || $service->pharmacie_id) && $service->pharmacie_id && $quantite > 0) {
                $medicament = \App\Models\Pharmacie::find($service->pharmacie_id);

                if ($medicament && $medicament->stockSuffisant($quantite)) {
                    $medicament->deduireStock($quantite);
                } else {
                    $caisse->delete();
                    return back()->withErrors(['quantite_medicament' => "Stock insuffisant pour {$examen->nom}. Stock disponible: " . ($medicament ? $medicament->stock : 0)]);
                }
            }
        }

        // Calculer les parts cabinet et médecin
        $part_cabinet = 0;
        $part_medecin = 0;

        if ($request->examens_multiple === 'true' && $request->filled('examens_data')) {
            $examensData = json_decode($request->examens_data, true);
            foreach ($examensData as $examenData) {
                $examen = Examen::find($examenData['id']);
                $part_cabinet += ($examen->part_cabinet ?? 0) * $examenData['quantite'];
                $part_medecin += ($examen->part_medecin ?? 0) * $examenData['quantite'];
            }
        } else {
            $examen = Examen::findOrFail($request->examen_id);
            $quantite = $request->quantite_medicament ?? 1;
            $part_cabinet = ($examen->part_cabinet ?? 0) * $quantite;
            $part_medecin = ($examen->part_medecin ?? 0) * $quantite;
        }

        // Normaliser le type de paiement
        $typePaiement = $request->type;
        if ($typePaiement === 'especes') {
            $typePaiement = 'espèces';
        }

        // Calculer répartition patient/assurance
        $montantTotal = $caisse->total;
        $couverture = $caisse->couverture ?? 0;
        $montantAssurance = $caisse->assurance_id ? ($montantTotal * ($couverture / 100)) : 0;
        $montantPatient = $montantTotal - $montantAssurance;

        // Créer l'état de caisse avec la recette côté patient (ce qui entre en caisse immédiatement)
        $etatCaisse = EtatCaisse::create([
            'caisse_id' => $caisse->id,
            'designation' => 'Facture N°' . $caisse->numero_facture,
            'recette' => $montantPatient,
            'part_medecin' => $part_medecin,
            'part_clinique' => $montantPatient, // Part clinique = montant payé par le patient (pas le total)
            'assurance_id' => $caisse->assurance_id,
            'medecin_id' => $caisse->medecin_id,
        ]);

        // Créer le paiement (uniquement la part patient)
        ModePaiement::create([
            'caisse_id' => $caisse->id,
            'type' => $typePaiement ?? 'espèces',
            'montant' => $montantPatient,
            'source' => 'caisse'
        ]);

        return redirect()->route('caisses.show', $caisse->id)->with('success', 'Facture et état de caisse créés avec succès.');
    }

    public function show($id)
    {
        $caisse = Caisse::with(['patient', 'medecin', 'prescripteur', 'examen', 'service'])->find($id);

        if (!$caisse) {
            abort(404, 'Caisse non trouvée.');
        }

        return view('caisses.show', compact('caisse'));
    }

    public function edit(Caisse $caisse, $id)
    {
        $caisse = Caisse::with(['patient', 'medecin', 'prescripteur', 'examen', 'service', 'assurance'])->find($id);
        $patients = GestionPatient::all();
        $medecins = Medecin::all();
        $prescripteurs = Prescripteur::all();
        // Exclure les examens d'hospitalisation automatiques des listes de sélection
        $exam_types = Examen::with('service.pharmacie')
            ->where('nom', 'NOT LIKE', 'Hospitalisation - %')
            ->get();
        $assurances = \App\Models\Assurance::all();

        return view('caisses.edit', compact(
            'caisse',
            'patients',
            'medecins',
            'prescripteurs',
            'exam_types',
            'assurances'
        ));
    }

    public function update(Request $request, Caisse $caisse)
    {
        $request->validate([
            'gestion_patient_id' => 'required|exists:gestion_patients,id',
            'medecin_id' => 'required|exists:medecins,id',
            'prescripteur_id' => 'nullable|exists:prescripteurs,id',
            'examen_id' => 'required|exists:examens,id',
            'date_examen' => 'required|date',
            'total' => 'required|numeric|min:0',
            'assurance_id' => 'nullable|exists:assurances,id',
            'couverture' => 'nullable|numeric|min:0|max:100',
            'nom_caissier' => 'required|string',
            'numero_entre' => 'nullable|integer|min:1',
        ]);

        // Obtenir les informations de l'examen pour le service_id
        $examen = Examen::findOrFail($request->examen_id);

        // Filtrer les données à sauvegarder
        $data = $request->only([
            'gestion_patient_id',
            'medecin_id',
            'prescripteur_id',
            'examen_id',
            'date_examen',
            'total',
            'assurance_id',
            'couverture',
            'nom_caissier',
            'numero_entre'
        ]);

        $data['service_id'] = $examen->idsvc ?? $examen->service_id ?? null;
        $data['assurance_id'] = $request->filled('assurance_id') ? $request->assurance_id : null;
        $data['couverture'] = $request->couverture ?? 0;

        // Si le médecin a changé et qu'un nouveau numéro d'entrée est fourni, l'utiliser
        if ($request->filled('numero_entre')) {
            $data['numero_entre'] = $request->numero_entre;
        }

        // Mettre à jour la caisse
        $caisse->update($data);

        // Mettre à jour l'état de caisse correspondant s'il existe
        $etatCaisse = \App\Models\EtatCaisse::where('caisse_id', $caisse->id)->first();

        if ($etatCaisse) {
            // Calculer la part patient en fonction de l'assurance
            $montantTotal = $caisse->total;
            $couverture = $caisse->couverture ?? 0;
            $montantAssurance = $montantTotal * ($couverture / 100);
            $montantPatient = $montantTotal - $montantAssurance;

            $etatCaisse->update([
                'designation' => 'Facture caisse n°' . $caisse->id,
                'recette' => $montantPatient,
                'assurance_id' => $caisse->assurance_id && $caisse->couverture > 0 ? $caisse->assurance_id : null,
                'medecin_id' => $caisse->medecin_id,
            ]);

            // EtatCaisse mis à jour
        }

        // Caisse mise à jour avec succès

        // Redirection selon le rôle de l'utilisateur
        $role = Auth::user()->role->name;
        $routeName = $role === 'superadmin' || $role === 'admin' ? $role . '.caisses.index' : 'caisses.index';

        // Force le navigateur à ne pas utiliser le cache pour cette redirection
        return redirect()->route($routeName)
            ->with('success', 'Examen mis à jour avec succès.')
            ->with('timestamp', time()); // Force refresh avec timestamp
    }

    public function destroy(Caisse $caisse, $id)
    {
        $caisse = Caisse::find($id);
        if ($caisse->delete()) {
            $role = Auth::user()->role->name;

            if ($role === 'superadmin') {
                return redirect()->route('superadmin.caisses.index')->with('success', 'Facture supprimée !');
            } elseif ($role === 'admin') {
                return redirect()->route('admin.caisses.index')->with('success', 'Facture supprimée !');
            }

            return redirect()->route('caisses.index')->with('success', 'Facture supprimée !');
        }

        return back()->with('error', 'Erreur lors de la suppression');
    }

    public function exportPdf(Caisse $caisse)
    {
        // Charger les relations nécessaires
        $caisse->load(['patient', 'medecin', 'prescripteur', 'examen', 'service']);

        $pdf = Pdf::loadView('caisses.export', compact('caisse'));
        return $pdf->download('examen-' . $caisse->numero_entre . '.pdf');
    }

    public function print()
    {
        $caisses = Caisse::with(['patient', 'medecin', 'prescripteur', 'examen', 'service'])->get();
        return view('caisses.print', compact('caisses'));
    }

    public function printSingle($id)
    {
        $caisse = Caisse::with(['patient', 'medecin', 'prescripteur', 'examen.service.pharmacie'])->findOrFail($id);
        return view('caisses.print', compact('caisse'));
    }

    public function getNextNumeroEntree($medecinId)
    {
        $today = now()->startOfDay();

        // Récupérer tous les numéros d'entrée utilisés aujourd'hui pour ce médecin
        // (caisses + rendez-vous) pour éviter les doublons
        $numerosCaisses = Caisse::where('medecin_id', $medecinId)
            ->whereDate('created_at', $today)
            ->pluck('numero_entre')
            ->toArray();

        $numerosRendezVous = \App\Models\RendezVous::where('medecin_id', $medecinId)
            ->whereDate('created_at', $today)
            ->pluck('numero_entree')
            ->toArray();

        // Fusionner et trier tous les numéros utilisés
        $numerosUtilises = array_merge($numerosCaisses, $numerosRendezVous);
        sort($numerosUtilises);

        // Trouver le prochain numéro disponible
        $prochainNumero = 1;
        foreach ($numerosUtilises as $numero) {
            if ($numero >= $prochainNumero) {
                $prochainNumero = $numero + 1;
            }
        }

        return $prochainNumero;
    }
}
