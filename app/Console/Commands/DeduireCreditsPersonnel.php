<?php

namespace App\Console\Commands;

use App\Models\Personnel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeduireCreditsPersonnel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'credits:deduire-personnel {--force : Forcer la déduction même si pas fin de mois}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Déduire automatiquement les crédits du salaire du personnel à la fin du mois';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== DÉDUCTION AUTOMATIQUE DES CRÉDITS PERSONNEL ===');

        // 1) Vérifier si l'auto-déduction est activée, sinon sortir immédiatement
        if (!config('payroll.auto_deduct', false)) {
            $this->warn('Auto-déduction désactivée (config/payroll.php). Aucune action.');
            return 0;
        }

        // 2) Vérifier si c'est la fin du mois (optionnel avec --force)
        if (!$this->option('force') && !$this->estFinDeMois()) {
            $this->warn('Ce n\'est pas la fin du mois. Utilisez --force pour forcer la déduction.');
            return 1;
        }

        $personnels = Personnel::where('credit', '>', 0)->get();

        if ($personnels->isEmpty()) {
            $this->info('Aucun personnel avec des crédits en cours.');
            return 0;
        }

        $this->info("Traitement de {$personnels->count()} personnel(s) avec des crédits...");

        $totalDeduit = 0;
        $personnelsTraites = 0;

        foreach ($personnels as $personnel) {
            $this->line("Traitement de {$personnel->nom}...");

            $creditAvant = $personnel->credit;
            $salaireAvant = $personnel->salaire;

            $montantDeduit = $personnel->deduireCreditDuSalaire();

            if ($montantDeduit > 0) {
                $totalDeduit += $montantDeduit;
                $personnelsTraites++;

                $this->info("  ✓ Crédit déduit : {$montantDeduit} MRU");
                $this->line("    Salaire avant : {$salaireAvant} MRU");
                $this->line("    Salaire après : {$personnel->salaire} MRU");
                $this->line("    Crédit avant : {$creditAvant} MRU");
                $this->line("    Crédit après : {$personnel->credit} MRU");

                // Log de l'opération
                Log::info('Déduction crédit personnel', [
                    'personnel_id' => $personnel->id,
                    'personnel_nom' => $personnel->nom,
                    'montant_deduit' => $montantDeduit,
                    'salaire_avant' => $salaireAvant,
                    'salaire_apres' => $personnel->salaire,
                    'credit_avant' => $creditAvant,
                    'credit_apres' => $personnel->credit,
                    'date' => now()->toDateString()
                ]);
            } else {
                $this->warn("  ⚠ Aucun crédit à déduire pour {$personnel->nom}");
            }

            $this->line('');
        }

        $this->info("=== RÉSUMÉ ===");
        $this->info("Personnels traités : {$personnelsTraites}");
        $this->info("Total déduit : {$totalDeduit} MRU");

        if ($totalDeduit > 0) {
            $this->info("✅ Déduction terminée avec succès !");
        } else {
            $this->warn("⚠ Aucune déduction effectuée.");
        }

        return 0;
    }

    /**
     * Vérifier si c'est la fin du mois
     */
    private function estFinDeMois()
    {
        $aujourdhui = now();
        $dernierJourDuMois = $aujourdhui->endOfMonth();

        // Considérer comme fin de mois si on est dans les 3 derniers jours
        return $aujourdhui->diffInDays($dernierJourDuMois) <= 3;
    }
}
