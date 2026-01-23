@extends('layouts.app')
@section('title', 'Détails Ordonnance')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-4 sm:p-8">
    <div class="max-w-5xl mx-auto">
        <!-- En-tête -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">💊 Ordonnance</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $ordonnance->reference }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('medecin.ordonnances.index') }}" 
                       class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        ← Retour
                    </a>
                    <a href="{{ route('medecin.ordonnances.print-page', $ordonnance->id) }}" 
                       target="_blank"
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        🖨️ Imprimer
                    </a>
                </div>
            </div>
        </div>

        <!-- Informations générales -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">📋 Informations</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Patient</p>
                    <p class="font-semibold text-gray-900 dark:text-white">
                        {{ $ordonnance->patient->first_name }} {{ $ordonnance->patient->last_name }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $ordonnance->patient->phone }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Date</p>
                    <p class="font-semibold text-gray-900 dark:text-white">
                        {{ $ordonnance->date_ordonnance->format('d/m/Y') }}
                    </p>
                    @if($ordonnance->date_expiration)
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Expire le {{ $ordonnance->date_expiration->format('d/m/Y') }}
                    </p>
                    @endif
                </div>
            </div>

            @if($ordonnance->notes)
            <div class="mt-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">Notes</p>
                <p class="text-gray-900 dark:text-white">{{ $ordonnance->notes }}</p>
            </div>
            @endif

            @if($ordonnance->consultation)
            <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400">Rapport médical lié</p>
                <a href="{{ route('medecin.consultations.show', $ordonnance->consultation->id) }}" 
                   class="text-blue-600 hover:text-blue-800 dark:text-blue-400 font-semibold">
                    Voir le rapport médical du {{ $ordonnance->consultation->date_consultation->format('d/m/Y') }}
                </a>
            </div>
            @endif
        </div>

        <!-- Médicaments -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">💊 Médicaments prescrits</h2>
            <div class="space-y-4">
                @foreach($ordonnance->medicaments as $index => $med)
                <div class="border-l-4 border-purple-600 bg-purple-50 dark:bg-purple-900/30 dark:border-purple-700 p-4 rounded-r-lg">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <p class="font-bold text-lg text-gray-900 dark:text-white">
                                {{ $index + 1 }}. {{ $med->medicament_nom }}
                            </p>
                            @if($med->dosage)
                            <p class="text-gray-700 dark:text-gray-200 mt-1">
                                <span class="font-semibold text-gray-800 dark:text-gray-100">Dosage:</span> {{ $med->dosage }}
                            </p>
                            @endif
                            @if($med->duree)
                            <p class="text-gray-700 dark:text-gray-200">
                                <span class="font-semibold text-gray-800 dark:text-gray-100">Durée:</span> {{ $med->duree }}
                            </p>
                            @endif
                            @if($med->note)
                            <p class="text-gray-600 dark:text-gray-300 text-sm mt-2 italic">
                                {{ $med->note }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

