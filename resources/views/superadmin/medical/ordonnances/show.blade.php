@extends('layouts.app')
@section('title', 'Détails Ordonnance')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-4 sm:p-8">
    <div class="max-w-5xl mx-auto">
        <!-- En-tête -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">📋 Ordonnance {{ $ordonnance->reference }}</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        {{ $ordonnance->created_at->format('d/m/Y') }} à {{ $ordonnance->created_at->format('H:i') }}
                    </p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('superadmin.medical.ordonnances.index') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Retour
                    </a>
                    <a href="{{ route('superadmin.medical.ordonnances.edit', $ordonnance->id) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Modifier
                    </a>
                    <a href="{{ route('superadmin.medical.ordonnances.print', $ordonnance->id) }}" 
                       target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Imprimer
                    </a>
                </div>
            </div>
        </div>

        <!-- Informations Patient & Médecin -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Patient -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">👤 Patient</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Nom complet</p>
                        <p class="font-semibold text-gray-900 dark:text-white">
                            {{ $ordonnance->patient->first_name }} {{ $ordonnance->patient->last_name }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Téléphone</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $ordonnance->patient->phone ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Médecin -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">👨‍⚕️ Médecin</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Nom</p>
                        <p class="font-semibold text-gray-900 dark:text-white">
                            {{ $ordonnance->medecin->fonction ?? '' }} {{ $ordonnance->medecin->nom ?? '' }} {{ $ordonnance->medecin->prenom ?? '' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Spécialité</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $ordonnance->medecin->specialite ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Médicaments -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">💊 Médicaments Prescrits</h2>
            
            @if($ordonnance->medicaments->count() > 0)
                <div class="space-y-4">
                    @foreach($ordonnance->medicaments as $index => $med)
                        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 border-l-4 border-purple-500 dark:border-purple-400">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-purple-500 text-white font-bold text-sm">
                                        {{ $index + 1 }}
                                    </span>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $med->medicament_nom }}
                                    </h3>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3 ml-11">
                                @if($med->dosage)
                                    <div>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Dosage</p>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $med->dosage }}</p>
                                    </div>
                                @endif
                                
                                @if($med->duree)
                                    <div>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Durée</p>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $med->duree }}</p>
                                    </div>
                                @endif
                                
                                @if($med->note)
                                    <div class="md:col-span-3">
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Note</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $med->note }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400">Aucun médicament prescrit.</p>
            @endif
        </div>

        <!-- Notes additionnelles -->
        @if($ordonnance->notes)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">📝 Notes</h2>
                <p class="text-gray-600 dark:text-gray-400">{{ $ordonnance->notes }}</p>
            </div>
        @endif
    </div>
</div>
@endsection

