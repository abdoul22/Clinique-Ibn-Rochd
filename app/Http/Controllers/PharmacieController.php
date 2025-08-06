<?php

namespace App\Http\Controllers;

use App\Models\Pharmacie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PharmacieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pharmacie::query();

        // Filtrage par recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom_medicament', 'like', "%{$search}%")
                    ->orWhere('categorie', 'like', "%{$search}%")
                    ->orWhere('fournisseur', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtrage par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtrage par stock
        if ($request->filled('stock')) {
            switch ($request->stock) {
                case 'en_stock':
                    $query->where('stock', '>', 0);
                    break;
                case 'rupture':
                    $query->where('stock', 0);
                    break;
                case 'faible':
                    $query->where('stock', '<=', 10)->where('stock', '>', 0);
                    break;
            }
        }

        // Filtrage par catégorie
        if ($request->filled('categorie')) {
            $query->where('categorie', 'like', "%{$request->categorie}%");
        }

        $pharmacies = $query->orderBy('created_at', 'desc')->paginate(10);

        // Calculer les statistiques pour le résumé
        $statsQuery = Pharmacie::query();

        // Appliquer les mêmes filtres pour les statistiques
        if ($request->filled('search')) {
            $search = $request->search;
            $statsQuery->where(function ($q) use ($search) {
                $q->where('nom_medicament', 'like', "%{$search}%")
                    ->orWhere('categorie', 'like', "%{$search}%")
                    ->orWhere('fournisseur', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('statut')) {
            $statsQuery->where('statut', $request->statut);
        }

        if ($request->filled('stock')) {
            switch ($request->stock) {
                case 'en_stock':
                    $statsQuery->where('stock', '>', 0);
                    break;
                case 'rupture':
                    $statsQuery->where('stock', 0);
                    break;
                case 'faible':
                    $statsQuery->where('stock', '<=', 10)->where('stock', '>', 0);
                    break;
            }
        }

        if ($request->filled('categorie')) {
            $statsQuery->where('categorie', 'like', "%{$request->categorie}%");
        }

        $stats = $statsQuery->selectRaw('
            COUNT(*) as total_medicaments,
            SUM(stock) as total_stock,
            SUM(stock * prix_achat) as valeur_stock_achat,
            SUM(stock * prix_vente) as valeur_stock_vente,
            SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as medicaments_rupture,
            SUM(CASE WHEN stock <= 10 AND stock > 0 THEN 1 ELSE 0 END) as medicaments_faible_stock,
            AVG(prix_vente - prix_achat) as marge_moyenne_absolue,
            AVG(CASE WHEN prix_achat > 0 THEN ((prix_vente - prix_achat) / prix_achat * 100) ELSE 0 END) as marge_moyenne_pourcentage,
            SUM(CASE WHEN prix_achat > 0 THEN (prix_vente - prix_achat) * stock ELSE 0 END) as benefice_potentiel_total
        ')->first();

        $resume = [
            'total_medicaments' => $stats->total_medicaments ?? 0,
            'total_stock' => $stats->total_stock ?? 0,
            'valeur_stock_achat' => $stats->valeur_stock_achat ?? 0,
            'valeur_stock_vente' => $stats->valeur_stock_vente ?? 0,
            'medicaments_rupture' => $stats->medicaments_rupture ?? 0,
            'medicaments_faible_stock' => $stats->medicaments_faible_stock ?? 0,
            'marge_moyenne_absolue' => $stats->marge_moyenne_absolue ?? 0,
            'marge_moyenne_pourcentage' => $stats->marge_moyenne_pourcentage ?? 0,
            'benefice_potentiel_total' => $stats->benefice_potentiel_total ?? 0,
        ];

        return view('pharmacie.index', compact('pharmacies', 'resume'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pharmacie.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom_medicament' => 'required|string|max:255',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'prix_unitaire' => 'required|numeric|min:0',
            'quantite' => 'required|integer|min:1',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'categorie' => 'nullable|string|max:255',
            'fournisseur' => 'nullable|string|max:255',
            'date_expiration' => 'nullable|date|after:today',
            'statut' => 'required|in:actif,inactif,rupture',
        ]);

        Pharmacie::create($request->all());

        return redirect()->route('pharmacie.index')
            ->with('success', 'Médicament ajouté avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pharmacie = Pharmacie::findOrFail($id);
        $pharmacie->load('services');

        // Statistiques détaillées du médicament
        $servicesCount = $pharmacie->services->count();
        $valeurStockAchat = $pharmacie->stock * $pharmacie->prix_achat;
        $valeurStockVente = $pharmacie->stock * $pharmacie->prix_vente;
        $margeBeneficiaire = $pharmacie->marge_beneficiaire;
        $pourcentageMarge = $pharmacie->prix_achat > 0 ? (($pharmacie->prix_vente - $pharmacie->prix_achat) / $pharmacie->prix_achat) * 100 : 0;

        // Calculer les ventes potentielles et bénéfices
        $ventesPotentielles = $pharmacie->stock * $pharmacie->prix_vente;
        $beneficePotentiel = $pharmacie->stock * ($pharmacie->prix_vente - $pharmacie->prix_achat);

        // Calculer le prix unitaire réel (si différent du prix de vente)
        $prixUnitaireReel = $pharmacie->prix_unitaire ?? $pharmacie->prix_vente;
        $valeurStockUnitaire = $pharmacie->stock * $prixUnitaireReel;

        $stats = [
            'services_lies' => $servicesCount,
            'valeur_stock_achat' => $valeurStockAchat,
            'valeur_stock_vente' => $valeurStockVente,
            'valeur_stock_unitaire' => $valeurStockUnitaire,
            'marge_beneficiaire' => $margeBeneficiaire,
            'pourcentage_marge' => $pourcentageMarge,
            'ventes_potentielles' => $ventesPotentielles,
            'benefice_potentiel' => $beneficePotentiel,
            'statut_stock' => $pharmacie->stock == 0 ? 'Rupture' : ($pharmacie->stock <= 10 ? 'Faible' : 'OK'),
            'prix_unitaire_reel' => $prixUnitaireReel,
        ];

        return view('pharmacie.show', compact('pharmacie', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $pharmacie = Pharmacie::findOrFail($id);
        return view('pharmacie.edit', compact('pharmacie'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nom_medicament' => 'required|string|max:255',
            'prix_achat' => 'required|numeric|min:0',
            'prix_vente' => 'required|numeric|min:0',
            'prix_unitaire' => 'required|numeric|min:0',
            'quantite' => 'required|integer|min:1',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'categorie' => 'nullable|string|max:255',
            'fournisseur' => 'nullable|string|max:255',
            'date_expiration' => 'nullable|date',
            'statut' => 'required|in:actif,inactif,rupture',
        ]);

        $pharmacie = Pharmacie::findOrFail($id);
        $pharmacie->update($request->all());

        return redirect()->route('pharmacie.show', $pharmacie->id)
            ->with('success', 'Médicament mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pharmacie = Pharmacie::findOrFail($id);

        // Supprimer le médicament (les services et examens liés seront supprimés automatiquement)
        $pharmacie->delete();

        return redirect()->route('pharmacie.index')
            ->with('success', 'Médicament supprimé avec succès.');
    }

    /**
     * API pour récupérer les médicaments (utilisé par les services)
     */
    public function getMedicaments()
    {
        $medicaments = Pharmacie::where('stock', '>', 0)
            ->where('statut', 'actif')
            ->select('id', 'nom_medicament', 'prix_vente', 'prix_unitaire', 'stock')
            ->get();

        return response()->json($medicaments);
    }

    /**
     * API pour récupérer un médicament spécifique
     */
    public function getMedicament($id)
    {
        $medicament = Pharmacie::findOrFail($id);

        return response()->json([
            'id' => $medicament->id,
            'nom_medicament' => $medicament->nom_medicament,
            'prix_vente' => $medicament->prix_vente,
            'prix_unitaire' => $medicament->prix_unitaire,
            'stock' => $medicament->stock,
            'stock_suffisant' => $medicament->stock > 0
        ]);
    }

    /**
     * Déduire du stock (appelé depuis la caisse)
     */
    public function deduireStock(Request $request, $id)
    {
        $request->validate([
            'quantite' => 'required|integer|min:1'
        ]);

        $medicament = Pharmacie::findOrFail($id);

        if ($medicament->deduireStock($request->quantite)) {
            return response()->json([
                'success' => true,
                'message' => 'Stock déduit avec succès',
                'nouveau_stock' => $medicament->stock
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Stock insuffisant'
        ], 400);
    }
}
