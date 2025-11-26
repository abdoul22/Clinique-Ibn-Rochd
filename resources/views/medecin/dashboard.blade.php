@extends('layouts.app')
@section('title', "Dashboard Médecin")

@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <!-- En-tête avec informations du médecin -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-6 mb-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Bienvenue Dr. {{ $medecin->nom_complet_avec_prenom }}</h1>
                <p class="text-blue-100">{{ $medecin->specialite ?? 'Médecin' }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-blue-100">{{ \Carbon\Carbon::now()->isoFormat('dddd D MMMM YYYY') }}</p>
                <p class="text-lg font-semibold">{{ \Carbon\Carbon::now()->format('H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Aujourd'hui</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $stats['consultations_aujourdhui'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Consultations</p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Ce mois</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $stats['consultations_mois'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Consultations</p>
                </div>
                <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Ordonnances</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $stats['ordonnances_mois'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Ce mois</p>
                </div>
                <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-full">
                    <svg class="w-8 h-8 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Patients</p>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $stats['patients_total'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total suivis</p>
                </div>
                <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded-full">
                    <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        <!-- Nouvelle consultation -->
        <a href="{{ route('medecin.consultations.create') }}"
            class="bg-blue-600 text-white rounded-2xl p-6 shadow-lg hover:bg-blue-700 transition transform hover:scale-105">
            <div class="flex items-center mb-2">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <h2 class="text-xl font-semibold">Nouvelle Consultation</h2>
            </div>
            <p class="text-sm text-blue-100">Créer un rapport de consultation pour un patient</p>
        </a>

        <!-- Nouvelle ordonnance -->
        <a href="{{ route('medecin.ordonnances.create') }}"
            class="bg-purple-600 text-white rounded-2xl p-6 shadow-lg hover:bg-purple-700 transition transform hover:scale-105">
            <div class="flex items-center mb-2">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h2 class="text-xl font-semibold">Nouvelle Ordonnance</h2>
            </div>
            <p class="text-sm text-purple-100">Prescrire des médicaments à un patient</p>
        </a>

        <!-- Mes consultations -->
        <a href="{{ route('medecin.consultations.index') }}"
            class="bg-green-600 text-white rounded-2xl p-6 shadow-lg hover:bg-green-700 transition transform hover:scale-105">
            <div class="flex items-center mb-2">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h2 class="text-xl font-semibold">Mes Consultations</h2>
            </div>
            <p class="text-sm text-green-100">Historique de vos consultations médicales</p>
        </a>

        <!-- Mes ordonnances -->
        <a href="{{ route('medecin.ordonnances.index') }}"
            class="bg-indigo-600 text-white rounded-2xl p-6 shadow-lg hover:bg-indigo-700 transition transform hover:scale-105">
            <div class="flex items-center mb-2">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h2 class="text-xl font-semibold">Mes Ordonnances</h2>
            </div>
            <p class="text-sm text-indigo-100">Liste de toutes vos ordonnances</p>
        </a>

        <!-- Liste des patients -->
        <a href="{{ route('medecin.patients.index') }}"
            class="bg-yellow-600 text-white rounded-2xl p-6 shadow-lg hover:bg-yellow-700 transition transform hover:scale-105">
            <div class="flex items-center mb-2">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h2 class="text-xl font-semibold">Patients</h2>
            </div>
            <p class="text-sm text-yellow-100">Rechercher et consulter les dossiers patients</p>
        </a>
    </div>

    <!-- Dernières consultations -->
    @if($dernieresConsultations->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Dernières Consultations</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Motif</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($dernieresConsultations as $consultation)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            {{ $consultation->date_consultation->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-300">
                            {{ $consultation->patient->first_name }} {{ $consultation->patient->last_name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ Str::limit($consultation->motif, 50) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('medecin.consultations.show', $consultation->id) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">Voir</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection

