<?php

require_once 'vendor/autoload.php';

use App\Models\Depense;
use App\Models\EtatCaisse;

// Initialiser Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CORRECTION DES DÉPENSES SANS MODE DE PAIEMENT ===\n\n";

// Trouver les dépenses sans mode de paiement
$depensesSansMode = Depense::whereNull('mode_paiement_id')->orWhere('mode_paiement_id', '')->get();

if ($depensesSansMode->isEmpty()) {
    echo "✅ Aucune dépense sans mode de paiement trouvée.\n";
    exit;
}

echo "🔧 Dépenses à corriger : {$depensesSansMode->count()}\n\n";

foreach ($depensesSansMode as $depense) {
    echo "📋 Dépense #{$depense->id}: {$depense->nom} - {$depense->montant} MRU\n";

    // Si c'est une dépense liée à un état de caisse, récupérer le mode de paiement de la caisse
    if ($depense->etat_caisse_id) {
        $etatCaisse = EtatCaisse::with('caisse.mode_paiements')->find($depense->etat_caisse_id);

        if ($etatCaisse && $etatCaisse->caisse && $etatCaisse->caisse->mode_paiements->isNotEmpty()) {
            $modePaiement = $etatCaisse->caisse->mode_paiements->first();
            $depense->update(['mode_paiement_id' => $modePaiement->type]);
            echo "   ✅ Corrigée avec le mode: {$modePaiement->type}\n";
        } else {
            // Mode par défaut si pas de caisse liée
            $depense->update(['mode_paiement_id' => 'espèces']);
            echo "   ✅ Corrigée avec le mode par défaut: espèces\n";
        }
    } else {
        // Mode par défaut pour les dépenses manuelles
        $depense->update(['mode_paiement_id' => 'espèces']);
        echo "   ✅ Corrigée avec le mode par défaut: espèces\n";
    }
}

echo "\n✅ Correction terminée !\n";
