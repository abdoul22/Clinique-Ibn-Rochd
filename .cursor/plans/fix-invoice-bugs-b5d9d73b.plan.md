<!-- b5d9d73b-49ef-449d-ba85-d41b36fc5bfe f4553fd7-464c-4769-96c8-7a885ec67e11 -->
# Correction du Bug de Paiement des Hospitalisations

## Analyse du Problème

### Symptômes observés

- **Hospitalisation #49 (Facture #103)**: Total affiché = 2 760 MRU, Mode de paiement enregistré = 3 260 MRU (différence de +500 MRU)
- **Hospitalisation #50 (Facture #104)**: Total affiché = 30 600 MRU, Mode de paiement enregistré = 31 100 MRU (différence de +500 MRU)

La différence systématique de **+500 MRU** correspond exactement à la **part médecin** qui est ajoutée au montant du mode de paiement.

### Localisation du Bug

Dans `app/Http/Controllers/HospitalisationController.php`, méthode `payerTout` (lignes 793-965):

**Code actuel (lignes 816-822, 925):**

```php
$total = (float) $charges->sum('total_price');
$partCabinet = (float) $charges->sum('part_cabinet');
$partMedecin = (float) $charges->sum('part_medecin');

$assuranceId = $hospitalisation->assurance_id;
$couverture = (float) ($hospitalisation->couverture ?? 0);
$patientPart = $assuranceId ? $total * (1 - ($couverture / 100)) : $total;

// ... plus tard ...

ModePaiement::create([
    'caisse_id' => $caisse->id,
    'type' => $request->type,
    'montant' => $patientPart,  // ✅ CORRECT
    'source' => 'caisse',
]);
```

Le code du contrôleur semble correct : il utilise `$patientPart` qui est égal à `$total` (sans assurance).

### Hypothèses sur la source du bug

1. **JavaScript dans la vue**: Il se peut qu'un script JavaScript modifie le montant lors de l'envoi du formulaire
2. **Événement d'écoute sur ModePaiement**: Un listener ou observer pourrait modifier le montant après création
3. **Trigger de base de données**: Un trigger SQL pourrait ajouter la part médecin
4. **Autre méthode de facturation**: Il pourrait exister une autre méthode `facturer()` qui est utilisée et qui a le bug

## Plan de Correction

### Étape 1: Vérifier qu'il n'y a pas de méthode `facturer()` utilisée

Vérifier si la méthode `facturer()` (lignes 330-441) est utilisée au lieu de `payerTout()`:

```php:app/Http/Controllers/HospitalisationController.php:420-427
// Paiement (uniquement part patient)
ModePaiement::create([
    'caisse_id' => $caisse->id,
    'type' => $request->type,
    'montant' => $patientPart,
    'source' => 'caisse',
]);
```

Cette méthode semble également correcte.

### Étape 2: Rechercher les Observers et Events

Chercher si un observer ou event listener modifie le montant après création du ModePaiement:

```bash
grep -r "ModePaiement.*Observer" app/
grep -r "ModePaiement::creating" app/
grep -r "ModePaiement::created" app/
```

### Étape 3: Vérifier le modèle ModePaiement

Chercher des mutateurs ou accesseurs qui pourraient modifier le montant:

```app/Models/ModePaiement.php
// Rechercher:
- getMontantAttribute()
- setMontantAttribute()
- $appends contenant 'montant'
```

### Étape 4: Vérifier le JavaScript de la vue

Chercher dans `resources/views/hospitalisations/show.blade.php` s'il y a du JavaScript qui ajoute la part médecin au montant lors de la soumission du formulaire de paiement.

### Étape 5: Ajouter une protection supplémentaire

Même si le code semble correct, ajouter une validation explicite pour s'assurer que le montant enregistré dans `mode_paiements` correspond exactement au total de la facture:

```php:app/Http/Controllers/HospitalisationController.php:922-927
// Paiement - S'assurer que le montant = part patient uniquement
$montantPaiement = $patientPart; // Ne PAS inclure la part médecin

ModePaiement::create([
    'caisse_id' => $caisse->id,
    'type' => $request->type,
    'montant' => $montantPaiement,
    'source' => 'caisse',
]);

// Log de vérification
\Log::info("Paiement créé - Caisse: {$caisse->id}, Total: {$total}, Patient Part: {$patientPart}, Montant Paiement: {$montantPaiement}");
```

### Étape 6: Corriger le problème une fois identifié

Selon les résultats des étapes 1-4, appliquer la correction appropriée:

- Si c'est un Observer/Event: Le modifier ou le supprimer
- Si c'est un mutateur dans le modèle: Le corriger
- Si c'est le JavaScript: Le corriger dans la vue
- Si c'est la mauvaise méthode utilisée: Utiliser `payerTout()` au lieu de `facturer()`

## Fichiers à vérifier/modifier

1. `app/Http/Controllers/HospitalisationController.php` - Méthodes `payerTout()` et `facturer()`
2. `app/Models/ModePaiement.php` - Rechercher mutateurs/accesseurs
3. `app/Providers/EventServiceProvider.php` - Vérifier les listeners
4. `app/Observers/` - Vérifier s'il existe un ModePaiementObserver
5. `resources/views/hospitalisations/show.blade.php` - Vérifier le JavaScript du formulaire de paiement

## Note

Cette correction ne modifiera PAS les données existantes (Factures #103 et #104). Elle empêchera seulement le problème pour les nouvelles factures.

### To-dos

- [ ] Modifier CaisseController::store pour recalculer automatiquement le total côté serveur
- [ ] Ajouter validation stricte du total dans CaisseController::store
- [ ] Modifier caisses/show.blade.php pour afficher tous les examens
- [ ] Modifier RecapServiceController pour décomposer examens_data par service
- [ ] Modifier RecapOperateurController pour décomposer examens_data par opérateur
- [ ] Modifier HospitalisationController pour séparer ROOM_DAY des autres actes