# 🩺 Module Médecin - Guide d'Installation et d'Utilisation

## 📋 Vue d'ensemble

Ce module ajoute un **Dashboard Médecin** complet à votre application Clinique Ibn Rochd, permettant aux médecins de :

-   ✅ Créer des **Rapports de Consultation** détaillés
-   ✅ Générer des **Ordonnances Médicales**
-   ✅ Imprimer des **PDFs professionnels**
-   ✅ Consulter l'historique de leurs consultations et ordonnances
-   ✅ Gérer leurs patients

## 🎯 Fonctionnalités Implémentées

### 1. **Dashboard Médecin** (`/medecin/dashboard`)

-   Statistiques en temps réel
-   Consultations du jour et du mois
-   Ordonnances créées
-   Nombre de patients suivis
-   Accès rapide aux actions principales

### 2. **Rapports de Consultation** (`/medecin/consultations`)

-   Formulaire complet avec tous les champs médicaux
-   Motif, Antécédents, RAS, Histoire de la maladie
-   Examen clinique, Conduite à tenir, Résumé
-   Génération automatique de PDF au format professionnel
-   Historique avec filtres (date, patient, statut)

### 3. **Ordonnances Médicales** (`/medecin/ordonnances`)

-   Sélection de médicaments depuis une base de données
-   Dosage, durée, et notes pour chaque médicament
-   Génération de PDF d'ordonnance
-   Référence unique automatique (ex: ORD2025000001)
-   Lien possible avec une consultation

### 4. **Base de Données Médicaments**

-   Catalogue pré-rempli avec les médicaments courants
-   Recherche intelligente
-   Personnalisable et extensible

## 🚀 Installation

### Étape 1 : Exécuter les Migrations

```bash
php artisan migrate
```

Cela va créer :

-   ✅ Rôle "medecin" dans la table `roles`
-   ✅ Champ `medecin_id` dans la table `users`
-   ✅ Table `consultations` (rapports médicaux)
-   ✅ Table `medicaments` (catalogue de médicaments)
-   ✅ Table `ordonnances` (prescriptions)
-   ✅ Table `ordonnance_medicaments` (lignes d'ordonnance)

### Étape 2 : Peupler la Base de Médicaments

```bash
php artisan db:seed --class=MedicamentsSeeder
```

Cela va ajouter environ 25 médicaments de base (sirops, comprimés, etc.).

### Étape 3 : Créer un Compte Médecin

#### Option A : Via l'Interface Admin/SuperAdmin

1. Allez dans **Gestion des Médecins** (`/admin/medecins` ou `/superadmin/medecins`)
2. Créez un nouveau médecin (si pas déjà existant)
3. Allez dans **Gestion des Utilisateurs** (SuperAdmin uniquement)
4. Créez un utilisateur avec :
    - **Rôle** : `medecin`
    - **Médecin associé** : Sélectionnez le médecin créé
5. Approuvez le compte si nécessaire

#### Option B : Via la Base de Données (pour les tests)

```sql
-- 1. Obtenir l'ID du rôle médecin
SELECT id FROM roles WHERE name = 'medecin';

-- 2. Obtenir l'ID d'un médecin existant
SELECT id, nom, prenom FROM medecins LIMIT 1;

-- 3. Créer l'utilisateur médecin (ou mettre à jour un existant)
INSERT INTO users (name, email, password, role_id, medecin_id, is_approved, created_at, updated_at)
VALUES (
    'Dr. Test Médecin',
    'medecin@test.com',
    '$2y$12$LQv3c1yf1/gCgU3DfLQhdu8I/NiCGOp95pzzBzWX.EGUPZxTOgVw6', -- password: "password"
    [ID_ROLE_MEDECIN],
    [ID_MEDECIN],
    1,
    NOW(),
    NOW()
);
```

### Étape 4 : Tester le Module

1. **Se connecter** : Utilisez les identifiants du médecin
2. **Dashboard** : Vous serez redirigé vers `/medecin/dashboard`
3. **Créer une consultation** : Cliquez sur "Nouvelle Consultation"
4. **Créer une ordonnance** : Cliquez sur "Nouvelle Ordonnance"
5. **Imprimer les PDFs** : Utilisez les boutons "PDF" ou "Imprimer"

## 📁 Structure des Fichiers

### Migrations

```
database/migrations/
├── 2025_11_26_000001_add_medecin_role_to_roles_table.php
├── 2025_11_26_000002_add_medecin_id_to_users_table.php
├── 2025_11_26_000003_create_consultations_table.php
├── 2025_11_26_000004_create_medicaments_table.php
├── 2025_11_26_000005_create_ordonnances_table.php
└── 2025_11_26_000006_create_ordonnance_medicaments_table.php
```

### Modèles

```
app/Models/
├── Consultation.php
├── Medicament.php
├── Ordonnance.php
└── OrdonnanceMedicament.php
```

### Contrôleurs

```
app/Http/Controllers/Medecin/
├── DashboardController.php
├── ConsultationController.php
└── OrdonnanceController.php
```

### Vues

```
resources/views/medecin/
├── dashboard.blade.php
├── consultations/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   ├── show.blade.php
│   └── pdf.blade.php
└── ordonnances/
    ├── index.blade.php
    ├── create.blade.php
    ├── show.blade.php
    └── pdf.blade.php
```

### Seeders

```
database/seeders/
└── MedicamentsSeeder.php
```

## 🔐 Système de Permissions

Le module utilise le middleware `role:medecin` pour protéger toutes les routes médecin.

```php
// Exemple de route protégée
Route::middleware(['auth', 'role:medecin', 'is.approved'])
    ->prefix('medecin')
    ->name('medecin.')
    ->group(function () {
        Route::get('/dashboard', [MedecinDashboardController::class, 'index']);
        // ... autres routes
    });
```

### Accès aux Routes

| Utilisateur | Dashboard SuperAdmin | Dashboard Admin | Dashboard Médecin |
| ----------- | -------------------- | --------------- | ----------------- |
| SuperAdmin  | ✅                   | ❌              | ❌                |
| Admin       | ❌                   | ✅              | ❌                |
| Médecin     | ❌                   | ❌              | ✅                |

## 📱 Routes Disponibles

### Dashboard Médecin

-   `GET /medecin/dashboard` - Dashboard principal

### Consultations

-   `GET /medecin/consultations` - Liste des consultations
-   `GET /medecin/consultations/create` - Formulaire nouvelle consultation
-   `POST /medecin/consultations` - Enregistrer consultation
-   `GET /medecin/consultations/{id}` - Détails consultation
-   `GET /medecin/consultations/{id}/edit` - Modifier consultation
-   `PUT /medecin/consultations/{id}` - Mettre à jour consultation
-   `GET /medecin/consultations/{id}/print` - Imprimer PDF
-   `DELETE /medecin/consultations/{id}` - Supprimer consultation

### Ordonnances

-   `GET /medecin/ordonnances` - Liste des ordonnances
-   `GET /medecin/ordonnances/create` - Formulaire nouvelle ordonnance
-   `POST /medecin/ordonnances` - Enregistrer ordonnance
-   `GET /medecin/ordonnances/{id}` - Détails ordonnance
-   `GET /medecin/ordonnances/{id}/print` - Imprimer PDF
-   `DELETE /medecin/ordonnances/{id}` - Supprimer ordonnance

### API Routes (pour les recherches AJAX)

-   `GET /medecin/consultations/search-patients` - Recherche patients
-   `GET /medecin/ordonnances/search-medicaments` - Recherche médicaments

## 🎨 Design & UX

### Couleurs Thématiques

-   **Dashboard Médecin** : Bleu/Violet (`blue-600`, `purple-600`)
-   **Consultations** : Bleu (`blue-600`)
-   **Ordonnances** : Violet (`purple-600`)
-   **PDFs** : Rose/Rouge (`#fce4ec`, `#e91e63`)

### Responsive Design

-   ✅ Mobile-first
-   ✅ Tablettes
-   ✅ Desktop
-   ✅ Dark mode supporté

## 📊 Base de Données

### Structure Consultations

```sql
consultations
├── id
├── patient_id (FK -> gestion_patients)
├── medecin_id (FK -> medecins)
├── dossier_medical_id (FK -> dossiers_medicaux)
├── date_consultation
├── heure_consultation
├── motif
├── antecedents
├── ras
├── histoire_maladie
├── examen_clinique
├── conduite_tenir
├── resume
├── statut (en_cours, terminee, annulee)
├── created_at
└── updated_at
```

### Structure Ordonnances

```sql
ordonnances
├── id
├── reference (unique, ex: ORD2025000001)
├── consultation_id (FK -> consultations, nullable)
├── patient_id (FK -> gestion_patients)
├── medecin_id (FK -> medecins)
├── date_ordonnance
├── date_expiration (nullable)
├── notes (nullable)
├── statut (active, expiree, annulee)
├── created_at
└── updated_at

ordonnance_medicaments
├── id
├── ordonnance_id (FK -> ordonnances)
├── medicament_id (FK -> medicaments, nullable)
├── medicament_nom
├── dosage
├── duree
├── note
├── ordre
├── created_at
└── updated_at
```

## 🧪 Tests & Validation

### Test Manuel Recommandé

1. **Connexion Médecin**

    ```
    Email: medecin@test.com
    Password: password
    ```

2. **Créer une Consultation**

    - Sélectionner un patient
    - Remplir les champs médicaux
    - Enregistrer
    - Vérifier le PDF généré

3. **Créer une Ordonnance**

    - Sélectionner le même patient
    - Ajouter 2-3 médicaments
    - Enregistrer
    - Vérifier le PDF généré

4. **Tester les Filtres**
    - Filtrer par date
    - Rechercher par nom de patient

## 💡 Conseils & Astuces

### Ajouter des Médicaments

```php
use App\Models\Medicament;

Medicament::create([
    'nom' => 'DOLIPRANE',
    'forme' => 'comprimé',
    'dosage' => '1000mg',
    'fabricant' => 'Sanofi',
    'actif' => true,
]);
```

### Personnaliser les PDFs

Les templates PDF se trouvent dans :

-   `resources/views/medecin/consultations/pdf.blade.php`
-   `resources/views/medecin/ordonnances/pdf.blade.php`

Vous pouvez modifier :

-   Les couleurs
-   Le logo (actuellement "CLINIQUE L'HUMANITÉ")
-   Les informations de contact
-   La mise en page

### Lier un User Existant à un Médecin

```sql
UPDATE users
SET medecin_id = [ID_MEDECIN],
    role_id = (SELECT id FROM roles WHERE name = 'medecin')
WHERE id = [ID_USER];
```

## 🐛 Dépannage

### Erreur : "Aucun profil médecin associé"

**Cause** : L'utilisateur connecté n'a pas de `medecin_id`

**Solution** :

```sql
UPDATE users SET medecin_id = [ID] WHERE email = 'medecin@example.com';
```

### Les PDFs ne s'affichent pas

**Cause** : Problème avec DomPDF

**Solution** :

```bash
composer require barryvdh/laravel-dompdf
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

### Erreur 403 sur les routes médecin

**Cause** : Le rôle médecin n'existe pas ou n'est pas assigné

**Solution** :

```bash
php artisan migrate:fresh
php artisan db:seed --class=MedicamentsSeeder
```

### Les médicaments n'apparaissent pas

**Cause** : Le seeder n'a pas été exécuté

**Solution** :

```bash
php artisan db:seed --class=MedicamentsSeeder
```

## 🚀 Améliorations Futures (Optionnelles)

-   [ ] Export Excel des consultations
-   [ ] Statistiques avancées pour les médecins
-   [ ] Notifications en temps réel
-   [ ] Intégration avec le module rendez-vous
-   [ ] Recherche avancée multi-critères
-   [ ] Historique des modifications
-   [ ] Signatures numériques pour les ordonnances
-   [ ] QR Code sur les ordonnances
-   [ ] Module de téléconsultation

## 📞 Support

Pour toute question ou problème, consultez ce guide ou contactez l'administrateur système.

---

**Développé avec ❤️ pour Clinique Ibn Rochd**

Date de création : 26 Novembre 2025
