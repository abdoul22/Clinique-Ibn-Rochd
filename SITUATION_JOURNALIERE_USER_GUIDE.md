# Guide d'Utilisation - Situation Journalière

## Accès au Module

### Pour le Superadmin

1. Connectez-vous à: `http://localhost:8000/dashboard/superadmin`
2. Cliquez sur la carte **"Situation Journalière"** (gradient violet-purple-fuchsia)
3. Vous serez redirigé vers: `http://localhost:8000/superadmin/situation-journaliere`

### Pour l'Admin

1. Connectez-vous à: `http://localhost:8000/dashboard/admin`
2. Cliquez sur la carte **"Situation Journalière"** (gradient violet-purple-fuchsia)
3. Vous serez redirigé vers: `http://localhost:8000/admin/situation-journaliere`

## Fonctionnalités

### 1. Sélection de la Date

-   **Par défaut:** La date du jour est affichée
-   **Changer la date:** Cliquez sur le champ de date et sélectionnez une date
-   **Filtrer:** Cliquez sur le bouton "Filtrer" pour afficher les données de la date sélectionnée

### 2. Visualisation des Données

#### Section Gauche (Services)

Les services sont automatiquement regroupés par catégorie:

**ECHOGRAPHIE** (Bleu)

-   Affiche tous les examens d'échographie de la journée
-   Colonnes: Examens | Nbre | Recette
-   Total automatique

**RADIOGRAPHIE** (Vert)

-   RX SIMPLE, RX P.C, RX HSG, RX MAMMO, etc.
-   Colonnes: Examens | Nbre | Recette
-   Total automatique

**GENERALISTE** (Jaune)

-   Consultations généralistes
-   Colonnes: Examens | Nbre | Recette
-   Total automatique

**SPECIALISTE** (Violet)

-   Consultations spécialistes (ORL, Cardio, Gastro, Gynéco, etc.)
-   Colonnes: Examens | Nbre | Recette
-   Total automatique

**HOSPITALISATION** (Rose)

-   Mise en observation
-   Total des hospitalisations

**SOINS INFIRMIERS** (Turquoise)

-   Pansement, Suture, Pose plâtre, Ablation plâtre
-   Total des soins

**LABORATOIRE** (Orange)

-   Examens de laboratoire
-   Total des analyses

#### Section Droite (Résumé et Financier)

**TOTAL RECETTES** (Vert Émeraude - Grande Boîte)

-   Somme totale de toutes les recettes de la journée
-   Affiché en gros caractères

**MODES DE PAIEMENT**

-   💵 **Espèces:** Montant total en espèces
-   📱 **Bankily:** Montant total Bankily
-   📱 **Masrivi:** Montant total Masrivi
-   📱 **Sedad:** Montant total Sedad

**PARTS MÉDECINS**

-   Liste de tous les médecins avec:
    -   Nom du médecin
    -   Nombre de consultations
    -   Part du médecin (calculée automatiquement)
-   Total des parts médecins

**DÉPENSES**

-   Liste groupée par type de dépense:
    -   Garde
    -   Maintenance
    -   Gazoil
    -   Autres
-   Total des dépenses

**CRÉDITS**

-   Crédit Personnel (non payé)
-   Crédit Assurance (non payé)
-   Total des crédits

**PHARMACIE**

-   Total des ventes pharmacie (si applicable)

**💰 LIQUIDITÉ EN ESPÈCES** (Violet-Purple-Fuchsia - IMPORTANT)
Cette boîte calcule automatiquement:

```
Recettes (Espèces): XXXXX MRU
Dépenses (Espèces): -XXXXX MRU
─────────────────────────────
RESTANT: XXXXX MRU
```

**C'est le montant d'espèces que le caissier doit avoir en fin de journée!**

**RÉSUMÉ GLOBAL**

-   Recettes totales
-   Dépenses
-   Crédits
-   Pharmacie
-   Liquidité (Espèces)

### 3. Impression

**Bouton "🖨️ Imprimer"**

-   Cliquez sur ce bouton
-   Une nouvelle fenêtre s'ouvrira automatiquement
-   La fenêtre d'impression du navigateur apparaîtra automatiquement
-   Le format est optimisé pour l'impression sur papier A4

### 4. Export PDF

**Bouton "📄 PDF"**

-   Cliquez sur ce bouton
-   Le fichier PDF sera téléchargé automatiquement
-   Nom du fichier: `situation-journaliere-YYYY-MM-DD.pdf`
-   Format professionnel, prêt à archiver

## Calcul de la Liquidité en Espèces

### Formule

```
LIQUIDITÉ = RECETTES ESPÈCES - DÉPENSES ESPÈCES
```

### Exemple

Si pour une journée:

-   Recettes en espèces: **123,000 MRU**
-   Dépenses en espèces: **9,500 MRU**

Alors:

```
LIQUIDITÉ = 123,000 - 9,500 = 113,500 MRU
```

Le caissier doit avoir **113,500 MRU en espèces** en fin de journée.

### Vérification

À la fin de la journée:

1. Le caissier compte les espèces en caisse
2. Compare avec le montant "RESTANT" affiché
3. Si les montants correspondent → ✅ Caisse équilibrée
4. Si différence → Vérifier les transactions

## Données Affichées

### Source des Données

Le module récupère automatiquement les données de:

-   **Caisse** (table `caisses`)
-   **Dépenses** (table `depenses`)
-   **Modes de Paiement** (table `mode_paiements`)
-   **Crédits** (table `credits`)
-   **Hospitalisations** (table `hospitalisations`)
-   **Assurances** (table `assurances`)

### Filtrage par Date

Toutes les données sont filtrées par:

-   **Pour les recettes:** `date_examen`
-   **Pour les dépenses:** `created_at`
-   **Pour les hospitalisations:** `date_entree`

## Cas d'Usage

### Utilisation Quotidienne

**Fin de journée (17h-18h):**

1. Le caissier accède au module
2. La date du jour est automatiquement sélectionnée
3. Il vérifie toutes les sections
4. Il note le montant "LIQUIDITÉ EN ESPÈCES"
5. Il compte les espèces en caisse
6. Il compare les deux montants
7. Il imprime ou exporte en PDF pour archivage

### Vérification Historique

**Pour vérifier une journée passée:**

1. Accéder au module
2. Cliquer sur le champ de date
3. Sélectionner la date à vérifier
4. Cliquer sur "Filtrer"
5. Toutes les données de cette date s'affichent

### Rapports Mensuels

**Fin de mois:**

1. Accéder au module pour chaque jour du mois
2. Exporter en PDF chaque journée
3. Archiver tous les PDF dans un dossier "Situation Journalière - Mois XX"

## Couleurs et Interface

### Carte du Dashboard

-   **Gradient:** Violet → Purple → Fuchsia
-   **Effet:** Ombre et élévation au survol
-   **Unique:** Différente de toutes les autres cartes

### Sections dans le Module

Chaque section a sa propre couleur pour faciliter la lecture:

-   🔵 Échographie (Bleu)
-   🟢 Radiographie (Vert)
-   🟡 Généraliste (Jaune)
-   🟣 Spécialiste (Violet)
-   🌸 Hospitalisation (Rose)
-   🔷 Soins Infirmiers (Turquoise)
-   🟠 Laboratoire (Orange)

### Mode Sombre

Le module supporte le mode sombre:

-   Activation automatique selon les préférences du système
-   Couleurs adaptées pour une meilleure lisibilité

## Dépannage

### Problème: Aucune donnée n'apparaît

**Solution:**

1. Vérifiez que la date sélectionnée a des transactions
2. Vérifiez que des caisses ont été créées pour cette date
3. Essayez avec la date du jour si vous avez des transactions aujourd'hui

### Problème: Le total ne correspond pas

**Solution:**

1. Vérifiez que tous les paiements ont été enregistrés correctement
2. Vérifiez les modes de paiement dans la table `mode_paiements`
3. Assurez-vous que les dépenses ont le bon `mode_paiement_id`

### Problème: PDF ne se télécharge pas

**Solution:**

1. Vérifiez que DomPDF est installé (`composer require barryvdh/laravel-dompdf`)
2. Vérifiez les permissions du dossier `storage`
3. Consultez les logs dans `storage/logs/laravel.log`

### Problème: L'impression ne fonctionne pas

**Solution:**

1. Assurez-vous que les pop-ups ne sont pas bloqués
2. Autorisez les fenêtres pop-up pour ce site
3. Essayez avec un autre navigateur

## Support

Pour toute question ou problème:

1. Consultez ce guide d'abord
2. Vérifiez le fichier `SITUATION_JOURNALIERE_IMPLEMENTATION.md`
3. Contactez l'administrateur système

## Notes Importantes

⚠️ **À savoir:**

-   Les données sont générées **à la volée** (pas de sauvegarde dans une table séparée)
-   Les calculs sont faits en **temps réel** à partir des données existantes
-   Toujours vérifier que toutes les transactions de la journée sont enregistrées avant de consulter la situation
-   Le module ne modifie **aucune donnée**, il affiche uniquement

✅ **Avantages:**

-   Données toujours à jour
-   Pas de risque de désynchronisation
-   Calculs automatiques et précis
-   Accès rapide aux situations historiques

---

**Date de création:** {{ date('d/m/Y') }}
**Version:** 1.0
**Module:** Situation Journalière du Caissier
**Clinique Ibn Rochd**
