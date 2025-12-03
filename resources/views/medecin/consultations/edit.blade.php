@extends('layouts.app')
@section('title', 'Modifier Rapport Médical')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-4 sm:p-8">
    <div class="max-w-5xl mx-auto">
        <!-- En-tête -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">✏️ Modifier Rapport Médical</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Mettre à jour le rapport médical</p>
                </div>
                <a href="{{ route('medecin.consultations.show', $consultation->id) }}" 
                   class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    ← Retour
                </a>
            </div>
        </div>

        <!-- Formulaire -->
        <form action="{{ route('medecin.consultations.update', $consultation->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Carte Patient -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">👤 Informations Patient</h2>
                
                <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg mb-6">
                    <p class="font-semibold text-gray-900 dark:text-white">
                        {{ $consultation->patient->first_name }} {{ $consultation->patient->last_name }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $consultation->patient->phone }} - {{ $consultation->patient->age ?? 'N/A' }} ans
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Date du rapport médical <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_consultation" 
                               value="{{ old('date_consultation', $consultation->date_consultation->format('Y-m-d')) }}" 
                               required
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('date_consultation')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Heure -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Heure
                        </label>
                        <input type="time" name="heure_consultation" 
                               value="{{ old('heure_consultation', $consultation->heure_consultation ? $consultation->heure_consultation->format('H:i') : '') }}"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Rapport Médical -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">📝 Rapport Médical</h2>
                
                <div class="space-y-6">
                    <!-- Motif -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Motif
                        </label>
                        <input type="text" name="motif" value="{{ old('motif', $consultation->motif) }}" 
                               placeholder="Ex: FIEVRE ET FRISSON"
                               class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- RAS -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            RAS (Rien À Signaler)
                        </label>
                        <textarea name="ras" rows="2" 
                                  class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('ras', $consultation->ras) }}</textarea>
                    </div>

                    <!-- Antécédents -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Antécédents
                        </label>
                        <textarea name="antecedents" rows="3" 
                                  class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('antecedents', $consultation->antecedents) }}</textarea>
                    </div>

                    <!-- Histoire de la maladie -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Histoire de la maladie
                        </label>
                        <textarea name="histoire_maladie" rows="4" 
                                  class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('histoire_maladie', $consultation->histoire_maladie) }}</textarea>
                    </div>

                    <!-- Examen clinique -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Examen clinique
                        </label>
                        <textarea name="examen_clinique" rows="4" 
                                  class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('examen_clinique', $consultation->examen_clinique) }}</textarea>
                    </div>

                    <!-- Conduite à tenir -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Conduite à tenir
                        </label>
                        <textarea name="conduite_tenir" rows="4" 
                                  class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('conduite_tenir', $consultation->conduite_tenir) }}</textarea>
                    </div>

                    <!-- Résumé -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Résumé
                        </label>
                        <textarea name="resume" rows="3" 
                                  class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('resume', $consultation->resume) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('medecin.consultations.show', $consultation->id) }}" 
                   class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    Annuler
                </a>
                <button type="submit" 
                        class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition transform hover:scale-105 font-semibold">
                    💾 Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

