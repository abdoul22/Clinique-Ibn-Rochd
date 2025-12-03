<?php

use App\Models\EtatCaisse;
use App\Models\Depense;
use App\Models\ModePaiement;
use Carbon\Carbon;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Début de la correction des dates des dépenses et paiements...\n";

// Récupérer tous les états de caisse validés avec une part médecin
$etats = EtatCaisse::where('validated', true)
    ->where('part_medecin', '>', 0)
    ->with(['caisse', 'depense'])
    ->get();

$countDepenses = 0;
$countPaiements = 0;

foreach ($etats as $etat) {
    // 1. Correction de la Dépense liée
    // Attention : 'depense' est aussi un attribut (montant) sur EtatCaisse, donc conflit de nom avec la relation
    $depense = Depense::where('etat_caisse_id', $etat->id)->first();

    if ($depense) {
        $oldDate = $depense->created_at instanceof \DateTime ? $depense->created_at : Carbon::parse($depense->created_at);
        $targetDate = $etat->created_at instanceof \DateTime ? $etat->created_at : Carbon::parse($etat->created_at);

        // On compare les dates (Y-m-d) pour voir s'il y a un décalage
        if ($oldDate->format('Y-m-d') !== $targetDate->format('Y-m-d')) {
            $depense->created_at = $targetDate;
            $depense->updated_at = $targetDate; // On garde sync
            $depense->save();
            $countDepenses++;
            // echo "Dépense corrigée pour Etat #{$etat->id} : {$oldDate->toDateString()} -> {$targetDate->toDateString()}\n";
        }

        // 2. Correction du ModePaiement
        // Le ModePaiement n'est pas directement lié par ID dans le code actuel (créé comme 'part_medecin' avec montant négatif)
        // On va essayer de le trouver avec l'ancienne date (souvent la date de la facture)
        
        $invoiceDate = $etat->caisse ? $etat->caisse->created_at : null;
        
        // Si on n'a pas de caisse, on ne peut pas deviner l'ancienne date facilement (peut-être $depense->created_at avant modif ?)
        // Utilisons $oldDate récupérée ci-dessus si $invoiceDate est null
        $searchDate = $invoiceDate ?? $oldDate;

        if ($searchDate) {
             // On cherche le paiement correspondant :
             // - Source = part_medecin
             // - Montant = -part_medecin (négatif)
             // - Date proche de l'ancienne date
             
             $paiement = ModePaiement::where('source', 'part_medecin')
                ->where('montant', -$etat->part_medecin)
                ->whereBetween('created_at', [
                    $searchDate->copy()->subMinutes(1), // Marge de 1 minute
                    $searchDate->copy()->addMinutes(1)
                ])
                ->first();
                
             if ($paiement) {
                 if ($paiement->created_at->format('Y-m-d') !== $targetDate->format('Y-m-d')) {
                     $paiement->created_at = $targetDate;
                     $paiement->updated_at = $targetDate;
                     $paiement->save();
                     $countPaiements++;
                 }
             }
        }
    }
}

echo "---------------------------------------------------\n";
echo "Résultat :\n";
echo "Dépenses corrigées : $countDepenses\n";
echo "Paiements corrigés : $countPaiements\n";
echo "Terminé.\n";

