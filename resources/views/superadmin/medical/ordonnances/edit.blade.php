@extends('layouts.app')
@section('title', 'Modifier Ordonnance')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-4 sm:p-8">
    <div class="max-w-5xl mx-auto">
        <!-- En-tête -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">✏️ Modifier Ordonnance</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Ordonnance {{ $ordonnance->reference }}</p>
                </div>
                <a href="{{ route('superadmin.medical.ordonnances.show', $ordonnance->id) }}" 
                   class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    ← Retour
                </a>
            </div>
        </div>

        <!-- Formulaire -->
        <form action="{{ route('superadmin.medical.ordonnances.update', $ordonnance->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Informations de base -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">📋 Informations</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Patient (lecture seule) -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Patient
                        </label>
                        <input type="text" 
                               value="{{ $ordonnance->patient->first_name }} {{ $ordonnance->patient->last_name }}"
                               disabled
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Médecin -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Médecin <span class="text-red-500">*</span>
                        </label>
                        <select name="medecin_id" required
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            @foreach($medecins as $medecin)
                                <option value="{{ $medecin->id }}" {{ $ordonnance->medecin_id == $medecin->id ? 'selected' : '' }}>
                                    {{ $medecin->fonction }} {{ $medecin->nom }} {{ $medecin->prenom }} - {{ $medecin->specialite }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date ordonnance -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_ordonnance" value="{{ $ordonnance->date_ordonnance ? $ordonnance->date_ordonnance->format('Y-m-d') : '' }}" required
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>

                    <!-- Date expiration -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Date expiration
                        </label>
                        <input type="date" name="date_expiration" value="{{ $ordonnance->date_expiration ? $ordonnance->date_expiration->format('Y-m-d') : '' }}"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Notes
                        </label>
                        <textarea name="notes" rows="2" 
                                  class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">{{ $ordonnance->notes }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Médicaments -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">💊 Médicaments</h2>
                    <button type="button" onclick="ajouterMedicament()" 
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        + Ajouter un médicament
                    </button>
                </div>
                
                <div id="medicaments-container" class="space-y-4">
                    <!-- Les médicaments existants et nouveaux seront ajoutés ici -->
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('superadmin.medical.ordonnances.show', $ordonnance->id) }}" 
                   class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-8 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 transition transform hover:scale-105 font-semibold">
                    💾 Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let medicamentIndex = {{ $ordonnance->medicaments->count() }};

// Charger les médicaments existants
@foreach($ordonnance->medicaments as $index => $med)
ajouterMedicamentExistant(
    {{ $index }},
    "{{ addslashes($med->medicament_nom) }}",
    "{{ addslashes($med->dosage ?? '') }}",
    "{{ addslashes($med->duree ?? '') }}",
    "{{ addslashes($med->note ?? '') }}"
);
@endforeach

function ajouterMedicamentExistant(index, nom, dosage, duree, note) {
    const container = document.getElementById('medicaments-container');
    const div = document.createElement('div');
    div.className = 'bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 border border-purple-200 dark:border-purple-700';
    div.id = `medicament-${index}`;
    
    div.innerHTML = `
        <div class="flex items-start justify-between mb-3">
            <h3 class="font-semibold text-gray-900 dark:text-white">Médicament ${index + 1}</h3>
            <button type="button" onclick="supprimerMedicament(${index})" 
                    class="text-red-600 hover:text-red-800 dark:text-red-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nom du médicament <span class="text-red-500">*</span>
                </label>
                <input type="text" name="medicaments[${index}][medicament_nom]" value="${nom}" required
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dosage</label>
                <input type="text" name="medicaments[${index}][dosage]" value="${dosage}"
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Durée</label>
                <input type="text" name="medicaments[${index}][duree]" value="${duree}"
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Note</label>
                <input type="text" name="medicaments[${index}][note]" value="${note}"
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
        </div>
    `;
    
    container.appendChild(div);
}

function ajouterMedicament() {
    const container = document.getElementById('medicaments-container');
    const div = document.createElement('div');
    div.className = 'bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 border border-purple-200 dark:border-purple-700';
    div.id = `medicament-${medicamentIndex}`;
    
    div.innerHTML = `
        <div class="flex items-start justify-between mb-3">
            <h3 class="font-semibold text-gray-900 dark:text-white">Médicament ${medicamentIndex + 1}</h3>
            <button type="button" onclick="supprimerMedicament(${medicamentIndex})" 
                    class="text-red-600 hover:text-red-800 dark:text-red-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nom du médicament <span class="text-red-500">*</span>
                </label>
                <input type="text" name="medicaments[${medicamentIndex}][medicament_nom]" required
                       placeholder="Ex: Paracétamol"
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dosage</label>
                <input type="text" name="medicaments[${medicamentIndex}][dosage]"
                       placeholder="Ex: 500mg, 3x/jour"
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Durée</label>
                <input type="text" name="medicaments[${medicamentIndex}][duree]"
                       placeholder="Ex: 7 jours"
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Note</label>
                <input type="text" name="medicaments[${medicamentIndex}][note]"
                       placeholder="Instructions spéciales..."
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
        </div>
    `;
    
    container.appendChild(div);
    medicamentIndex++;
}

function supprimerMedicament(index) {
    const element = document.getElementById(`medicament-${index}`);
    if (element) {
        element.remove();
    }
}
</script>
@endsection

