# 📋 PRD - Système de Gestion Clinique IBN ROCHD

**Version:** 1.0  
**Date:** Janvier 2025  
**Auteur:** Équipe de Développement  
**Statut:** Production

---

## 📌 Table des Matières

1. [Vue d'ensemble](#vue-densemble)
2. [Contexte et Objectifs](#contexte-et-objectifs)
3. [Stakeholders](#stakeholders)
4. [Fonctionnalités Principales](#fonctionnalités-principales)
5. [Architecture Technique](#architecture-technique)
6. [Rôles et Permissions](#rôles-et-permissions)
7. [Modules Détaillés](#modules-détaillés)
8. [Exigences Non-Fonctionnelles](#exigences-non-fonctionnelles)
9. [Sécurité](#sécurité)
10. [Interface Utilisateur](#interface-utilisateur)
11. [Intégrations](#intégrations)
12. [Performance et Scalabilité](#performance-et-scalabilité)
13. [Plan de Déploiement](#plan-de-déploiement)
14. [Critères d'Acceptation](#critères-dacceptation)

---

## 🎯 Vue d'ensemble

### Description du Projet

Le **Système de Gestion Clinique IBN ROCHD** est une application web complète de gestion médicale développée pour le **Centre IBN ROCHD**, un centre d'imagerie médicale situé à Nouakchott, Mauritanie. Le système permet la gestion intégrée de tous les aspects administratifs, médicaux et financiers d'une clinique moderne.

### Informations de la Clinique

- **Nom:** CENTRE IBN ROCHD (مركز ابن رشد)
- **Adresse:** Avenue John Kennedy, en face de la Polyclinique – Nouakchott
- **Téléphone:** Urgences Tél. 43 45 54 23 – 22 30 56 26
- **Site Web:** ibnrochd.pro
- **Directeur:** Dr Brahim Ould Ntaghry (الدكتور إبراهيم ولد نْتَغري)
- **Spécialité:** Spécialiste en Imagerie Médicale (اختصاصي في التشخيص الطبي والأشعة)
- **Type de Centre:** Centre Imagerie Médicale (مركز التشخيص الطبي)
- **Services:** Scanner – Echographie – Radiologie Générale – Mammographie – Panoramique Dentaire

### Portée du Projet

Le système couvre l'ensemble du cycle de vie d'une consultation médicale, depuis la prise de rendez-vous jusqu'au paiement et à la génération de rapports financiers, en passant par la gestion des patients, des examens, des hospitalisations et de la pharmacie.

---

## 🎯 Contexte et Objectifs

### Problématiques Résolues

1. **Gestion Manuelle Complexe:** Remplacement des systèmes papier par une solution numérique centralisée
2. **Traçabilité:** Suivi complet des dossiers médicaux et des transactions financières
3. **Efficacité Opérationnelle:** Automatisation des processus administratifs et médicaux
4. **Conformité:** Respect des normes de gestion médicale et comptable
5. **Rapportage:** Génération automatique de rapports et états financiers

### Objectifs Business

- ✅ Réduire le temps de traitement administratif de 60%
- ✅ Améliorer la traçabilité des dossiers médicaux à 100%
- ✅ Automatiser la génération de rapports financiers quotidiens
- ✅ Centraliser la gestion des ressources (personnel, chambres, médicaments)
- ✅ Faciliter la prise de décision grâce aux tableaux de bord en temps réel

### Objectifs Techniques

- ✅ Application web responsive et moderne
- ✅ Support multilingue (Français/Arabe)
- ✅ Génération de documents PDF professionnels
- ✅ Système de permissions granulaire
- ✅ Performance optimale pour 100+ utilisateurs simultanés

---

## 👥 Stakeholders

### Utilisateurs Principaux

1. **Super Administrateur**
   - Gestion complète du système
   - Gestion des administrateurs
   - Accès à tous les modules

2. **Administrateur**
   - Gestion opérationnelle quotidienne
   - Gestion des patients, médecins, services
   - Gestion financière et rapports

3. **Médecin**
   - Consultations médicales
   - Prescriptions (ordonnances)
   - Accès aux dossiers de ses patients

4. **Personnel Administratif**
   - Saisie des données
   - Gestion de la caisse
   - Prise de rendez-vous

### Parties Prenantes Externes

- **Patients:** Bénéficiaires finaux des services
- **Assurances:** Partenaires pour la prise en charge
- **Prescripteurs:** Médecins externes prescrivant des examens

---

## 🚀 Fonctionnalités Principales

### 1. Gestion des Patients

**Description:** Module complet de gestion des dossiers patients avec historique médical.

**Fonctionnalités:**
- ✅ Enregistrement des informations personnelles (nom, prénom, date de naissance, téléphone, adresse)
- ✅ Recherche avancée par nom, téléphone, ou numéro de dossier
- ✅ Historique complet des consultations et examens
- ✅ Gestion des dossiers médicaux
- ✅ Export PDF des dossiers
- ✅ Synchronisation automatique des dossiers

**Routes:**
- `/patients` - Liste des patients
- `/superadmin/patients` - Gestion complète (SuperAdmin)
- `/admin/patients` - Gestion complète (Admin)
- `/medecin/patients` - Liste des patients du médecin

**Critères d'Acceptation:**
- Un patient peut être créé avec toutes les informations requises
- La recherche retourne des résultats en moins de 2 secondes
- L'historique médical est accessible en un clic

---

### 2. Gestion des Médecins

**Description:** Administration des médecins et de leurs spécialités.

**Fonctionnalités:**
- ✅ Enregistrement des médecins avec spécialité
- ✅ Attribution de comptes utilisateurs
- ✅ Statistiques par médecin (consultations, revenus)
- ✅ Gestion des horaires et disponibilités

**Routes:**
- `/superadmin/medecins` - CRUD complet
- `/admin/medecins` - CRUD complet
- `/medecins/{id}/stats` - Statistiques détaillées

**Critères d'Acceptation:**
- Chaque médecin peut avoir un compte utilisateur associé
- Les statistiques sont calculées en temps réel

---

### 3. Consultations Médicales

**Description:** Module dédié aux médecins pour créer des rapports de consultation détaillés.

**Fonctionnalités:**
- ✅ Formulaire de consultation complet:
  - Motif de consultation
  - Antécédents médicaux
  - RAS (Rien à Signaler)
  - Histoire de la maladie
  - Examen clinique
  - Conduite à tenir
  - Résumé et diagnostic
- ✅ Recherche de patients
- ✅ Génération de PDF professionnel
- ✅ Historique des consultations par médecin
- ✅ Filtres par date, patient, statut

**Routes:**
- `/medecin/consultations` - Liste des consultations
- `/medecin/consultations/create` - Nouvelle consultation
- `/medecin/consultations/{id}/print` - Impression PDF

**Critères d'Acceptation:**
- Un médecin peut créer une consultation complète en moins de 5 minutes
- Le PDF généré est au format professionnel avec en-tête de la clinique
- Les consultations sont liées au médecin connecté

---

### 4. Ordonnances Médicales

**Description:** Système de prescription médicale avec base de données de médicaments.

**Fonctionnalités:**
- ✅ Catalogue de médicaments intégré
- ✅ Recherche intelligente de médicaments
- ✅ Ajout de médicaments à l'ordonnance avec:
  - Dosage
  - Durée du traitement
  - Notes spéciales
- ✅ Génération de PDF d'ordonnance
- ✅ Numéro de référence unique automatique (ex: ORD2025000001)
- ✅ Lien optionnel avec une consultation

**Routes:**
- `/medecin/ordonnances` - Liste des ordonnances
- `/medecin/ordonnances/create` - Nouvelle ordonnance
- `/medecin/ordonnances/{id}/print` - Impression PDF
- `/medecin/ordonnances/search-medicaments` - API de recherche

**Critères d'Acceptation:**
- Un médecin peut créer une ordonnance avec 5 médicaments en moins de 3 minutes
- Le PDF est conforme aux standards médicaux
- La base de médicaments est extensible

---

### 5. Examens Médicaux

**Description:** Gestion des examens médicaux (Scanner, Échographie, Radiologie, etc.).

**Fonctionnalités:**
- ✅ Enregistrement des examens par service
- ✅ Association patient-médecin-prescripteur
- ✅ Gestion des prix par type d'examen
- ✅ Suivi du statut (en attente, réalisé, payé)
- ✅ Export PDF et impression
- ✅ Intégration avec la caisse
- ✅ Vérification du stock pour les médicaments

**Routes:**
- `/superadmin/examens` - CRUD complet
- `/admin/examens` - CRUD complet
- `/examens/print` - Impression liste
- `/examens/export-pdf` - Export PDF
- `/api/examens/{id}/stock-info` - API stock médicament

**Critères d'Acceptation:**
- Un examen peut être enregistré et facturé en une seule opération
- Les examens sont automatiquement liés à la caisse
- Le système vérifie le stock avant la vente de médicaments

---

### 6. Hospitalisations

**Description:** Gestion complète des hospitalisations avec chambres et lits.

**Fonctionnalités:**
- ✅ Gestion des chambres (numéro, type, capacité)
- ✅ Gestion des lits (disponible, occupé, maintenance)
- ✅ Enregistrement des hospitalisations:
  - Patient
  - Chambre et lit assignés
  - Médecin responsable
  - Date d'entrée/sortie
  - Motif d'hospitalisation
- ✅ Ajout de charges supplémentaires (médicaments, examens)
- ✅ Facturation automatique
- ✅ Paiement partiel ou total
- ✅ Suivi des médecins par date
- ✅ Génération de PDF d'hospitalisation

**Routes:**
- `/superadmin/hospitalisations` - CRUD complet
- `/admin/hospitalisations` - CRUD complet
- `/hospitalisations/{id}/facturer` - Facturation
- `/hospitalisations/{id}/payer-tout` - Paiement total
- `/hospitalisations/{id}/charges` - Ajout de charges
- `/hospitalisations/{id}/print` - Impression PDF
- `/hospitalisations/lits-disponibles` - API lits disponibles
- `/hospitalisations/search-patients-by-phone` - Recherche patient

**Critères d'Acceptation:**
- Le système affiche uniquement les lits disponibles lors de la création
- La facturation calcule automatiquement les jours d'hospitalisation
- Les charges peuvent être ajoutées à tout moment

---

### 7. Gestion de la Caisse

**Description:** Système de caisse pour enregistrer toutes les transactions financières.

**Fonctionnalités:**
- ✅ Enregistrement des entrées de caisse:
  - Numéro d'entrée unique par médecin
  - Patient
  - Service/Examen
  - Montant
  - Mode de paiement
  - Assurance (optionnel)
- ✅ Génération de reçus PDF
- ✅ Recherche et filtres avancés
- ✅ Export PDF et impression
- ✅ Suivi des paiements par assurance
- ✅ Calcul automatique des parts médecins

**Routes:**
- `/superadmin/caisses` - CRUD complet
- `/admin/caisses` - CRUD complet
- `/caisses/{id}/print` - Impression reçu
- `/caisses/{caisse}/exportPdf` - Export PDF
- `/api/caisses/numero-entree/{medecin_id}` - API numéro suivant

**Critères d'Acceptation:**
- Chaque transaction génère un reçu numéroté
- Les reçus sont imprimables en format A5 ou A4
- Le système calcule automatiquement les totaux

---

### 8. Pharmacie

**Description:** Gestion du stock de médicaments et des ventes.

**Fonctionnalités:**
- ✅ Catalogue de médicaments avec:
  - Nom commercial
  - Stock disponible
  - Prix de vente
  - Statut (actif/inactif)
- ✅ Déduction automatique du stock lors des ventes
- ✅ Alertes de stock faible
- ✅ Recherche de médicaments
- ✅ API pour intégration avec ordonnances

**Routes:**
- `/pharmacie` - Liste des médicaments
- `/pharmacie-api/medicaments` - API liste
- `/pharmacie-api/medicament/{id}` - API détail
- `/pharmacie-api/medicament/{id}/deduire-stock` - API déduction stock

**Critères d'Acceptation:**
- Le stock est mis à jour en temps réel
- Les ventes impossibles si stock insuffisant
- Recherche rapide par nom de médicament

---

### 9. Rendez-vous

**Description:** Système de prise de rendez-vous pour les consultations.

**Fonctionnalités:**
- ✅ Création de rendez-vous:
  - Patient
  - Médecin
  - Date et heure
  - Motif
  - Statut (confirmé, annulé, reporté)
- ✅ Calendrier des rendez-vous
- ✅ Filtrage par date
- ✅ Changement de statut
- ✅ Numéro d'entrée unique
- ✅ Export PDF et impression

**Routes:**
- `/superadmin/rendezvous` - CRUD complet
- `/admin/rendezvous` - CRUD complet
- `/rendezvous/{id}/change-status` - Changement statut
- `/rendezvous/get-by-date` - API par date
- `/rendezvous/print` - Impression liste

**Critères d'Acceptation:**
- Les rendez-vous peuvent être créés jusqu'à 3 mois à l'avance
- Le système empêche les doubles réservations
- Les notifications peuvent être envoyées (futur)

---

### 10. Dossiers Médicaux

**Description:** Centralisation et synchronisation des dossiers médicaux.

**Fonctionnalités:**
- ✅ Vue consolidée du dossier patient
- ✅ Synchronisation automatique des données
- ✅ Historique complet:
  - Consultations
  - Examens
  - Hospitalisations
  - Ordonnances
- ✅ Export PDF du dossier complet

**Routes:**
- `/dossiers` - Liste des dossiers
- `/dossiers/{id}` - Détail du dossier
- `/dossiers/synchroniser` - Synchronisation manuelle

**Critères d'Acceptation:**
- La synchronisation se fait automatiquement à chaque modification
- Le dossier est accessible en moins de 3 secondes
- Toutes les données sont à jour

---

### 11. Gestion Financière

#### 11.1 États de Caisse

**Description:** Génération d'états de caisse pour différentes entités.

**Fonctionnalités:**
- ✅ État général (toutes les transactions)
- ✅ État par personnel (crédits)
- ✅ État par assurance
- ✅ État journalier
- ✅ Validation/Invalidation des états
- ✅ Export PDF et impression

**Routes:**
- `/etatcaisse` - Liste des états
- `/etatcaisse/generer/general` - Générer état général
- `/etatcaisse/generer/personnel/{id}` - État personnel
- `/etatcaisse/generer/assurance/{id}` - État assurance
- `/etatcaisse/generer/journalier` - État journalier
- `/etatcaisse/{id}/valider` - Validation (SuperAdmin uniquement)

**Critères d'Acceptation:**
- Les états sont générés en moins de 5 secondes
- Les calculs sont exacts à 100%
- Les PDF sont au format professionnel

#### 11.2 Crédits

**Description:** Gestion des crédits accordés aux patients et personnels.

**Fonctionnalités:**
- ✅ Enregistrement des crédits
- ✅ Suivi des remboursements
- ✅ Paiement partiel ou total
- ✅ Statuts (en cours, payé, impayé)
- ✅ Historique des paiements

**Routes:**
- `/credits` - Liste des crédits
- `/credits/create` - Nouveau crédit
- `/credits/{credit}/payer` - Paiement crédit
- `/credits/{credit}/payer-salaire` - Paiement salaire

**Critères d'Acceptation:**
- Les crédits sont traçables jusqu'au remboursement complet
- Les intérêts peuvent être calculés (futur)

#### 11.3 Dépenses

**Description:** Enregistrement et suivi des dépenses de la clinique.

**Fonctionnalités:**
- ✅ Enregistrement des dépenses:
  - Type de dépense
  - Montant
  - Date
  - Description
  - Pièce justificative (futur)
- ✅ Filtres par date et type
- ✅ Export PDF et impression

**Routes:**
- `/depenses` - Liste (SuperAdmin)
- `/depenses/create` - Création (Admin et SuperAdmin)
- `/depenses-export-pdf` - Export PDF
- `/depenses-print` - Impression

**Critères d'Acceptation:**
- Toutes les dépenses sont enregistrées avec justification
- Les rapports sont générables par période

#### 11.4 Salaires

**Description:** Gestion de la paie du personnel.

**Fonctionnalités:**
- ✅ Calcul automatique des salaires
- ✅ Paiement individuel ou global
- ✅ Génération de fiches de paie PDF
- ✅ Historique des paiements

**Routes:**
- `/salaires` - Liste des salaires
- `/salaires/pdf` - Export PDF
- `/salaires/payer-tout` - Paiement global
- `/salaires/{personnelId}/payer` - Paiement individuel

**Critères d'Acceptation:**
- Les salaires sont calculés automatiquement selon les règles définies
- Les fiches de paie sont conformes aux normes légales

---

### 12. Situation Journalière

**Description:** Rapport quotidien consolidé de toutes les activités financières.

**Fonctionnalités:**
- ✅ Vue d'ensemble par service:
  - Échographie
  - Radiographie
  - Consultations (Généraliste/Spécialiste)
  - Hospitalisation
  - Soins infirmiers
  - Laboratoire
- ✅ Détails par médecin:
  - Nombre d'actes
  - Recettes
  - Part médecin
- ✅ Totaux automatiques:
  - Total recettes
  - Répartition par mode de paiement
  - Total parts médecins
- ✅ Filtrage par date
- ✅ Export PDF et impression

**Routes:**
- `/superadmin/situation-journaliere` - Vue SuperAdmin
- `/admin/situation-journaliere` - Vue Admin
- `/situation-journaliere/print` - Impression
- `/situation-journaliere/export-pdf` - Export PDF

**Critères d'Acceptation:**
- Le rapport est généré en moins de 10 secondes
- Tous les totaux sont exacts
- Le format est lisible et professionnel

---

### 13. Récapitulatifs

#### 13.1 Récapitulatif par Service

**Description:** Rapport détaillé des activités par service sur une période.

**Fonctionnalités:**
- ✅ Filtrage par date
- ✅ Détails par service
- ✅ Totaux par catégorie
- ✅ Export PDF et impression

**Routes:**
- `/recap-services` - Liste
- `/recap-services/print` - Impression
- `/recap-services/export-pdf` - Export PDF

#### 13.2 Récapitulatif par Opérateur

**Description:** Rapport des activités par médecin/opérateur.

**Fonctionnalités:**
- ✅ Filtrage par date
- ✅ Détails par médecin
- ✅ Nombre d'actes et recettes
- ✅ Export PDF et impression

**Routes:**
- `/recap-operateurs` - Liste
- `/recap-operateurs-print` - Impression
- `/recap-operateurs-export-pdf` - Export PDF

---

### 14. Gestion des Services

**Description:** Administration des services médicaux offerts par la clinique.

**Fonctionnalités:**
- ✅ CRUD complet des services
- ✅ Catégorisation (Échographie, Radiographie, Consultation, etc.)
- ✅ Gestion des prix
- ✅ Association avec la pharmacie (pour médicaments)
- ✅ Export PDF et impression

**Routes:**
- `/services` - CRUD complet
- `/services/export-pdf` - Export PDF
- `/services/print` - Impression

**Critères d'Acceptation:**
- Les services peuvent être activés/désactivés
- Les prix sont modifiables à tout moment
- Les modifications sont tracées

---

### 15. Gestion des Assurances

**Description:** Administration des compagnies d'assurance partenaires.

**Fonctionnalités:**
- ✅ Enregistrement des assurances
- ✅ Taux de prise en charge
- ✅ Suivi des remboursements
- ✅ États de compte par assurance
- ✅ Export PDF et impression

**Routes:**
- `/assurances` - CRUD complet
- `/assurances/export/pdf` - Export PDF
- `/assurances/print` - Impression

**Critères d'Acceptation:**
- Les assurances peuvent être associées aux transactions
- Les remboursements sont traçables

---

### 16. Gestion des Prescripteurs

**Description:** Administration des médecins prescripteurs externes.

**Fonctionnalités:**
- ✅ Enregistrement des prescripteurs
- ✅ Informations de contact
- ✅ Historique des prescriptions
- ✅ Export PDF et impression

**Routes:**
- `/prescripteurs` - CRUD complet
- `/prescripteurs/print` - Impression
- `/prescripteurs/export-pdf` - Export PDF

---

### 17. Gestion du Personnel

**Description:** Administration du personnel administratif et médical.

**Fonctionnalités:**
- ✅ Enregistrement du personnel
- ✅ Fonction et service
- ✅ Informations de contact
- ✅ Gestion des salaires
- ✅ Historique des activités

**Routes:**
- `/personnels` - CRUD complet

**Critères d'Acceptation:**
- Chaque personnel peut avoir un compte utilisateur
- Les rôles sont assignables

---

### 18. Gestion des Chambres et Lits

**Description:** Administration des ressources d'hospitalisation.

**Fonctionnalités:**
- ✅ Gestion des chambres:
  - Numéro
  - Type (simple, double, VIP)
  - Capacité
  - Statut (disponible, occupée, maintenance)
- ✅ Gestion des lits:
  - Association à une chambre
  - Statut (disponible, occupé, maintenance)
- ✅ API pour vérification de disponibilité

**Routes:**
- `/chambres` - CRUD complet
- `/lits` - CRUD complet
- `/chambres-api/disponibles` - API chambres disponibles
- `/lits-api/disponibles` - API lits disponibles

**Critères d'Acceptation:**
- Le système affiche uniquement les ressources disponibles
- Les changements de statut sont instantanés

---

### 19. Gestion des Motifs de Consultation

**Description:** Administration des motifs de consultation standards.

**Fonctionnalités:**
- ✅ CRUD des motifs
- ✅ Activation/désactivation
- ✅ Utilisation dans les consultations
- ✅ API pour récupération des motifs actifs

**Routes:**
- `/motifs` - CRUD complet
- `/motifs/{id}/toggle-status` - Activer/désactiver
- `/motifs/get-actifs` - API motifs actifs

---

### 20. Modes de Paiement

**Description:** Gestion des différents modes de paiement acceptés.

**Fonctionnalités:**
- ✅ Enregistrement des modes de paiement
- ✅ Statistiques par mode
- ✅ Dashboard des paiements
- ✅ Historique des transactions

**Routes:**
- `/modepaiements` - CRUD complet
- `/mode-paiements/dashboard` - Dashboard
- `/mode-paiements/historique` - Historique

---

## 🏗️ Architecture Technique

### Stack Technologique

**Backend:**
- **Framework:** Laravel 12.0
- **Langage:** PHP 8.2+
- **Base de données:** MySQL/MariaDB (SQLite en développement)
- **Cache:** Redis (via Predis)
- **Queue:** Laravel Queue System

**Frontend:**
- **Framework:** Livewire 3.x avec Volt
- **UI Components:** Livewire Flux 2.1
- **Styling:** Tailwind CSS
- **JavaScript:** Vanilla JS (via Livewire)

**Outils de Développement:**
- **Tests:** Pest PHP 3.8
- **Code Quality:** Laravel Pint
- **PDF Generation:** DomPDF
- **Build Tool:** Vite

### Structure du Projet

```
clinique-ibn-rochd/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # 35 contrôleurs
│   │   └── Middleware/         # 10 middlewares
│   ├── Livewire/
│   ├── Models/                 # 30+ modèles
│   └── Providers/
├── database/
│   ├── migrations/             # 71 migrations
│   ├── seeders/               # 22 seeders
│   └── factories/
├── resources/
│   ├── views/                 # 152 vues Blade
│   ├── css/
│   └── js/
├── routes/
│   └── web.php                # Routes principales
├── config/
│   └── clinique.php           # Configuration clinique
└── public/
    └── images/
        └── logo.png
```

### Base de Données

**Tables Principales:**
- `users` - Utilisateurs du système
- `roles` - Rôles (superadmin, admin, medecin)
- `patients` - Dossiers patients
- `medecins` - Médecins
- `consultations` - Rapports de consultation
- `ordonnances` - Prescriptions médicales
- `medicaments` - Catalogue de médicaments
- `examens` - Examens médicaux
- `services` - Services offerts
- `hospitalisations` - Hospitalisations
- `chambres` - Chambres d'hospitalisation
- `lits` - Lits d'hospitalisation
- `caisses` - Transactions financières
- `credits` - Crédits accordés
- `depenses` - Dépenses
- `etat_caisses` - États de caisse
- `assurances` - Compagnies d'assurance
- `prescripteurs` - Médecins prescripteurs
- `personnels` - Personnel
- `rendez_vous` - Rendez-vous
- `motifs` - Motifs de consultation
- `pharmacies` - Stock pharmacie
- `payrolls` - Salaires

**Relations Clés:**
- User → Role (Many-to-One)
- User → Medecin (Many-to-One)
- Patient → Consultations (One-to-Many)
- Patient → Examens (One-to-Many)
- Patient → Hospitalisations (One-to-Many)
- Medecin → Consultations (One-to-Many)
- Examen → Service (Many-to-One)
- Hospitalisation → Chambre → Lit (Many-to-One)
- Caisse → Patient, Medecin, Service (Many-to-One)

---

## 🔐 Rôles et Permissions

### Hiérarchie des Rôles

```
SuperAdmin (Niveau 3)
    ↓
Admin (Niveau 2)
    ↓
Médecin (Niveau 1)
```

### Matrice des Permissions

| Module | SuperAdmin | Admin | Médecin |
|--------|------------|-------|---------|
| **Gestion Utilisateurs** |
| Gestion des Admins | ✅ | ❌ | ❌ |
| Approbation des comptes | ✅ | ❌ | ❌ |
| **Gestion Patients** |
| CRUD Patients | ✅ | ✅ | 👁️ (ses patients) |
| **Gestion Médecins** |
| CRUD Médecins | ✅ | ✅ | ❌ |
| Statistiques médecins | ✅ | ✅ | 👁️ (ses stats) |
| **Consultations** |
| Créer consultation | ❌ | ❌ | ✅ |
| Voir consultations | ✅ | ✅ | ✅ (ses consultations) |
| **Ordonnances** |
| Créer ordonnance | ❌ | ❌ | ✅ |
| Voir ordonnances | ✅ | ✅ | ✅ (ses ordonnances) |
| **Examens** |
| CRUD Examens | ✅ | ✅ | ❌ |
| **Hospitalisations** |
| CRUD Hospitalisations | ✅ | ✅ | ❌ |
| Facturation | ✅ | ✅ | ❌ |
| **Caisse** |
| CRUD Transactions | ✅ | ✅ | ❌ |
| **Pharmacie** |
| Gestion stock | ✅ | ✅ | ❌ |
| **Rendez-vous** |
| CRUD Rendez-vous | ✅ | ✅ | ❌ |
| **Dossiers Médicaux** |
| Synchronisation | ✅ | ✅ | ❌ |
| Consultation | ✅ | ✅ | ✅ (ses patients) |
| **Finances** |
| États de caisse | ✅ | ✅ | ❌ |
| Crédits | ✅ | ✅ | ❌ |
| Dépenses | ✅ | ✅ (création) | ❌ |
| Salaires | ✅ | ❌ | ❌ |
| **Rapports** |
| Situation journalière | ✅ | ✅ | ❌ |
| Récapitulatifs | ✅ | ✅ | ❌ |

### Système d'Approbation

- **Nouveaux utilisateurs:** Doivent être approuvés par un SuperAdmin
- **Page d'attente:** `/waiting-approval` pour les utilisateurs non approuvés
- **Middleware:** `is.approved` vérifie l'approbation sur toutes les routes protégées

---

## 📱 Modules Détaillés

### Module Authentification

**Fonctionnalités:**
- ✅ Connexion avec email/mot de passe
- ✅ Inscription avec approbation
- ✅ Déconnexion
- ✅ Gestion des sessions
- ✅ Suivi de la dernière connexion

**Sécurité:**
- Mots de passe hashés (bcrypt)
- Protection CSRF
- Rate limiting sur les tentatives de connexion

---

### Module Impression et Export

**Fonctionnalités:**
- ✅ Génération PDF avec DomPDF
- ✅ Formats A4 et A5
- ✅ En-têtes et pieds de page personnalisables
- ✅ Logo de la clinique
- ✅ Informations bilingues (FR/AR)

**Documents Imprimables:**
- Reçus de caisse
- Consultations
- Ordonnances
- Examens
- Hospitalisations
- États de caisse
- Récapitulatifs
- Situation journalière
- Dossiers médicaux

**Routes d'Impression:**
- Tous les modules ont des routes `/print` et `/export-pdf`

---

### Module API REST

**Endpoints Disponibles:**

1. **Caisse:**
   - `GET /api/caisses/numero-entree/{medecin_id}` - Prochain numéro d'entrée

2. **Examens:**
   - `GET /api/examens/{id}/stock-info` - Informations de stock

3. **Hospitalisations:**
   - `GET /hospitalisations/search-patients-by-phone` - Recherche patient
   - `GET /hospitalisations/lits-disponibles` - Lits disponibles

4. **Chambres:**
   - `GET /chambres-api/disponibles` - Chambres disponibles

5. **Lits:**
   - `GET /lits-api/disponibles` - Lits disponibles

6. **Pharmacie:**
   - `GET /pharmacie-api/medicaments` - Liste médicaments
   - `GET /pharmacie-api/medicament/{id}` - Détail médicament
   - `POST /pharmacie-api/medicament/{id}/deduire-stock` - Déduction stock

7. **Consultations:**
   - `GET /medecin/consultations/search-patients` - Recherche patients

8. **Ordonnances:**
   - `GET /medecin/ordonnances/search-medicaments` - Recherche médicaments

9. **Rendez-vous:**
   - `GET /api/next-numero-entree-rdv` - Prochain numéro RDV
   - `GET /rendezvous/get-by-date` - Rendez-vous par date

10. **Motifs:**
    - `GET /motifs/get-actifs` - Motifs actifs

---

## ⚙️ Exigences Non-Fonctionnelles

### Performance

- **Temps de chargement:** < 2 secondes pour les pages principales
- **Recherche:** < 1 seconde pour les recherches de patients
- **Génération PDF:** < 5 secondes pour les rapports complexes
- **Concurrent Users:** Support de 100+ utilisateurs simultanés

### Disponibilité

- **Uptime cible:** 99.5%
- **Maintenance:** Fenêtres de maintenance planifiées
- **Backup:** Sauvegardes quotidiennes automatiques

### Compatibilité

- **Navigateurs:** Chrome, Firefox, Safari, Edge (dernières versions)
- **Responsive:** Tablettes et smartphones
- **Résolution:** Optimisé pour 1920x1080 et supérieur

### Accessibilité

- **Langues:** Français (principal), Arabe (partiel)
- **Contraste:** Conforme WCAG 2.1 AA
- **Navigation clavier:** Support complet

---

## 🔒 Sécurité

### Mesures de Sécurité Implémentées

1. **Authentification:**
   - Mots de passe hashés (bcrypt)
   - Protection CSRF sur tous les formulaires
   - Rate limiting sur les tentatives de connexion
   - Sessions sécurisées

2. **Autorisation:**
   - Middleware de rôles (`role:superadmin,admin`)
   - Vérification d'approbation (`is.approved`)
   - Contrôle d'accès au niveau des routes

3. **Protection des Données:**
   - Validation stricte des entrées
   - Protection contre les injections SQL (Eloquent ORM)
   - Échappement XSS (Blade)
   - Chiffrement des données sensibles

4. **Audit:**
   - Logs des actions importantes
   - Traçabilité des modifications
   - Historique des transactions

### Conformité

- **RGPD:** Conformité avec les réglementations sur les données médicales
- **HIPAA:** Principes de sécurité des données de santé (adapté au contexte mauritanien)
- **Sauvegarde:** Données médicales sauvegardées quotidiennement

---

## 🎨 Interface Utilisateur

### Design System

- **Framework UI:** Livewire Flux 2.1
- **Styling:** Tailwind CSS
- **Couleurs principales:**
  - Bleu roi: `#1e40af` (Primary)
  - Gris: Palette Tailwind standard
- **Typographie:** Système par défaut du navigateur

### Composants Principaux

1. **Dashboards:**
   - Cartes statistiques
   - Graphiques (futur)
   - Accès rapide aux modules

2. **Formulaires:**
   - Validation en temps réel
   - Messages d'erreur clairs
   - Auto-complétion

3. **Tableaux:**
   - Tri par colonnes
   - Pagination
   - Recherche intégrée
   - Actions en ligne

4. **Modales:**
   - Confirmation d'actions
   - Formulaires rapides
   - Affichage de détails

### Responsive Design

- **Desktop:** Layout complet avec sidebar
- **Tablette:** Layout adaptatif
- **Mobile:** Navigation hamburger, cartes empilées

---

## 🔌 Intégrations

### Intégrations Actuelles

1. **DomPDF:** Génération de documents PDF
2. **Redis:** Cache et sessions
3. **MySQL/MariaDB:** Base de données principale

### Intégrations Futures (Roadmap)

1. **Système de paiement:** Intégration avec processeurs de paiement locaux
2. **SMS:** Notifications par SMS pour rendez-vous
3. **Email:** Notifications par email
4. **API externe:** Intégration avec systèmes d'assurance
5. **Imagerie médicale:** Stockage et visualisation d'images médicales

---

## 📈 Performance et Scalabilité

### Optimisations Actuelles

- **Cache:** Configuration et routes mises en cache
- **Lazy Loading:** Relations Eloquent chargées à la demande
- **Indexation:** Index sur les colonnes fréquemment recherchées
- **Pagination:** Limitation des résultats affichés

### Scalabilité

- **Base de données:** Support de millions d'enregistrements
- **Serveur:** Architecture horizontale possible
- **CDN:** Assets statiques servis via CDN (futur)

### Monitoring

- **Logs:** Laravel Log pour le débogage
- **Performance:** Monitoring des requêtes lentes (futur)
- **Erreurs:** Tracking des erreurs (futur)

---

## 🚀 Plan de Déploiement

### Environnements

1. **Développement:** Local avec SQLite
2. **Staging:** Serveur de test (optionnel)
3. **Production:** Serveur principal (ibnrochd.pro)

### Processus de Déploiement

1. **Pré-déploiement:**
   - Tests en local
   - Vérification des migrations
   - Backup de la base de données

2. **Déploiement:**
   - Pull du code depuis Git
   - Installation des dépendances
   - Exécution des migrations
   - Compilation des assets
   - Nettoyage des caches

3. **Post-déploiement:**
   - Vérification des fonctionnalités
   - Monitoring des logs
   - Tests de régression

### Scripts de Déploiement

- `deploy-production.sh` (Linux/Mac)
- `deploy-production.ps1` (Windows)
- `deploy.sh` (Script alternatif)

---

## ✅ Critères d'Acceptation

### Critères Généraux

1. **Fonctionnalité:**
   - Toutes les fonctionnalités listées sont implémentées
   - Les workflows sont complets et fonctionnels
   - Les validations sont en place

2. **Performance:**
   - Temps de chargement < 2 secondes
   - Recherches < 1 seconde
   - Génération PDF < 5 secondes

3. **Sécurité:**
   - Authentification sécurisée
   - Autorisation par rôles
   - Protection des données sensibles

4. **Qualité:**
   - Code testé et documenté
   - Interface utilisateur intuitive
   - Gestion d'erreurs appropriée

5. **Compatibilité:**
   - Fonctionne sur les navigateurs modernes
   - Responsive sur mobile et tablette
   - Support multilingue (FR/AR)

### Critères par Module

Chaque module doit respecter:
- ✅ CRUD complet fonctionnel
- ✅ Validation des données
- ✅ Gestion des erreurs
- ✅ Export PDF (si applicable)
- ✅ Recherche et filtres
- ✅ Permissions appropriées

---

## 📅 Roadmap Future

### Phase 2 (Q2 2025)

- [ ] Module de messagerie interne
- [ ] Notifications push
- [ ] Application mobile (React Native)
- [ ] Intégration paiement mobile
- [ ] Tableaux de bord avancés avec graphiques

### Phase 3 (Q3 2025)

- [ ] Stockage d'images médicales
- [ ] Télémédecine (consultations à distance)
- [ ] Intégration laboratoire externe
- [ ] Module de facturation avancée
- [ ] Export Excel amélioré

### Phase 4 (Q4 2025)

- [ ] Intelligence artificielle pour diagnostics
- [ ] Prédiction de charge de travail
- [ ] Optimisation des rendez-vous
- [ ] Analytics avancés
- [ ] Intégration avec systèmes gouvernementaux

---

## 📞 Support et Maintenance

### Documentation

- **Guide utilisateur:** Disponible pour chaque module
- **Documentation technique:** Code commenté et README
- **Guides de déploiement:** DEPLOYMENT-GUIDE.md

### Support

- **Email:** support@ibnrochd.pro (à définir)
- **Logs:** `storage/logs/laravel.log`
- **Monitoring:** À mettre en place

### Maintenance

- **Mises à jour:** Mensuelles (sécurité et fonctionnalités)
- **Backups:** Quotidiens automatiques
- **Tests:** Exécution avant chaque déploiement

---

## 📝 Annexes

### A. Glossaire

- **Caisse:** Système de gestion des transactions financières
- **État de caisse:** Rapport financier consolidé
- **Situation journalière:** Rapport quotidien des activités
- **Récapitulatif:** Rapport détaillé par période
- **Dossier médical:** Dossier complet d'un patient
- **Prescripteur:** Médecin externe prescrivant des examens
- **Assurance:** Compagnie d'assurance partenaire

### B. Références Techniques

- Laravel Documentation: https://laravel.com/docs
- Livewire Documentation: https://livewire.laravel.com/docs
- Tailwind CSS: https://tailwindcss.com/docs
- DomPDF: https://github.com/dompdf/dompdf

### C. Contacts

- **Développement:** Équipe technique
- **Direction:** Dr Brahim Ould Ntaghry
- **Support:** À définir

---

**Document Version:** 1.0  
**Dernière Mise à Jour:** Janvier 2025  
**Prochaine Révision:** Trimestrielle

---

*Ce document est un document vivant et sera mis à jour régulièrement pour refléter l'évolution du système.*





