@extends('layouts.app')
@section('title', 'Dossiers Médicaux')

@section('content')
<div class="min-h-screen bg-gray-50/50 dark:bg-gray-900 p-4 sm:p-8">
    
    <!-- En-tête Moderne -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                🗂️ Dossiers Patients
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">
                Gestion centralisée et historique médical des patients.
            </p>
        </div>
        
        <div class="flex gap-3 w-full lg:w-auto">
            <a href="{{ auth()->user()->role?->name === 'admin' ? route('admin.dossiers.synchroniser') : route('dossiers.synchroniser') }}"
                class="group relative inline-flex items-center justify-center px-6 py-3 text-sm font-medium text-white transition-all duration-200 bg-indigo-600 border border-transparent rounded-full shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 w-full lg:w-auto"
                onclick="return confirm('Cette opération va scanner tous les patients pour créer leurs dossiers manquants. Continuer ?')">
                <span class="absolute inset-0 w-full h-full mt-1 ml-1 transition-all duration-200 ease-out bg-indigo-800 rounded-full group-hover:mt-0 group-hover:ml-0"></span>
                <span class="relative flex items-center">
                    <svg class="w-5 h-5 mr-2 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Synchroniser les dossiers
                </span>
            </a>
        </div>
    </div>

    <!-- Cartes de Statistiques Intelligentes -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Dossiers -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold tracking-wide text-gray-500 dark:text-gray-400 uppercase">Total Dossiers</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalDossiers }}</h3>
                </div>
                <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
            </div>
        </div>

        <!-- Dossiers Actifs -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold tracking-wide text-gray-500 dark:text-gray-400 uppercase">Patients Actifs</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $dossiersActifs }}</h3>
                </div>
                <div class="p-2 bg-green-50 dark:bg-green-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Visites Récentes -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold tracking-wide text-gray-500 dark:text-gray-400 uppercase">Visites (Ce mois)</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $visitesCeMois }}</h3>
                </div>
                <div class="p-2 bg-purple-50 dark:bg-purple-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Chiffre d'Affaires Global -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold tracking-wide text-gray-500 dark:text-gray-400 uppercase">Volume Global</p>
                    <h3 class="text-xl font-bold text-indigo-600 dark:text-indigo-400 mt-1 truncate" title="{{ number_format($volumeGlobal, 0, ',', ' ') }} MRU">
                        {{ number_format($volumeGlobal, 0, ',', ' ') }} <span class="text-xs font-medium text-gray-500">MRU</span>
                    </h3>
                </div>
                <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Barre de Recherche Intelligente -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-1 mb-6 border border-gray-200 dark:border-gray-700">
        <form method="GET" action="{{ auth()->user()->role?->name === 'admin' ? route('admin.dossiers.index') : route('dossiers.index') }}" class="flex flex-col lg:flex-row gap-2 p-2">
            
            <div class="relative flex-grow">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="block w-full pl-10 pr-3 py-2.5 border-none rounded-lg bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"
                    placeholder="Rechercher un patient (Nom, Tél, N° Dossier)...">
            </div>

            <div class="flex gap-2 overflow-x-auto pb-2 lg:pb-0">
                <select name="statut" class="py-2.5 pl-3 pr-10 text-sm border-none rounded-lg bg-gray-50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700">
                    <option value="">Tous les statuts</option>
                    <option value="actif" {{ request('statut')=='actif' ? 'selected' : '' }}>🟢 Actifs</option>
                    <option value="inactif" {{ request('statut')=='inactif' ? 'selected' : '' }}>🟠 Inactifs</option>
                    <option value="archive" {{ request('statut')=='archive' ? 'selected' : '' }}>⚫ Archivés</option>
                </select>

                <button type="submit" class="px-6 py-2.5 bg-gray-900 dark:bg-indigo-600 text-white font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-indigo-700 focus:ring-4 focus:ring-gray-300 dark:focus:ring-indigo-900 transition-all shadow-sm flex-shrink-0">
                    Rechercher
                </button>
                
                @if(request()->has('search') || request()->has('statut'))
                <a href="{{ auth()->user()->role?->name === 'admin' ? route('admin.dossiers.index') : route('dossiers.index') }}" 
                   class="px-4 py-2.5 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-all flex-shrink-0 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    Effacer
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tableau Intelligent -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-4">Patient / Dossier</th>
                        <th class="px-6 py-4">Fréquence</th>
                        <th class="px-6 py-4">Dernière Activité</th>
                        <th class="px-6 py-4 text-right">Total Facturé</th>
                        <th class="px-6 py-4 text-center">État</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($dossiers as $dossier)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                        
                        <!-- Patient Info -->
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900 dark:to-purple-900 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold text-lg border-2 border-white dark:border-gray-800 shadow-sm">
                                        {{ substr($dossier->patient->first_name, 0, 1) }}{{ substr($dossier->patient->last_name, 0, 1) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                        {{ $dossier->patient->first_name }} {{ $dossier->patient->last_name }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center mt-0.5">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        {{ $dossier->numero_dossier }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Fréquence Visites -->
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $dossier->nombre_visites }}</span>
                                <span class="ml-1 text-xs text-gray-500">visites</span>
                            </div>
                            <!-- Barre de progression visuelle -->
                            <div class="w-24 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full mt-1.5 overflow-hidden">
                                @php
                                    $width = min(100, ($dossier->nombre_visites / 20) * 100); // Max 20 visites pour barre pleine
                                    $color = $dossier->nombre_visites > 10 ? 'bg-green-500' : ($dossier->nombre_visites > 3 ? 'bg-blue-500' : 'bg-gray-400');
                                @endphp
                                <div class="h-full {{ $color }} rounded-full" style="width: {{ $width }}%"></div>
                            </div>
                        </td>

                        <!-- Dernière Activité (Intelligent) -->
                        <td class="px-6 py-4">
                            @if($dossier->derniere_visite)
                                <div class="text-sm text-gray-900 dark:text-white font-medium">
                                    {{ $dossier->derniere_visite->diffForHumans() }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ $dossier->derniere_visite->format('d M Y') }}
                                </div>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    Jamais
                                </span>
                            @endif
                        </td>

                        <!-- Total (Aligné droite pour chiffres) -->
                        <td class="px-6 py-4 text-right">
                            <div class="text-sm font-bold text-gray-900 dark:text-white font-mono">
                                {{ number_format($dossier->total_depense, 0, ',', ' ') }} <span class="text-xs text-gray-500 font-sans">MRU</span>
                            </div>
                        </td>

                        <!-- Statut -->
                        <td class="px-6 py-4 text-center">
                            @if($dossier->statut === 'actif')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 border border-green-200 dark:border-green-800">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                    Actif
                                </span>
                            @elseif($dossier->statut === 'inactif')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 border border-yellow-200 dark:border-yellow-800">
                                    <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1.5"></span>
                                    Inactif
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                    Archivé
                                </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-center">
                            <a href="{{ auth()->user()->role?->name === 'admin' ? route('admin.dossiers.show', $dossier->id) : route('dossiers.show', $dossier->id) }}" 
                               class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-indigo-600 bg-indigo-50 hover:bg-indigo-100 dark:text-indigo-300 dark:bg-indigo-900/20 dark:hover:bg-indigo-900/40 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                Consulter
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Aucun dossier trouvé</h3>
                                <p class="text-gray-500 dark:text-gray-400 mt-1 max-w-sm">
                                    Essayez de modifier vos filtres ou effectuez une synchronisation si vous pensez qu'il manque des dossiers.
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Moderne -->
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
            {{ $dossiers->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<style>
    /* Animation douce pour le spinner de synchronisation */
    @keyframes spin-slow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .animate-spin-slow {
        animation: spin-slow 3s linear infinite;
    }
</style>
@endsection