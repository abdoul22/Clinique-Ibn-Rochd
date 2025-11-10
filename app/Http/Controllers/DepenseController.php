<?php

namespace App\Http\Controllers;

use App\Models\Depense;
use App\Models\PaymentMode;
use App\Models\ModePaiement;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepenseController extends Controller
{
    public function index(Request $request)
    {
        // Bloquer l'accès pour les admins
        if (Auth::check() && Auth::user()->role && Auth::user()->role->name === 'admin') {
            abort(403, 'Accès refusé. Les administrateurs ne peuvent pas consulter la liste des dépenses.');
        }

        $period = $request->input('period', 'day');
        $date = $request->input('date');
        $week = $request->input('week');
        $month = $request->input('month');
        $year = $request->input('year');
        $dateStart = $request->input('date_start');
        $dateEnd = $request->input('date_end');

        $query = Depense::with(['modePaiement', 'credit', 'creator']);
        $query->where('rembourse', false);

        // Filtrage par période - seulement si les paramètres sont fournis
        if ($period === 'day' && $date) {
            $query->whereDate('created_at', $date);
        } elseif ($period === 'week' && $week) {
            $parts = explode('-W', $week);
            if (count($parts) === 2) {
                $yearW = (int)$parts[0];
                $weekW = (int)$parts[1];
                $startOfWeek = \Carbon\Carbon::now()->setISODate($yearW, $weekW)->startOfWeek();
                $endOfWeek = \Carbon\Carbon::now()->setISODate($yearW, $weekW)->endOfWeek();
                $query->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
            }
        } elseif ($period === 'month' && $month) {
            $parts = explode('-', $month);
            if (count($parts) === 2) {
                $yearM = (int)$parts[0];
                $monthM = (int)$parts[1];
                $query->whereYear('created_at', $yearM)->whereMonth('created_at', $monthM);
            }
        } elseif ($period === 'year' && $year) {
            $query->whereYear('created_at', $year);
        } elseif ($period === 'range' && $dateStart && $dateEnd) {
            $query->whereBetween('created_at', [$dateStart, $dateEnd]);
        }
        // Si period=day mais pas de date, afficher toutes les dépenses (pas de filtre)

        if ($request->has('search')) {
            $query->where('nom', 'like', '%' . $request->search . '%');
        }

        if ($request->has('source') && in_array($request->source, ['manuelle', 'automatique', 'part_medecin'])) {
            if ($request->source === 'part_medecin') {
                $query->where('nom', 'like', '%Part médecin%');
            } else {
                $query->where('source', $request->source);
            }
        }

        if ($request->has('mode_paiement') && in_array($request->mode_paiement, ['espèces', 'bankily', 'masrivi', 'sedad'])) {
            $query->where('mode_paiement_id', $request->mode_paiement);
        }

        $depenses = $query->latest()->paginate(10)->withQueryString();

        // Calculer le total des dépenses (excluant les crédits personnel et assurance)
        $totalDepenses = Depense::where('rembourse', false)->sum('montant');

        return view('depenses.index', ['depenses' => $depenses]);
    }

    public function create()
    {
        $modes = \App\Models\ModePaiement::getTypes();
        return view('depenses.create', compact('modes'));
    }


    public function store(Request $request)
    {
        $modesDisponibles = \App\Models\ModePaiement::getTypes();
        $modesString = implode(',', $modesDisponibles);

        $request->validate([
            'nom' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'mode_paiement_id' => "required|string|in:$modesString",
        ]);

        if (str_contains(request('nom'), 'Part médecin')) {
            abort(403, 'Création manuelle des parts médecin interdite.');
        }

        // Convertir le montant en nombre (entier car la colonne est integer)
        $montant = (int) round((float) $request->montant);

        // Vérification du solde du mode de paiement (aligné au dashboard)
        $entree = \App\Models\EtatCaisse::whereNotNull('caisse_id')->whereHas('caisse.mode_paiements', function ($query) use ($request) {
            $query->where('type', $request->mode_paiement_id);
        })->sum('recette');

        // Ajouter les paiements de crédits d'assurance
        $entree += \App\Models\ModePaiement::where('type', $request->mode_paiement_id)
            ->whereNull('caisse_id')
            ->where('source', 'credit_assurance')
            ->sum('montant');

        // Calculer les sorties (dépenses)
        $sortie = \App\Models\Depense::where('mode_paiement_id', $request->mode_paiement_id)
            ->where('rembourse', false)
            ->sum('montant');

        $soldeDisponible = $entree - $sortie;

        if ($montant > $soldeDisponible) {
            return back()->withErrors([
                'mode_paiement_id' => "Fonds insuffisants dans le mode de paiement {$request->mode_paiement_id}. Solde disponible : " . number_format($soldeDisponible, 2) . " MRU"
            ])->withInput();
        }

        // Le contrôle par mode est suffisant et cohérent avec le dashboard.

        // Utiliser une transaction pour s'assurer que les deux créations réussissent ou échouent ensemble
        try {
            DB::beginTransaction();

            // Créer un nouvel enregistrement ModePaiement pour la sortie
            $modePaiement = ModePaiement::create([
                'type' => $request->mode_paiement_id,
                'montant' => -$montant, // Montant négatif pour sortie
                'source' => 'depense'
            ]);

            // Log pour débogage
            Log::info('ModePaiement créé', [
                'mode_paiement_id' => $modePaiement->id,
                'type' => $modePaiement->type,
                'montant' => $modePaiement->montant,
                'user_id' => Auth::id(),
                'user_role' => Auth::user()->role?->name ?? 'N/A'
            ]);

            // Créer la dépense
            $depense = Depense::create([
                'nom' => $request->nom,
                'montant' => $montant,
                'mode_paiement_id' => $request->mode_paiement_id,
                'source' => 'manuelle',
                'created_by' => Auth::id(),
            ]);

            // Log pour débogage
            Log::info('Depense créée', [
                'depense_id' => $depense->id,
                'nom' => $depense->nom,
                'montant' => $depense->montant,
                'mode_paiement_id' => $depense->mode_paiement_id,
                'created_by' => $depense->created_by,
                'user_role' => Auth::user()->role?->name ?? 'N/A'
            ]);

            DB::commit();

            // Rediriger vers le dashboard admin si admin, sinon vers index
            if (Auth::check() && Auth::user()->role && Auth::user()->role->name === 'admin') {
                return redirect()->route('dashboard.admin')->with('success', 'Dépense ajoutée avec succès.');
            }
            
            return redirect()->route('depenses.index')->with('success', 'Dépense ajoutée avec succès.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log l'erreur pour débogage
            Log::error('Erreur lors de la création de la dépense', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'user_role' => Auth::user()->role?->name ?? 'N/A',
                'request_data' => $request->all()
            ]);

            return back()->withErrors([
                'error' => 'Une erreur est survenue lors de la création de la dépense. Veuillez réessayer.'
            ])->withInput();
        }
    }

    public function show($id)
    {
        // Bloquer l'accès pour les admins
        if (Auth::check() && Auth::user()->role && Auth::user()->role->name === 'admin') {
            abort(403, 'Accès refusé. Les administrateurs ne peuvent pas consulter les détails des dépenses.');
        }

        try {
            $depense = Depense::with(['creator.role', 'credit'])->findOrFail($id);
            return view('depenses.show', compact('depense'));
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'affichage de la dépense', [
                'depense_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, 'Une erreur est survenue lors du chargement de la dépense.');
        }
    }

    public function edit($id)
    {
        // Bloquer l'accès pour les admins
        if (Auth::check() && Auth::user()->role && Auth::user()->role->name === 'admin') {
            abort(403, 'Accès refusé. Les administrateurs ne peuvent pas modifier les dépenses.');
        }

        $depense = Depense::findOrFail($id);
        return view('depenses.edit', compact('depense'));
    }

    public function update(Request $request, $id)
    {
        // Bloquer l'accès pour les admins
        if (Auth::check() && Auth::user()->role && Auth::user()->role->name === 'admin') {
            abort(403, 'Accès refusé. Les administrateurs ne peuvent pas modifier les dépenses.');
        }

        $request->validate([
            'nom' => 'required|string|max:255',
            'montant' => 'required|string|max:255',
        ]);

        $depense = Depense::findOrFail($id);
        $depense->update($request->all());
        return redirect()->route('depenses.index')->with('success', 'Dépense mise à jour avec succès.');
    }

    public function destroy($id)
    {
        // Bloquer l'accès pour les admins
        if (Auth::check() && Auth::user()->role && Auth::user()->role->name === 'admin') {
            abort(403, 'Accès refusé. Les administrateurs ne peuvent pas supprimer les dépenses.');
        }

        $depense = Depense::findOrFail($id);
        $depense->delete();
        return redirect()->route('depenses.index')->with('success', 'Dépense supprimée.');
    }

    public function exportPdf()
    {
        $depenses = Depense::all();
        $pdf = Pdf::loadView('depenses.export_pdf', compact('depenses'));
        return $pdf->download('depenses.pdf');
    }

    public function print()
    {
        $depenses = Depense::all();
        return view('depenses.print', compact('depenses'));
    }
}
