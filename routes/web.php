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

require __DIR__ . '/auth.php';
// Page d'accueil
Route::get('/', fn() => view('home'))->name('home');

Route::get('/dashboard', function () {
    $role = Auth::user()?->role?->name;

    return match ($role) {
        'superadmin' => redirect()->route('dashboard.superadmin'),
        'admin' => redirect()->route('dashboard.admin'),
        default => redirect()->route('login'),
    };
})->middleware(['auth']);

// Authentification
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
    Route::get('/register', 'showRegister')->name('register');
    Route::post('/register', 'register');
});

Route::get('/waiting-approval', function () {
    return view('auth.waiting');
})->name('approval.waiting');

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
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('caisses', CaisseController::class);
});

// Routes communes pour SUPERADMIN avec préfixe
Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    // Patients
    Route::resource('patients', GestionPatientController::class);

    // Autres ressources pour superadmin

    Route::resource('medecins', MedecinController::class);
});

// Route pour afficher la liste des patients (accessible depuis les dashboards)
Route::middleware(['auth', 'is.approved'])->group(function () {
    Route::get('/patients', [GestionPatientController::class, 'index'])->name('patients.index');

    //Caisse
    Route::resource('caisses', CaisseController::class);
    Route::get('caisses-export-pdf', [CaisseController::class, 'exportPdf'])->name('caisses.exportPdf');
    Route::get('caisses-print', [CaisseController::class, 'print'])->name('caisses.print');
});

// Routes pour ADMIN
Route::middleware(['auth', 'role:admin', 'is.approved'])->group(function () {
    // Dashboard admin
    Route::get('/dashboard/admin', fn() => view('dashboard.admin'))->name('dashboard.admin');
});

// Routes communes pour ADMIN avec préfixe
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Patients
    Route::resource('patients', GestionPatientController::class);

    // Autres ressources pour admin


});


Route::middleware(['auth', 'role:superadmin,admin'])->group(function () {
    Route::resource('personnels', PersonnelController::class);


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

    //assurances
    Route::resource('assurances', AssuranceController::class);
    Route::get('assurances/export/pdf', [AssuranceController::class, 'exportPdf'])->name('assurances.exportPdf');
    Route::get('assurances/print', [AssuranceController::class, 'print'])->name('assurances.print');


    // depences
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
    Route::resource('recap-services', RecapitulatifServiceJournalierController::class);
    Route::get('recap-services/print', [RecapitulatifServiceJournalierController::class, 'print'])->name('recap-services.print');
    Route::get('recap-services/export-pdf', [RecapitulatifServiceJournalierController::class, 'exportPdf'])->name('recap-services.exportPdf');

    // recapitulatif des opérateurs
    Route::resource('recap-operateurs', RecapitulatifOperateurController::class);
    Route::get('recap-operateurs-export-pdf', [RecapitulatifOperateurController::class, 'exportPdf'])->name('recap-operateurs.exportPdf');
    Route::get('recap-operateurs-print', [RecapitulatifOperateurController::class, 'print'])->name('recap-operateurs.print');

    // Credits
    Route::get('credits/{credit}/payer', [CreditController::class, 'payer'])->name('credits.payer');
    Route::post('credits/{credit}/payer', [CreditController::class, 'payerStore'])->name('credits.payer.store');
    Route::get('/credits/create', [CreditController::class, 'create'])->name('credits.create');
    Route::post('/credits', [CreditController::class, 'store'])->name('credits.store');
    Route::post('/credits/{id}/statut/{statut}', [CreditController::class, 'marquerComme'])->name('credits.marquer');
    Route::get('/credits', [CreditController::class, 'index'])->name('credits.index');
});


// Route commune à superadmin et admin avec préfixe selon le rôle
Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::resource('caisses', CaisseController::class)->parameters(['caisses' => 'caisse']);
    Route::get('/caisses/exportPdf', [CaisseController::class, 'exportPdf'])->name('caisses.exportPdf');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('caisses', CaisseController::class)->parameters(['caisses' => 'caisse']);
    Route::get('/caisses/exportPdf', [CaisseController::class, 'exportPdf'])->name('caisses.exportPdf');
});
Route::post('/etatcaisse/{id}/valider', [EtatCaisseController::class, 'valider'])
    ->middleware(['auth', 'role:superadmin'])
    ->name('etatcaisse.valider');
Route::post('/etatcaisse/{id}/unvalider', [EtatCaisseController::class, 'unvalider'])->name('etatcaisse.unvalider');

Route::resource('modepaiements', ModePaiementController::class);

Route::resource('credits', CreditController::class);
Route::get('medecins/{id}/stats', [MedecinController::class, 'statistiques'])->name('medecins.stats');
Route::get('medecins/{id}/stats', [MedecinController::class, 'stats'])->name('medecins.stats');

Route::get('mode-paiements/dashboard', [App\Http\Controllers\ModePaiementController::class, 'dashboard'])
    ->name('modepaiements.dashboard')
    ->middleware('auth');

Route::get('mode-paiements/historique', [App\Http\Controllers\ModePaiementController::class, 'historique'])
    ->name('modepaiements.historique')
    ->middleware('auth');
