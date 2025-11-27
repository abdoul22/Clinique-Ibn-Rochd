<?php

use App\Http\Controllers\AssuranceController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CaisseController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\ExamenController;
use App\Http\Controllers\GestionPatientController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\MedecinController;
use App\Http\Controllers\PrescripteurController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\DepenseController;
use App\Http\Controllers\EtatCaisseController;
use App\Http\Controllers\ModePaiementController;
use App\Http\Controllers\RecapitulatifOperateurController;
use App\Http\Controllers\RecapitulatifServiceJournalierController;
use App\Http\Controllers\SuperAdmin\UserManagementController;
use App\Http\Controllers\RendezVousController;
use App\Http\Controllers\MotifController;
use App\Http\Controllers\DossierMedicalController;
use App\Http\Controllers\PharmacieController;
use App\Http\Controllers\HospitalisationController;
use App\Http\Controllers\ChambreController;
use App\Http\Controllers\LitController;
use App\Http\Controllers\SituationJournaliereController;
use App\Http\Controllers\Medecin\DashboardController as MedecinDashboardController;
use App\Http\Controllers\Medecin\ConsultationController as MedecinConsultationController;
use App\Http\Controllers\Medecin\OrdonnanceController as MedecinOrdonnanceController;

require __DIR__ . '/auth.php';

// Page d'accueil (accessible à tous)
Route::get('/', fn() => view('home'))->name('home');

// Route d'attente d'approbation (accessible aux utilisateurs connectés mais non approuvés)
Route::get('/waiting-approval', function () {
    return view('auth.waiting');
})->middleware('auth')->name('approval.waiting');

// Dashboard principal (redirection selon le rôle)
Route::get('/dashboard', function () {
    $role = Auth::user()?->role?->name;

    return match ($role) {
        'superadmin' => redirect()->route('dashboard.superadmin'),
        'admin' => redirect()->route('dashboard.admin'),
        'medecin' => redirect()->route('dashboard.medecin'),
        default => redirect()->route('login'),
    };
})->middleware(['auth', 'is.approved']);

// Authentification (accessible à tous)
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
    Route::get('/register', 'showRegister')->name('register');
    Route::post('/register', 'register');
});

// Routes pour SUPERADMIN
Route::middleware(['auth', 'role:superadmin', 'is.approved'])->group(function () {
    // Dashboard superadmin
    Route::get('/dashboard/superadmin', fn() => view('dashboard.superadmin'))->name('dashboard.superadmin');

    // Gestion spécifique aux superadmins (gestion des admins)
    Route::prefix('superadmin')->group(function () {
        Route::post('/admins/{id}/assign-role', [App\Http\Controllers\SuperAdmin\AdminController::class, 'assignRole'])->name('superadmin.admins.assignRole');
        Route::get('/admins', [SuperAdminController::class, 'index'])->name('superadmin.admins.index');
        Route::post('/admins/{id}/approve', [SuperAdminController::class, 'approve'])->name('superadmin.admins.approve');
        Route::post('/admins/{id}/reject', [SuperAdminController::class, 'reject'])->name('superadmin.admins.reject');
        Route::get('/admins/{id}', [App\Http\Controllers\SuperAdmin\AdminController::class, 'show'])->name('superadmin.admins.show');
        Route::get('/admins/{id}/edit', [App\Http\Controllers\SuperAdmin\AdminController::class, 'edit'])->name('superadmin.admins.edit');
        Route::put('/admins/{id}', [App\Http\Controllers\SuperAdmin\AdminController::class, 'update'])->name('superadmin.admins.update');
        Route::delete('/admins/{id}', [App\Http\Controllers\SuperAdmin\AdminController::class, 'destroy'])->name('superadmin.admins.destroy');
    });
});

// Routes communes pour SUPERADMIN avec préfixe
Route::middleware(['auth', 'role:superadmin', 'is.approved'])->prefix('superadmin')->name('superadmin.')->group(function () {
    // Patients
    Route::resource('patients', GestionPatientController::class);
    // Médecins
    Route::resource('medecins', MedecinController::class);
    // Examens
    Route::get('/examens/print', [ExamenController::class, 'print'])->name('examens.print');
    Route::get('/examens/export-pdf', [ExamenController::class, 'exportPdf'])->name('examens.exportPdf');
    Route::resource('examens', ExamenController::class);
    // Services
    Route::resource('services', ServiceController::class);
    Route::get('/services/export-pdf', [ServiceController::class, 'exportPdf'])->name('services.exportPdf');
    Route::get('/services/print', [ServiceController::class, 'print'])->name('services.print');
    // Prescripteurs
    Route::resource('prescripteurs', PrescripteurController::class);
    Route::get('/prescripteurs/print', [PrescripteurController::class, 'print'])->name('prescripteurs.print');
    Route::get('prescripteurs/export-pdf', [PrescripteurController::class, 'exportPdf'])->name('prescripteurs.exportPdf');
    // Assurances
    Route::resource('assurances', AssuranceController::class);
    Route::get('assurances/export/pdf', [AssuranceController::class, 'exportPdf'])->name('assurances.exportPdf');
    Route::get('assurances/print', [AssuranceController::class, 'print'])->name('assurances.print');
    // Caisse
    Route::resource('caisses', CaisseController::class)->parameters(['caisses' => 'caisse']);
    Route::get('/caisses/{caisse}/exportPdf', [CaisseController::class, 'exportPdf'])->name('caisses.exportPdf');
    Route::get('/caisses/{id}/print', [CaisseController::class, 'printSingle'])->name('caisses.printSingle');
    // API pour numéro d'entrée
    Route::get('/api/caisses/numero-entree/{medecin_id}', [CaisseController::class, 'getNextNumeroEntree'])->name('caisses.getNextNumeroEntree');

    // Récapitulatifs pour superadmin
    Route::get('recap-services/print', [RecapitulatifServiceJournalierController::class, 'print'])->name('recap-services.print');
    Route::get('recap-services/export-pdf', [RecapitulatifServiceJournalierController::class, 'exportPdf'])->name('recap-services.exportPdf');
    Route::resource('recap-services', RecapitulatifServiceJournalierController::class);

    Route::resource('recap-operateurs', RecapitulatifOperateurController::class);
    Route::get('recap-operateurs-export-pdf', [RecapitulatifOperateurController::class, 'exportPdf'])->name('recap-operateurs.exportPdf');
    Route::get('recap-operateurs-print', [RecapitulatifOperateurController::class, 'print'])->name('recap-operateurs.print');

    // Récapitulatif opérateurs - Détails médecins hospitalisations
    Route::get('hospitalisations/doctors/by-date/{date}', [HospitalisationController::class, 'showDoctorsByDate'])->name('hospitalisations.doctors.by-date');

    // Hospitalisations pour superadmin
    Route::get('hospitalisations/search-patients-by-phone', [HospitalisationController::class, 'searchPatientsByPhone'])->name('hospitalisations.search-patients-by-phone');
    Route::get('/hospitalisations/lits-disponibles', [HospitalisationController::class, 'getLitsDisponibles'])->name('hospitalisations.lits.disponibles');
    Route::get('hospitalisations/{id}/doctors', [HospitalisationController::class, 'showDoctors'])->name('hospitalisations.doctors');
    Route::get('hospitalisations/{id}/print', [HospitalisationController::class, 'print'])->name('hospitalisations.print');

    // Route de ressources
    Route::resource('hospitalisations', HospitalisationController::class);

    // Routes d'actions
    Route::post('hospitalisations/{id}/facturer', [HospitalisationController::class, 'facturer'])->name('hospitalisations.facturer');
    Route::patch('hospitalisations/{id}/status', [HospitalisationController::class, 'updateStatus'])->name('hospitalisations.updateStatus');
    Route::post('hospitalisations/{id}/payer-tout', [HospitalisationController::class, 'payerTout'])->name('hospitalisations.payerTout');
    Route::post('hospitalisations/{id}/charges', [HospitalisationController::class, 'addCharge'])->name('hospitalisations.addCharge');
    Route::delete('hospitalisations/{id}/charges/{chargeId}', [HospitalisationController::class, 'removeCharge'])->name('hospitalisations.removeCharge');

    // Rendez-vous pour superadmin
    Route::get('rendezvous/print', [RendezVousController::class, 'print'])->name('rendezvous.print');
    Route::resource('rendezvous', RendezVousController::class)->parameters(['rendezvous' => 'id']);
    Route::post('rendezvous/{id}/change-status', [RendezVousController::class, 'changeStatus'])->name('rendezvous.change-status');
    Route::get('rendezvous/get-by-date', [RendezVousController::class, 'getRendezVousByDate'])->name('rendezvous.get-by-date');

    // Situation Journalière pour superadmin
    Route::get('situation-journaliere', [SituationJournaliereController::class, 'index'])->name('situation-journaliere.index');
    Route::get('situation-journaliere/print', [SituationJournaliereController::class, 'print'])->name('situation-journaliere.print');
    Route::get('situation-journaliere/export-pdf', [SituationJournaliereController::class, 'exportPdf'])->name('situation-journaliere.exportPdf');
});

// Routes pour ADMIN
Route::middleware(['auth', 'role:admin', 'is.approved'])->group(function () {
    // Dashboard admin
    Route::get('/dashboard/admin', fn() => view('dashboard.admin'))->name('dashboard.admin');
});

// Routes communes pour ADMIN avec préfixe
Route::middleware(['auth', 'role:admin', 'is.approved'])->prefix('admin')->name('admin.')->group(function () {
    // Patients
    Route::resource('patients', GestionPatientController::class);
    // Médecins
    Route::resource('medecins', MedecinController::class);
    // Examens
    Route::get('/examens/print', [ExamenController::class, 'print'])->name('examens.print');
    Route::get('/examens/export-pdf', [ExamenController::class, 'exportPdf'])->name('examens.exportPdf');
    Route::resource('examens', ExamenController::class);
    // Services
    Route::resource('services', ServiceController::class);
    Route::get('/services/export-pdf', [ServiceController::class, 'exportPdf'])->name('services.exportPdf');
    Route::get('/services/print', [ServiceController::class, 'print'])->name('services.print');
    // Prescripteurs
    Route::resource('prescripteurs', PrescripteurController::class);
    Route::get('/prescripteurs/print', [PrescripteurController::class, 'print'])->name('prescripteurs.print');
    Route::get('prescripteurs/export-pdf', [PrescripteurController::class, 'exportPdf'])->name('prescripteurs.exportPdf');
    // Assurances
    Route::resource('assurances', AssuranceController::class);
    Route::get('assurances/export/pdf', [AssuranceController::class, 'exportPdf'])->name('assurances.exportPdf');
    Route::get('assurances/print', [AssuranceController::class, 'print'])->name('assurances.print');
    // Rendez-vous pour admin
    Route::get('rendezvous/print', [RendezVousController::class, 'print'])->name('rendezvous.print');
    Route::resource('rendezvous', RendezVousController::class)->parameters(['rendezvous' => 'id']);
    Route::post('rendezvous/{id}/change-status', [RendezVousController::class, 'changeStatus'])->name('rendezvous.change-status');
    Route::get('rendezvous/get-by-date', [RendezVousController::class, 'getRendezVousByDate'])->name('rendezvous.get-by-date');
    // Autres ressources pour admin
    Route::resource('caisses', CaisseController::class);
    Route::get('dossiers/synchroniser', [DossierMedicalController::class, 'synchroniser'])->name('dossiers.synchroniser');
    Route::resource('dossiers', DossierMedicalController::class)->parameters(['dossiers' => 'dossier']);
    // Caisse
    Route::get('/caisses/{caisse}/exportPdf', [CaisseController::class, 'exportPdf'])->name('caisses.exportPdf');
    Route::get('/caisses/{id}/print', [CaisseController::class, 'printSingle'])->name('caisses.printSingle');

    // Hospitalisations pour admin
    // Routes spécifiques AVANT les routes de ressources
    Route::get('hospitalisations/search-patients-by-phone', [HospitalisationController::class, 'searchPatientsByPhone'])->name('hospitalisations.search-patients-by-phone');
    Route::get('/hospitalisations/lits-disponibles', [HospitalisationController::class, 'getLitsDisponibles'])->name('hospitalisations.lits.disponibles');
    Route::get('hospitalisations/doctors/by-date/{date}', [HospitalisationController::class, 'showDoctorsByDate'])->name('hospitalisations.doctors.by-date');
    Route::get('hospitalisations/{id}/doctors', [HospitalisationController::class, 'showDoctors'])->name('hospitalisations.doctors');
    Route::get('hospitalisations/{id}/print', [HospitalisationController::class, 'print'])->name('hospitalisations.print');

    // Route de ressources
    Route::resource('hospitalisations', HospitalisationController::class);

    // Routes d'actions
    Route::post('hospitalisations/{id}/facturer', [HospitalisationController::class, 'facturer'])->name('hospitalisations.facturer');
    Route::patch('hospitalisations/{id}/status', [HospitalisationController::class, 'updateStatus'])->name('hospitalisations.updateStatus');
    Route::post('hospitalisations/{id}/payer-tout', [HospitalisationController::class, 'payerTout'])->name('hospitalisations.payerTout');
    Route::post('hospitalisations/{id}/charges', [HospitalisationController::class, 'addCharge'])->name('hospitalisations.addCharge');
    Route::delete('hospitalisations/{id}/charges/{chargeId}', [HospitalisationController::class, 'removeCharge'])->name('hospitalisations.removeCharge');

    // Récapitulatifs pour admin
    Route::get('recap-services/print', [RecapitulatifServiceJournalierController::class, 'print'])->name('recap-services.print');
    Route::get('recap-services/export-pdf', [RecapitulatifServiceJournalierController::class, 'exportPdf'])->name('recap-services.exportPdf');
    Route::resource('recap-services', RecapitulatifServiceJournalierController::class);

    Route::resource('recap-operateurs', RecapitulatifOperateurController::class);
    Route::get('recap-operateurs-export-pdf', [RecapitulatifOperateurController::class, 'exportPdf'])->name('recap-operateurs.exportPdf');
    Route::get('recap-operateurs-print', [RecapitulatifOperateurController::class, 'print'])->name('recap-operateurs.print');

    // Situation Journalière pour admin
    Route::get('situation-journaliere', [SituationJournaliereController::class, 'index'])->name('situation-journaliere.index');
    Route::get('situation-journaliere/print', [SituationJournaliereController::class, 'print'])->name('situation-journaliere.print');
    Route::get('situation-journaliere/export-pdf', [SituationJournaliereController::class, 'exportPdf'])->name('situation-journaliere.exportPdf');

    // Dépenses - Seulement création pour admin
    Route::get('depenses/create', [DepenseController::class, 'create'])->name('depenses.create');
    Route::post('depenses', [DepenseController::class, 'store'])->name('depenses.store');
});

// Routes pour MEDECIN
Route::middleware(['auth', 'role:medecin', 'is.approved'])->prefix('medecin')->name('medecin.')->group(function () {
    // Dashboard médecin
    Route::get('/dashboard', [MedecinDashboardController::class, 'index'])->name('dashboard');

    // Consultations
    Route::get('/consultations/search-patients', [MedecinConsultationController::class, 'searchPatients'])->name('consultations.search-patients');
    Route::get('/consultations/{id}/print', [MedecinConsultationController::class, 'printPdf'])->name('consultations.print');
    Route::resource('consultations', MedecinConsultationController::class);

    // Ordonnances
    Route::get('/ordonnances/search-medicaments', [MedecinOrdonnanceController::class, 'searchMedicaments'])->name('ordonnances.search-medicaments');
    Route::get('/ordonnances/{id}/print', [MedecinOrdonnanceController::class, 'printPdf'])->name('ordonnances.print');
    Route::resource('ordonnances', MedecinOrdonnanceController::class);

    // Liste des patients du médecin (seulement ceux qu'il a consultés)
    Route::get('/patients', [MedecinDashboardController::class, 'mesPatients'])->name('patients.index');
    Route::get('/patients/{id}', [GestionPatientController::class, 'show'])->name('patients.show');
});

// Route pour afficher la liste des patients (accessible depuis les dashboards)
Route::middleware(['auth', 'is.approved'])->group(function () {
    Route::get('/patients', [GestionPatientController::class, 'index'])->name('patients.index');
    //Caisse
    Route::resource('caisses', CaisseController::class);
    Route::get('caisses/{caisse}/export-pdf', [CaisseController::class, 'exportPdf'])->name('caisses.exportPdf');
    Route::get('caisses-print', [CaisseController::class, 'print'])->name('caisses.print');
    Route::get('caisses/{id}/print', [CaisseController::class, 'printSingle'])->name('caisses.printSingle');
});

// Routes communes pour ADMIN et SUPERADMIN (protégées par auth et is.approved)
Route::middleware(['auth', 'role:superadmin,admin', 'is.approved'])->group(function () {
    Route::resource('personnels', PersonnelController::class);

    // Dossiers médicaux (routes communes pour admin et superadmin)
    Route::get('dossiers/synchroniser', [DossierMedicalController::class, 'synchroniser'])->name('dossiers.synchroniser');
    Route::resource('dossiers', DossierMedicalController::class)->parameters(['dossiers' => 'dossier']);

    // Services
    // Routes personnalisées pour export PDF et impression
    Route::resource('services', ServiceController::class);
    Route::get('/services/export-pdf', [ServiceController::class, 'exportPdf'])->name('services.exportPdf');
    Route::get('/services/print', [ServiceController::class, 'print'])->name('services.print');

    //prescripteurs
    Route::resource('prescripteurs', PrescripteurController::class);
    Route::get('/prescripteurs/print', [PrescripteurController::class, 'print'])->name('prescripteurs.print');
    Route::get('prescripteurs/export-pdf', [PrescripteurController::class, 'exportPdf'])->name('prescripteurs.exportPdf');

    //Examens
    Route::resource('examens', ExamenController::class);
    Route::get('/examens/print', [ExamenController::class, 'print'])->name('examens.print');
    Route::get('/examens/export-pdf', [ExamenController::class, 'exportPdf'])->name('examens.exportPdf');

    // Route API pour les informations de stock des examens
    Route::get('/api/examens/{id}/stock-info', function ($id) {
        $examen = \App\Models\Examen::with('service.pharmacie')->find($id);

        if (!$examen) {
            return response()->json(['error' => 'Examen non trouvé'], 404);
        }

        $service = $examen->service;
        $isMedicament = $service && $service->type_service === 'pharmacie' && $service->pharmacie;

        if ($isMedicament) {
            $medicament = $service->pharmacie;
            return response()->json([
                'is_medicament' => true,
                'stock_info' => [
                    'nom_medicament' => $medicament->nom_medicament,
                    'stock' => $medicament->stock,
                    'prix_vente' => number_format($medicament->prix_vente, 0, ',', ' '),
                    'statut' => $medicament->statut
                ]
            ]);
        }

        return response()->json(['is_medicament' => false]);
    });

    //assurances
    Route::resource('assurances', AssuranceController::class);
    Route::get('assurances/export/pdf', [AssuranceController::class, 'exportPdf'])->name('assurances.exportPdf');
    Route::get('assurances/print', [AssuranceController::class, 'print'])->name('assurances.print');

    // Dépenses - Routes complètes pour superadmin uniquement
    Route::resource('depenses', DepenseController::class);
    Route::get('depenses-export-pdf', [DepenseController::class, 'exportPdf'])->name('depenses.exportPdf');
    Route::get('depenses-print', [DepenseController::class, 'print'])->name('depenses.print');

    // états de caisse
    Route::resource('etatcaisse', EtatCaisseController::class);
    Route::get('etatcaisse-export-pdf', [EtatCaisseController::class, 'exportPdf'])->name('etatcaisse.exportPdf');
    Route::get('etatcaisse-print', [EtatCaisseController::class, 'print'])->name('etatcaisse.print');

    // Générer l'état général (ancien et nouveau)
    Route::post('/etatcaisse/generer/general', [EtatCaisseController::class, 'generateGeneral'])->name('etatcaisse.generer.general');
    Route::post('/etatcaisse/generer/etat-general', [EtatCaisseController::class, 'genererEtatGeneral'])->name('etatcaisse.generer.etat_general');
    // Générer pour un personnel (un seul)
    Route::post('/etatcaisse/generer/personnel/{id}', [EtatCaisseController::class, 'generateForPersonnel'])->name('etatcaisse.generer.personnel');
    // Générer pour tous les personnels avec crédit
    Route::post('/etatcaisse/generer/personnels', [EtatCaisseController::class, 'generateAllPersonnelCredits'])->name('etatcaisse.generer.personnels');
    // Générer pour une assurance (si tu ajoutes la méthode)
    Route::post('/etatcaisse/generer/assurance/{id}', [EtatCaisseController::class, 'generateForAssurance'])->name('etatcaisse.generer.assurance');
    // Générer pour toutes les assurances
    Route::post('/etatcaisse/generer/assurances', [EtatCaisseController::class, 'generateAllAssuranceEtats'])->name('etatcaisse.generer.assurances');
    // Générer un état journalier (avec filtres date d'aujourd'hui)
    Route::post('/etatcaisse/generer/journalier', [EtatCaisseController::class, 'generateDailyEtat'])->name('etatcaisse.generer.journalier');

    //Recap services
    Route::get('recap-services/print', [RecapitulatifServiceJournalierController::class, 'print'])->name('recap-services.print');
    Route::get('recap-services/export-pdf', [RecapitulatifServiceJournalierController::class, 'exportPdf'])->name('recap-services.exportPdf');
    Route::resource('recap-services', RecapitulatifServiceJournalierController::class);

    // recapitulatif des opérateurs
    Route::resource('recap-operateurs', RecapitulatifOperateurController::class);
    Route::get('recap-operateurs-export-pdf', [RecapitulatifOperateurController::class, 'exportPdf'])->name('recap-operateurs.exportPdf');
    Route::get('recap-operateurs-print', [RecapitulatifOperateurController::class, 'print'])->name('recap-operateurs.print');

    // Credits
    Route::get('credits/{credit}/payer', [CreditController::class, 'payer'])->name('credits.payer');
    Route::post('credits/{credit}/payer', [CreditController::class, 'payerStore'])->name('credits.payer.store');
    Route::post('credits/{credit}/payer-salaire', [CreditController::class, 'payerSalaire'])->name('credits.payer.salaire');
    Route::get('/credits/create', [CreditController::class, 'create'])->name('credits.create');
    Route::post('/credits', [CreditController::class, 'store'])->name('credits.store');
    Route::post('/credits/{id}/statut/{statut}', [CreditController::class, 'marquerComme'])->name('credits.marquer');
    Route::get('/credits', [CreditController::class, 'index'])->name('credits.index');

    // Pharmacie
    Route::resource('pharmacie', PharmacieController::class);
    Route::get('/pharmacie-api/medicaments', [PharmacieController::class, 'getMedicaments'])->name('pharmacie.api.medicaments');
    Route::get('/pharmacie-api/medicament/{id}', [PharmacieController::class, 'getMedicament'])->name('pharmacie.api.medicament');
    Route::post('/pharmacie-api/medicament/{id}/deduire-stock', [PharmacieController::class, 'deduireStock'])->name('pharmacie.api.deduire-stock');

    // Hospitalisations (protégées par auth et is.approved)
    // Routes spécifiques AVANT les routes de ressources
    Route::get('hospitalisations/search-patients-by-phone', [HospitalisationController::class, 'searchPatientsByPhone'])->name('hospitalisations.search-patients-by-phone');
    Route::get('/hospitalisations/lits-disponibles', [HospitalisationController::class, 'getLitsDisponibles'])->name('hospitalisations.lits.disponibles');
    Route::get('hospitalisations/doctors/by-date/{date}', [HospitalisationController::class, 'showDoctorsByDate'])->name('hospitalisations.doctors.by-date');
    Route::get('hospitalisations/{id}/doctors', [HospitalisationController::class, 'showDoctors'])->name('hospitalisations.doctors');
    Route::get('hospitalisations/{id}/print', [HospitalisationController::class, 'print'])->name('hospitalisations.print');

    // Route de ressources
    Route::resource('hospitalisations', HospitalisationController::class);

    // Routes d'actions
    Route::post('hospitalisations/{id}/facturer', [HospitalisationController::class, 'facturer'])->name('hospitalisations.facturer');
    Route::patch('hospitalisations/{id}/status', [HospitalisationController::class, 'updateStatus'])->name('hospitalisations.updateStatus');
    Route::post('hospitalisations/{id}/payer-tout', [HospitalisationController::class, 'payerTout'])->name('hospitalisations.payerTout');
    Route::post('hospitalisations/{id}/charges', [HospitalisationController::class, 'addCharge'])->name('hospitalisations.addCharge');
    Route::delete('hospitalisations/{id}/charges/{chargeId}', [HospitalisationController::class, 'removeCharge'])->name('hospitalisations.removeCharge');

    // Chambres (protégées par auth et is.approved)
    Route::resource('chambres', ChambreController::class);
    Route::get('/chambres-api/disponibles', [ChambreController::class, 'getChambresDisponibles'])->name('chambres.api.disponibles');
    Route::get('/chambres/{chambre}/lits-disponibles', [ChambreController::class, 'getLitsDisponibles'])->name('chambres.lits.disponibles');

    // Lits (protégées par auth et is.approved)
    Route::resource('lits', LitController::class);
    Route::get('/lits-api/disponibles', [LitController::class, 'getLitsDisponibles'])->name('lits.api.disponibles');

    // Rendez-vous (accessible aux admins et superadmins)
    Route::get('rendezvous/print', [RendezVousController::class, 'print'])->name('rendezvous.print');
    Route::resource('rendezvous', RendezVousController::class)->parameters(['rendezvous' => 'id']);
    Route::post('rendezvous/{id}/change-status', [RendezVousController::class, 'changeStatus'])->name('rendezvous.change-status');
    Route::get('rendezvous/get-by-date', [RendezVousController::class, 'getRendezVousByDate'])->name('rendezvous.get-by-date');

    // Routes pour les motifs de consultation (accessible aux admins et superadmins)
    Route::resource('motifs', MotifController::class);
    Route::post('motifs/{id}/toggle-status', [MotifController::class, 'toggleStatus'])->name('motifs.toggle-status');
    Route::get('motifs/get-actifs', [MotifController::class, 'getMotifsActifs'])->name('motifs.get-actifs');
});

// Routes profile utilisateur
Route::middleware(['auth', 'is.approved'])->group(function () {
    Route::get('/profile', function () {
        return view('profile.show');
    })->name('profile.show');

    Route::get('/settings', function () {
        return view('profile.settings');
    })->name('profile.settings');

    Route::get('/help', function () {
        return view('profile.help');
    })->name('profile.help');
});

// Routes spécifiques (protégées par auth et is.approved)
Route::middleware(['auth', 'is.approved'])->group(function () {
    Route::post('/etatcaisse/{id}/valider', [EtatCaisseController::class, 'valider'])
        ->middleware('role:superadmin')
        ->name('etatcaisse.valider');
    Route::post('/etatcaisse/{id}/unvalider', [EtatCaisseController::class, 'unvalider'])->name('etatcaisse.unvalider');

    Route::resource('modepaiements', ModePaiementController::class);
    Route::resource('credits', CreditController::class);

    Route::get('medecins/{id}/stats', [MedecinController::class, 'statistiques'])->name('medecins.stats');
    Route::get('medecins/{id}/stats', [MedecinController::class, 'stats'])->name('medecins.stats');

    Route::get('mode-paiements/dashboard', [App\Http\Controllers\ModePaiementController::class, 'dashboard'])
        ->name('modepaiements.dashboard');

    Route::get('mode-paiements/historique', [App\Http\Controllers\ModePaiementController::class, 'historique'])
        ->name('modepaiements.historique');

    // Salaires (liste, PDF, paiement global/individuel)
    Route::get('salaires', [App\Http\Controllers\PayrollController::class, 'index'])->name('salaires.index');
    Route::get('salaires/pdf', [App\Http\Controllers\PayrollController::class, 'pdf'])->name('salaires.pdf');
    Route::post('salaires/payer-tout', [App\Http\Controllers\PayrollController::class, 'payAll'])->name('salaires.payAll');
    Route::post('salaires/{personnelId}/payer', [App\Http\Controllers\PayrollController::class, 'payOne'])->name('salaires.payOne');

    // API pour récupérer le prochain numéro d'entrée
    Route::get('api/next-numero-entree', [App\Http\Controllers\CaisseController::class, 'getNextNumeroEntreeApi']);
    Route::get('api/next-numero-entree-rdv', [App\Http\Controllers\RendezVousController::class, 'getNextNumeroEntreeApi']);
});

// API pour récupérer le prochain numéro d'entrée d'un médecin
Route::get('/api/caisses/numero-entree/{medecin_id}', [CaisseController::class, 'getNextNumeroEntree'])->name('caisses.getNextNumeroEntree');
