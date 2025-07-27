<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Caisse;
use App\Models\EtatCaisse;

echo "=== DIAGNOSTIC CAISSE #20 ===\n\n";

$caisse = Caisse::with(['patient', 'medecin', 'assurance'])->find(20);

if (!$caisse) {
    echo "❌ Caisse #20 introuvable\n";
    exit;
}

echo "📊 DONNÉES CAISSE #20 :\n";
echo "- ID: " . $caisse->id . "\n";
echo "- Numero entre: " . $caisse->numero_entre . "\n";
echo "- Patient: " . ($caisse->patient ? $caisse->patient->nom . ' ' . $caisse->patient->prenom : 'N/A') . "\n";
echo "- Medecin: " . ($caisse->medecin ? 'Dr. ' . $caisse->medecin->nom . ' ' . $caisse->medecin->prenom : 'N/A') . "\n";
echo "- Total: " . $caisse->total . " MRU\n";
echo "- Assurance: " . ($caisse->assurance ? $caisse->assurance->nom : 'Aucune') . "\n";
echo "- Couverture: " . ($caisse->couverture ?? 0) . "%\n";
echo "- Nom caissier: " . $caisse->nom_caissier . "\n";
echo "- Date examen: " . $caisse->date_examen . "\n";
echo "- Created at: " . $caisse->created_at . "\n";
echo "- Updated at: " . $caisse->updated_at . "\n\n";

// Chercher l'état de caisse correspondant
$etatCaisse = EtatCaisse::where('caisse_id', 20)->first();

if ($etatCaisse) {
    echo "🏦 ÉTAT DE CAISSE CORRESPONDANT (ID: {$etatCaisse->id}) :\n";
    echo "- Designation: " . $etatCaisse->designation . "\n";
    echo "- Recette: " . $etatCaisse->recette . " MRU\n";
    echo "- Assurance ID: " . ($etatCaisse->assurance_id ?? 'NULL') . "\n";
    echo "- Medecin ID: " . ($etatCaisse->medecin_id ?? 'NULL') . "\n";
    echo "- Created at: " . $etatCaisse->created_at . "\n";
    echo "- Updated at: " . $etatCaisse->updated_at . "\n\n";
} else {
    echo "❌ Aucun état de caisse trouvé pour la caisse #20\n\n";
}

echo "🔍 VÉRIFICATIONS :\n";
echo "- La caisse existe : " . ($caisse ? 'OUI' : 'NON') . "\n";
echo "- La caisse a un état correspondant : " . ($etatCaisse ? 'OUI' : 'NON') . "\n";
echo "- Dernière modification caisse : " . $caisse->updated_at->diffForHumans() . "\n";
if ($etatCaisse) {
    echo "- Dernière modification état : " . $etatCaisse->updated_at->diffForHumans() . "\n";
}

echo "\n💡 RECOMMANDATIONS :\n";
echo "1. Vérifiez si les modifications sont réellement sauvegardées\n";
echo "2. Assurez-vous que l'état de caisse est mis à jour en même temps\n";
echo "3. Vérifiez la logique de redirection après modification\n";
