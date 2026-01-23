@extends('layouts.app')
@section('title', 'Détails Rapport Médical')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-4 sm:p-8">
    <div class="max-w-5xl mx-auto">
        <!-- En-tête -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">📋 Rapport Médical</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        {{ $consultation->created_at->format('d/m/Y') }} à {{ $consultation->created_at->format('H:i') }}
                    </p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('medecin.consultations.index') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Retour
                    </a>
                    <a href="{{ route('medecin.consultations.edit', $consultation->id) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 dark:bg-orange-500 dark:hover:bg-orange-600 text-white rounded-lg transition shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Modifier
                    </a>
                    <a href="{{ route('medecin.consultations.print', $consultation->id) }}" 
                       target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white rounded-lg transition shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Imprimer
                    </a>
                </div>
            </div>
        </div>

        <!-- Informations Patient -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">👤 Patient</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Nom complet</p>
                    <p class="font-semibold text-gray-900 dark:text-white">
                        {{ $consultation->patient->first_name }} {{ $consultation->patient->last_name }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Téléphone</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $consultation->patient->phone ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Âge</p>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $consultation->patient->age ?? 'N/A' }} ans</p>
                </div>
            </div>
        </div>

        <!-- Rapport Médical -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">📝 Rapport Médical</h2>
            
            @if($consultation->motif)
            <div class="mb-4">
                <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">Motif</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ $consultation->motif }}</p>
            </div>
            @endif

            @if($consultation->antecedents)
            <div class="mb-4">
                <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">Antécédents</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ $consultation->antecedents }}</p>
            </div>
            @endif

            @if($consultation->histoire_maladie)
            <div class="mb-4">
                <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">Histoire de la maladie</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ $consultation->histoire_maladie }}</p>
            </div>
            @endif

            @if($consultation->examen_clinique)
            <div class="mb-4">
                <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">Examen clinique</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ $consultation->examen_clinique }}</p>
            </div>
            @endif

            @if($consultation->conduite_tenir)
            <div class="mb-4">
                <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">Conduite à tenir</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ $consultation->conduite_tenir }}</p>
            </div>
            @endif

            @if($consultation->resume)
            <div class="mb-4">
                <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">Résumé</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ $consultation->resume }}</p>
            </div>
            @endif
        </div>

        <!-- Ordonnances liées -->
        @if($consultation->ordonnances->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">💊 Ordonnances</h2>
            <div class="space-y-3">
                @foreach($consultation->ordonnances as $ordonnance)
                <div class="flex items-center justify-between bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $ordonnance->reference }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $ordonnance->date_ordonnance->format('d/m/Y') }} - {{ $ordonnance->medicaments->count() }} médicament(s)
                        </p>
                    </div>
                    <a href="{{ route('medecin.ordonnances.show', $ordonnance->id) }}" 
                       class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm">
                        Voir
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
            <p class="text-center text-gray-500 dark:text-gray-400">
                Aucune ordonnance associée à ce rapport médical
            </p>
            <div class="text-center mt-4">
                <a href="{{ route('medecin.ordonnances.create', ['consultation_id' => $consultation->id]) }}" 
                   class="inline-block px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    + Créer une ordonnance
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

