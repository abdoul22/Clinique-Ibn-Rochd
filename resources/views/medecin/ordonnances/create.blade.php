@extends('layouts.app')
@section('title', 'Nouvelle Ordonnance')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-4 sm:p-8">
    <div class="max-w-5xl mx-auto">
        <!-- En-tête -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">💊 Nouvelle Ordonnance</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Prescrire des médicaments à un patient</p>
                </div>
                <a href="{{ route('medecin.ordonnances.index') }}" 
                   class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    ← Retour
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulaire -->
        <form action="{{ route('medecin.ordonnances.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Informations de base -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">📋 Informations</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Patient -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Patient <span class="text-red-500">*</span>
                        </label>
                        <select name="patient_id" id="patient_id" required
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="">-- Sélectionner un patient --</option>
                            @if($patient)
                                <option value="{{ $patient->id }}" selected>
                                    {{ $patient->first_name }} {{ $patient->last_name }} - {{ $patient->phone ?? 'N/A' }}
                                </option>
                            @else
                                @foreach($patients as $p)
                                    <option value="{{ $p->id }}" {{ old('patient_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->first_name }} {{ $p->last_name }} - {{ $p->phone ?? 'N/A' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    @if($consultation)
                        <input type="hidden" name="consultation_id" value="{{ $consultation->id }}">
                    @endif

                    <!-- Date ordonnance -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_ordonnance" value="{{ old('date_ordonnance', date('Y-m-d')) }}" required
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>

                    <!-- Date expiration -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Date expiration
                        </label>
                        <input type="date" name="date_expiration" value="{{ old('date_expiration') }}"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Notes
                        </label>
                        <textarea name="notes" rows="2" 
                                  placeholder="Notes additionnelles..."
                                  class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Médicaments -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">💊 Contenu de l'ordonnance</h2>
                
                <!-- Ajouter un médicament -->
                <div class="bg-purple-50 dark:bg-purple-900/30 rounded-lg p-4 mb-6 border border-purple-200 dark:border-purple-800">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Médicament
                                </label>
                                <button type="button" onclick="ouvrirModalMedicament()" 
                                        class="text-xs px-2 py-1 bg-purple-600 text-white rounded hover:bg-purple-700 transition">
                                    + Nouveau
                                </button>
                            </div>
                            <select id="medicament-select" 
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                <option value="">-- Sélectionner --</option>
                                @foreach($medicaments as $med)
                                    <option value="{{ $med->id }}" data-nom="{{ $med->nom }}" data-forme="{{ $med->forme }}" data-dosage="{{ $med->dosage ?? '' }}">
                                        {{ $med->nom_complet }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Dosage
                            </label>
                            <input type="text" id="dosage-input" placeholder="5ml 3fois par jour"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Durée
                            </label>
                            <input type="text" id="duree-input" placeholder="10jours"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                &nbsp;
                            </label>
                            <button type="button" onclick="ajouterMedicament()" 
                                    class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-semibold text-sm">
                                + Ajouter
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Liste des médicaments -->
                <div id="medicaments-list" class="space-y-3">
                    <!-- Les médicaments seront ajoutés ici dynamiquement -->
                </div>

                <div id="empty-message" class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p>Aucun médicament ajouté</p>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('medecin.ordonnances.index') }}" 
                   class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-8 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition transform hover:scale-105 font-semibold">
                    💾 Enregistrer l'Ordonnance
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal pour créer un nouveau médicament -->
<div id="modal-medicament" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Nouveau Médicament</h3>
            <button onclick="fermerModalMedicament()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="form-nouveau-medicament" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nom du médicament <span class="text-red-500">*</span>
                </label>
                <input type="text" id="medicament-nom" required
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                       placeholder="Ex: Paracétamol">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Forme
                </label>
                <input type="text" id="medicament-forme"
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                       placeholder="Ex: comprimé, sirop, gélule">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Dosage
                </label>
                <input type="text" id="medicament-dosage"
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                       placeholder="Ex: 500mg, 5ml">
            </div>
            
            <div class="flex items-center justify-end space-x-3 pt-4">
                <button type="button" onclick="fermerModalMedicament()" 
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    Annuler
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    Créer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let medicamentIndex = 0;

// Remplir automatiquement le dosage quand un médicament est sélectionné
document.getElementById('medicament-select').addEventListener('change', function() {
    const select = this;
    const dosageInput = document.getElementById('dosage-input');
    const selectedOption = select.options[select.selectedIndex];
    
    if (select.value && selectedOption.dataset.dosage) {
        dosageInput.value = selectedOption.dataset.dosage;
    } else if (!select.value) {
        // Si aucun médicament n'est sélectionné, vider le champ dosage
        dosageInput.value = '';
    }
});

function ajouterMedicament() {
    const select = document.getElementById('medicament-select');
    const dosageInput = document.getElementById('dosage-input');
    const dureeInput = document.getElementById('duree-input');
    
    const selectedOption = select.options[select.selectedIndex];
    const medicamentId = select.value;
    const medicamentNom = medicamentId ? selectedOption.dataset.nom + ' - ' + selectedOption.dataset.forme : '';
    const dosage = dosageInput.value;
    const duree = dureeInput.value;
    
    if (!medicamentNom && !dosage && !duree) {
        alert('Veuillez remplir au moins le nom du médicament');
        return;
    }
    
    const listContainer = document.getElementById('medicaments-list');
    const emptyMessage = document.getElementById('empty-message');
    
    emptyMessage.style.display = 'none';
    
    const medicamentRow = document.createElement('div');
    medicamentRow.className = 'flex items-center gap-4 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg';
    medicamentRow.innerHTML = `
        <div class="flex-1">
            <input type="hidden" name="medicaments[${medicamentIndex}][medicament_id]" value="${medicamentId}">
            <input type="hidden" name="medicaments[${medicamentIndex}][medicament_nom]" value="${medicamentNom || dosage}">
            <input type="hidden" name="medicaments[${medicamentIndex}][dosage]" value="${dosage}">
            <input type="hidden" name="medicaments[${medicamentIndex}][duree]" value="${duree}">
            <div class="font-semibold text-gray-900 dark:text-white">${medicamentNom || dosage}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                ${dosage ? 'Dosage: ' + dosage : ''} 
                ${duree ? ' | Durée: ' + duree : ''}
            </div>
        </div>
        <button type="button" onclick="supprimerMedicament(this)" 
                class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm">
            🗑️ Supprimer
        </button>
    `;
    
    listContainer.appendChild(medicamentRow);
    
    // Réinitialiser les champs
    select.selectedIndex = 0;
    dosageInput.value = '';
    dureeInput.value = '';
    
    medicamentIndex++;
}

function supprimerMedicament(button) {
    const row = button.parentElement;
    row.remove();
    
    const listContainer = document.getElementById('medicaments-list');
    const emptyMessage = document.getElementById('empty-message');
    
    if (listContainer.children.length === 0) {
        emptyMessage.style.display = 'block';
    }
}

function ouvrirModalMedicament() {
    document.getElementById('modal-medicament').classList.remove('hidden');
}

function fermerModalMedicament() {
    document.getElementById('modal-medicament').classList.add('hidden');
    document.getElementById('form-nouveau-medicament').reset();
}

// Gérer la soumission du formulaire de nouveau médicament
document.getElementById('form-nouveau-medicament').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const nom = document.getElementById('medicament-nom').value;
    const forme = document.getElementById('medicament-forme').value;
    const dosage = document.getElementById('medicament-dosage').value;
    
    if (!nom.trim()) {
        alert('Le nom du médicament est requis');
        return;
    }
    
    try {
        const response = await fetch('{{ route("medecin.ordonnances.medicament.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                nom: nom,
                forme: forme || null,
                dosage: dosage || null
            })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            // Ajouter le nouveau médicament au select
            const select = document.getElementById('medicament-select');
            const option = document.createElement('option');
            option.value = data.medicament.id;
            option.dataset.nom = data.medicament.nom;
            option.dataset.forme = data.medicament.forme || '';
            option.dataset.dosage = data.medicament.dosage || '';
            
            let texte = data.medicament.nom;
            if (data.medicament.forme) {
                texte += ' - ' + data.medicament.forme;
            }
            option.textContent = texte;
            
            select.appendChild(option);
            select.value = data.medicament.id;
            
            // Remplir automatiquement le dosage si disponible
            const dosageInput = document.getElementById('dosage-input');
            if (data.medicament.dosage) {
                dosageInput.value = data.medicament.dosage;
            }
            
            // Fermer la modal
            fermerModalMedicament();
        } else {
            alert(data.message || 'Erreur lors de la création du médicament');
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur lors de la création du médicament');
    }
});
</script>
@endsection

