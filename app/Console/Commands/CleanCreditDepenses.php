<?php

namespace App\Console\Commands;

use App\Models\Depense;
use App\Models\Credit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Exception;

class CleanCreditDepenses extends Command
{
    protected $signature = 'clean:credit-depenses
                            {--dry-run : Afficher seulement les dépenses sans les supprimer}
                            {--backup : Créer une sauvegarde avant suppression}
                            {--force : Forcer la suppression sans confirmation}';

    protected $description = 'Nettoyer les dépenses liées aux crédits personnel';

    public function handle()
    {
        $this->info('🔒 Nettoyage sécurisé des dépenses de crédits personnel');
        $this->line('==============================================================');

        // 1. ANALYSE DES DONNÉES
        $this->info('📊 ANALYSE DES DONNÉES :');

        $depensesPersonnel = Depense::where('mode_paiement_id', 'salaire')
            ->orWhere('nom', 'like', '%Déduction salaire%')
            ->orWhere('nom', 'like', '%Crédit personnel%')
            ->get();

        $this->info("Dépenses de crédits personnel trouvées : {$depensesPersonnel->count()}");

        if ($depensesPersonnel->count() === 0) {
            $this->info('✅ Aucune dépense de crédit personnel à nettoyer.');
            return 0;
        }

        // Afficher les détails
        $this->line("\n📋 Détails des dépenses :");
        $totalMontant = 0;
        foreach ($depensesPersonnel as $depense) {
            $this->line("   - ID: {$depense->id} | {$depense->nom} | {$depense->montant} MRU | {$depense->created_at}");
            $totalMontant += $depense->montant;
        }
        $this->info("   Total : {$totalMontant} MRU");

        // 2. SAUVEGARDE (si demandée)
        if ($this->option('backup')) {
            $this->line("\n💾 SAUVEGARDE :");
            $backupFile = 'backup_credit_depenses_' . date('Y-m-d_H-i-s') . '.json';
            $backupData = [
                'date' => now()->toISOString(),
                'total_depenses' => $depensesPersonnel->count(),
                'total_montant' => $totalMontant,
                'depenses' => $depensesPersonnel->toArray()
            ];

            file_put_contents($backupFile, json_encode($backupData, JSON_PRETTY_PRINT));
            $this->info("✅ Sauvegarde créée : {$backupFile}");
        }

        // 3. DRY RUN
        if ($this->option('dry-run')) {
            $this->warn("\n🔍 MODE DRY-RUN : Aucune suppression effectuée");
            $this->info("Pour effectuer la suppression, relancez sans --dry-run");
            return 0;
        }

        // 4. VALIDATION
        if (!$this->option('force')) {
            $this->line("\n⚠️  VALIDATION :");
            if (!$this->confirm("Voulez-vous vraiment supprimer ces {$depensesPersonnel->count()} dépenses ?")) {
                $this->warn('❌ Opération annulée par l\'utilisateur.');
                return 0;
            }
        }

        // 5. SUPPRESSION
        $this->line("\n🗑️  SUPPRESSION :");

        try {
            DB::beginTransaction();

            $deletedCount = 0;
            foreach ($depensesPersonnel as $depense) {
                $this->line("   - Suppression : {$depense->nom} ({$depense->montant} MRU)");
                $depense->delete();
                $deletedCount++;
            }

            DB::commit();

            $this->info("\n✅ SUPPRESSION TERMINÉE :");
            $this->info("   - {$deletedCount} dépenses supprimées");
        } catch (Exception $e) {
            DB::rollBack();
            $this->error("\n❌ ERREUR LORS DE LA SUPPRESSION :");
            $this->error("   - Erreur : " . $e->getMessage());
            $this->error("   - Aucune donnée n'a été supprimée");
            return 1;
        }

        // 6. VÉRIFICATION
        $this->line("\n🔍 VÉRIFICATION :");
        $remainingDepenses = Depense::where('mode_paiement_id', 'salaire')
            ->orWhere('nom', 'like', '%Déduction salaire%')
            ->orWhere('nom', 'like', '%Crédit personnel%')
            ->count();

        if ($remainingDepenses === 0) {
            $this->info("✅ Aucune dépense de crédit personnel restante.");
        } else {
            $this->warn("⚠️  {$remainingDepenses} dépenses de crédit personnel restantes.");
        }

        $this->info("\n✨ Nettoyage terminé avec succès !");
        return 0;
    }
}
