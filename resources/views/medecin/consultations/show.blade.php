@extends('layouts.app')
@section('title', 'Détails Consultation')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-4 sm:p-8">
    <div class="max-w-5xl mx-auto">
        <!-- En-tête -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">📋 Consultation</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $consultation->date_consultation->format('d/m/Y') }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('medecin.consultations.index') }}" 
                       class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        ← Retour
                    </a>
                    <a href="{{ route('medecin.consultations.edit', $consultation->id) }}" 
                       class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                        ✏️ Modifier
                    </a>
                    <a href="{{ route('medecin.consultations.print', $consultation->id) }}" 
                       target="_blank"
                       class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        🖨️ Imprimer PDF
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

            @if($consultation->ras)
            <div class="mb-4">
                <h3 class="font-semibold text-gray-700 dark:text-gray-300 mb-2">RAS</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ $consultation->ras }}</p>
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
                Aucune ordonnance associée à cette consultation
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

