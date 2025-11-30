<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Depense;
use App\Models\ModePaiement;
use App\Models\User;
use Carbon\Carbon;

class DebugDepenses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:depenses {--limit=10} {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug les dépenses créées par les admins - Vérifie si elles sont enregistrées et pourquoi elles ne s\'affichent pas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');
        $date = $this->option('date');

        $this->info('=== DÉBOGAGE DES DÉPENSES ===');
        $this->newLine();

        // 1. Lister les dernières dépenses avec tous leurs champs
        $this->info('1. LISTE DES DERNIÈRES DÉPENSES');
        $this->line('─────────────────────────────────────────────────────────');
        
        $query = Depense::with(['creator', 'modePaiement', 'credit'])
            ->orderBy('created_at', 'desc')
            ->limit($limit);

        if ($date) {
            $query->whereDate('created_at', $date);
            $this->info("Filtrage par date: {$date}");
        }

        $depenses = $query->get();

        if ($depenses->isEmpty()) {
            $this->warn('Aucune dépense trouvée.');
        } else {
            $this->info("Nombre de dépenses trouvées: {$depenses->count()}");
            $this->newLine();

            $tableData = [];
            foreach ($depenses as $depense) {
                $creator = $depense->creator ? $depense->creator->name . ' (ID: ' . $depense->creator->id . ')' : 'N/A';
                $creatorRole = $depense->creator && $depense->creator->role ? $depense->creator->role->name : 'N/A';
                
                $tableData[] = [
                    'ID' => $depense->id,
                    'Nom' => $depense->nom,
                    'Montant' => number_format($depense->montant, 0, ',', ' ') . ' MRU',
                    'Source' => $depense->source ?? 'N/A',
                    'Mode Paiement' => $depense->mode_paiement_id ?? 'N/A',
                    'Remboursé' => $depense->rembourse ? 'Oui' : 'Non',
                    'Créé par' => $creator,
                    'Rôle créateur' => $creatorRole,
                    'Date création' => $depense->created_at ? $depense->created_at->format('Y-m-d H:i:s') : 'N/A',
                ];
            }

            $this->table([
                'ID',
                'Nom',
                'Montant',
                'Source',
                'Mode Paiement',
                'Remboursé',
                'Créé par',
                'Rôle créateur',
                'Date création'
            ], $tableData);
        }

        $this->newLine();

        // 2. Vérifier les filtres appliqués dans DepenseController::index()
        $this->info('2. TEST DE LA REQUÊTE DepenseController::index()');
        $this->line('─────────────────────────────────────────────────────────');
        
        // Simuler la requête exacte de DepenseController::index()
        $period = 'day';
        $dateFilter = null; // Pas de date par défaut
        
        $queryIndex = Depense::with(['modePaiement', 'credit', 'creator']);
        $queryIndex->where('rembourse', false);

        // Filtrage par période - seulement si les paramètres sont fournis
        if ($period === 'day' && $dateFilter) {
            $queryIndex->whereDate('created_at', $dateFilter);
        }
        // Si period=day mais pas de date, afficher toutes les dépenses (pas de filtre)

        $depensesIndex = $queryIndex->latest()->limit($limit)->get();
        
        $this->info("Requête DepenseController::index() avec period=day sans date:");
        $this->info("  - Filtre rembourse=false: ✓");
        $this->info("  - Filtre date: " . ($dateFilter ? $dateFilter : "Aucun (toutes les dépenses)"));
        $this->info("  - Résultats trouvés: {$depensesIndex->count()}");
        
        if ($depensesIndex->isNotEmpty()) {
            $this->info("  - Dernière dépense ID: {$depensesIndex->first()->id}");
            $this->info("  - Dernière dépense créée le: {$depensesIndex->first()->created_at->format('Y-m-d H:i:s')}");
        }
        
        $this->newLine();

        // 3. Tester la requête utilisée dans ModePaiementController::dashboard()
        $this->info('3. TEST DE LA REQUÊTE ModePaiementController::dashboard()');
        $this->line('─────────────────────────────────────────────────────────');
        
        $testDate = $date ? Carbon::parse($date) : Carbon::now();
        $dateConstraints = ['type' => 'day', 'value' => $testDate->format('Y-m-d')];
        
        $queryDashboard = Depense::where('rembourse', false);
        
        // Appliquer le filtre de date comme dans dashboard()
        if ($dateConstraints && $dateConstraints['type'] === 'day') {
            $queryDashboard->whereDate('created_at', $dateConstraints['value']);
        }
        
        $depensesDashboard = $queryDashboard->get();
        
        $this->info("Requête ModePaiementController::dashboard() avec date={$testDate->format('Y-m-d')}:");
        $this->info("  - Filtre rembourse=false: ✓");
        $this->info("  - Filtre date: {$testDate->format('Y-m-d')}");
        $this->info("  - Résultats trouvés: {$depensesDashboard->count()}");
        
        if ($depensesDashboard->isNotEmpty()) {
            $totalMontant = $depensesDashboard->sum('montant');
            $this->info("  - Total montant: " . number_format($totalMontant, 0, ',', ' ') . " MRU");
        }
        
        $this->newLine();

        // 4. Tester la requête utilisée dans ModePaiementController::historique()
        $this->info('4. TEST DE LA REQUÊTE ModePaiementController::historique()');
        $this->line('─────────────────────────────────────────────────────────');
        
        $queryHistorique = Depense::with(['modePaiement', 'credit']);
        $queryHistorique->where('rembourse', false);
        
        // Appliquer le filtre de date comme dans historique()
        if ($dateConstraints && $dateConstraints['type'] === 'day') {
            $queryHistorique->whereDate('created_at', $dateConstraints['value']);
        }
        
        $depensesHistorique = $queryHistorique->orderBy('created_at', 'desc')->get();
        
        $this->info("Requête ModePaiementController::historique() avec date={$testDate->format('Y-m-d')}:");
        $this->info("  - Filtre rembourse=false: ✓");
        $this->info("  - Filtre date: {$testDate->format('Y-m-d')}");
        $this->info("  - Résultats trouvés: {$depensesHistorique->count()}");
        
        if ($depensesHistorique->isNotEmpty()) {
            $this->info("  - Dernière dépense ID: {$depensesHistorique->first()->id}");
            $this->info("  - Dernière dépense nom: {$depensesHistorique->first()->nom}");
        }
        
        $this->newLine();

        // 5. Vérifier la création du ModePaiement associé
        $this->info('5. VÉRIFICATION DES MODEPAIEMENT ASSOCIÉS');
        $this->line('─────────────────────────────────────────────────────────');
        
        $depensesAvecModePaiement = Depense::whereNotNull('mode_paiement_id')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
        
        $this->info("Dépenses avec mode_paiement_id renseigné: {$depensesAvecModePaiement->count()}");
        
        $modePaiementData = [];
        foreach ($depensesAvecModePaiement as $depense) {
            // Chercher le ModePaiement créé autour de la même date avec source='depense'
            $modePaiement = ModePaiement::where('type', $depense->mode_paiement_id)
                ->where('source', 'depense')
                ->whereBetween('created_at', [
                    $depense->created_at->copy()->subMinutes(5),
                    $depense->created_at->copy()->addMinutes(5)
                ])
                ->first();
            
            $modePaiementData[] = [
                'Depense ID' => $depense->id,
                'Depense Nom' => $depense->nom,
                'Mode Paiement Type' => $depense->mode_paiement_id,
                'ModePaiement Trouvé' => $modePaiement ? 'Oui (ID: ' . $modePaiement->id . ')' : 'Non',
                'Montant Depense' => number_format($depense->montant, 0, ',', ' ') . ' MRU',
                'Montant ModePaiement' => $modePaiement ? number_format(abs($modePaiement->montant), 0, ',', ' ') . ' MRU' : 'N/A',
            ];
        }
        
        if (!empty($modePaiementData)) {
            $this->table([
                'Depense ID',
                'Depense Nom',
                'Mode Paiement Type',
                'ModePaiement Trouvé',
                'Montant Depense',
                'Montant ModePaiement'
            ], $modePaiementData);
        }
        
        $this->newLine();

        // 6. Comparer les dépenses créées par admin vs superadmin
        $this->info('6. COMPARAISON ADMIN VS SUPERADMIN');
        $this->line('─────────────────────────────────────────────────────────');
        
        $adminUsers = User::whereHas('role', function($q) {
            $q->where('name', 'admin');
        })->pluck('id');
        
        $superadminUsers = User::whereHas('role', function($q) {
            $q->where('name', 'superadmin');
        })->pluck('id');
        
        $depensesAdmin = Depense::whereIn('created_by', $adminUsers)
            ->where('rembourse', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $depensesSuperadmin = Depense::whereIn('created_by', $superadminUsers)
            ->where('rembourse', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $this->info("Dépenses créées par ADMIN:");
        $this->info("  - Nombre total: " . Depense::whereIn('created_by', $adminUsers)->count());
        $this->info("  - Dernières 5:");
        foreach ($depensesAdmin as $d) {
            $this->line("    • ID {$d->id}: {$d->nom} - {$d->montant} MRU - Créée le {$d->created_at->format('Y-m-d H:i:s')}");
        }
        
        $this->newLine();
        
        $this->info("Dépenses créées par SUPERADMIN:");
        $this->info("  - Nombre total: " . Depense::whereIn('created_by', $superadminUsers)->count());
        $this->info("  - Dernières 5:");
        foreach ($depensesSuperadmin as $d) {
            $this->line("    • ID {$d->id}: {$d->nom} - {$d->montant} MRU - Créée le {$d->created_at->format('Y-m-d H:i:s')}");
        }
        
        $this->newLine();

        // 7. Vérifications spécifiques
        $this->info('7. VÉRIFICATIONS SPÉCIFIQUES');
        $this->line('─────────────────────────────────────────────────────────');
        
        // Vérifier les dépenses avec rembourse=true
        $depensesRemboursees = Depense::where('rembourse', true)->count();
        $this->info("Dépenses avec rembourse=true: {$depensesRemboursees}");
        
        // Vérifier les dépenses sans mode_paiement_id
        $depensesSansMode = Depense::whereNull('mode_paiement_id')->count();
        $this->info("Dépenses sans mode_paiement_id: {$depensesSansMode}");
        
        // Vérifier les dépenses sans created_by
        $depensesSansCreatedBy = Depense::whereNull('created_by')->count();
        $this->info("Dépenses sans created_by: {$depensesSansCreatedBy}");
        
        // Vérifier les dépenses avec source manuelle
        $depensesManuelles = Depense::where('source', 'manuelle')->where('rembourse', false)->count();
        $this->info("Dépenses manuelles (source=manuelle, rembourse=false): {$depensesManuelles}");
        
        $this->newLine();
        $this->info('=== FIN DU DÉBOGAGE ===');
        
        return 0;
    }
}










