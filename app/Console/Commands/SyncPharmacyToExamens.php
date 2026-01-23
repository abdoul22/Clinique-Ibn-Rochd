<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pharmacie;
use App\Models\Service;
use App\Models\Examen;

class SyncPharmacyToExamens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pharmacy:sync-examens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchroniser tous les médicaments de la pharmacie avec la table examens';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Synchronisation des médicaments avec les examens...');
        
        // Récupérer ou créer un service PHARMACIE générique
        $servicePharmacie = Service::where('type_service', 'PHARMACIE')
            ->whereNull('pharmacie_id')
            ->first();
        
        if (!$servicePharmacie) {
            $this->info('📦 Création du service PHARMACIE générique...');
            $servicePharmacie = Service::create([
                'nom' => 'Pharmacie',
                'type_service' => 'PHARMACIE',
                'description' => 'Service générique pour les médicaments',
            ]);
        }
        
        // Récupérer tous les médicaments actifs
        $medicaments = Pharmacie::where('statut', 'actif')->get();
        $this->info("📊 {$medicaments->count()} médicaments trouvés dans la pharmacie");
        
        $created = 0;
        $updated = 0;
        $skipped = 0;
        
        foreach ($medicaments as $medicament) {
            // Vérifier si un examen existe déjà pour ce médicament
            $examen = Examen::where('nom', $medicament->nom_medicament)
                ->where('idsvc', $servicePharmacie->id)
                ->first();
            
            if ($examen) {
                // Mettre à jour l'examen existant
                $examen->update([
                    'tarif' => $medicament->prix_vente,
                    'part_cabinet' => $medicament->prix_vente,
                    'part_medecin' => 0,
                ]);
                $updated++;
                $this->line("  ✅ Mis à jour: {$medicament->nom_medicament}");
            } else {
                // Créer un nouvel examen
                Examen::create([
                    'nom' => $medicament->nom_medicament,
                    'idsvc' => $servicePharmacie->id,
                    'tarif' => $medicament->prix_vente,
                    'part_cabinet' => $medicament->prix_vente,
                    'part_medecin' => 0,
                ]);
                $created++;
                $this->line("  ➕ Créé: {$medicament->nom_medicament}");
            }
        }
        
        $this->newLine();
        $this->info("✨ Synchronisation terminée !");
        $this->table(
            ['Action', 'Nombre'],
            [
                ['Créés', $created],
                ['Mis à jour', $updated],
                ['Total traités', $created + $updated],
            ]
        );
        
        return Command::SUCCESS;
    }
}


