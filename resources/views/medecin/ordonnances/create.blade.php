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
                <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Médicament
                            </label>
                            <select id="medicament-select" 
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm">
                                <option value="">-- Sélectionner --</option>
                                @foreach($medicaments as $med)
                                    <option value="{{ $med->id }}" data-nom="{{ $med->nom }}" data-forme="{{ $med->forme }}">
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

<script>
let medicamentIndex = 0;

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
</script>
@endsection

