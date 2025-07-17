@extends('layouts.app')
@section('title', 'Dossiers Médicaux')

@section('content')
<div class="w-full px-0 sm:px-2 lg:px-4 py-4 sm:py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 gap-4">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-200">Dossiers Médicaux</h1>
        <div class="flex space-x-2 w-full sm:w-auto">
            <a href="{{ route('dossiers.synchroniser') }}"
                class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg text-base w-full sm:w-auto text-center"
                onclick="return confirm('Voulez-vous synchroniser tous les dossiers ?')">
                <i class="fas fa-sync-alt mr-2"></i>Synchroniser
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
        <form method="GET" action="{{ route('dossiers.index') }}"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label class="block text-base font-medium text-gray-700 dark:text-gray-300 mb-2">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Nom, téléphone, N° dossier..."
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-3 text-base">
            </div>

            <div>
                <label class="block text-base font-medium text-gray-700 dark:text-gray-300 mb-2">Statut</label>
                <select name="statut"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-3 text-base">
                    <option value="">Tous les statuts</option>
                    <option value="actif" {{ request('statut')=='actif' ? 'selected' : '' }}>Actif</option>
                    <option value="inactif" {{ request('statut')=='inactif' ? 'selected' : '' }}>Inactif</option>
                    <option value="archive" {{ request('statut')=='archive' ? 'selected' : '' }}>Archivé</option>
                </select>
            </div>

            <div>
                <label class="block text-base font-medium text-gray-700 dark:text-gray-300 mb-2">Date début</label>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-3 text-base">
            </div>

            <div>
                <label class="block text-base font-medium text-gray-700 dark:text-gray-300 mb-2">Date fin</label>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-3 py-3 text-base">
            </div>

            <div class="flex flex-col sm:flex-row items-end gap-2">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-base">
                    <i class="fas fa-search mr-2"></i>Filtrer
                </button>
                <a href="{{ route('dossiers.index') }}"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-6 rounded-lg text-base text-center">
                    <i class="fas fa-times mr-2"></i>Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4 sm:mb-6">
        <div class="card text-sm flex flex-col items-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <span class="font-bold text-gray-700 dark:text-gray-300 mb-1 text-center">Total Dossiers</span>
            <span class="text-blue-700 dark:text-blue-400 text-xl font-bold">{{ $dossiers->total() }}</span>
        </div>
        <div class="card text-sm flex flex-col items-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <span class="font-bold text-gray-700 dark:text-gray-300 mb-1 text-center">Dossiers Actifs</span>
            <span class="text-green-700 dark:text-green-400 text-xl font-bold">{{ $dossiers->where('statut',
                'actif')->count() }}</span>
        </div>
        <div class="card text-sm flex flex-col items-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <span class="font-bold text-gray-700 dark:text-gray-300 mb-1 text-center">Visites ce mois</span>
            <span class="text-purple-700 dark:text-purple-400 text-xl font-bold">{{ $dossiers->where('derniere_visite',
                '>=', now()->startOfMonth())->count() }}</span>
        </div>
        <div class="card text-sm flex flex-col items-center p-4 bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <span class="font-bold text-gray-700 dark:text-gray-300 mb-1 text-center">Total Dépenses</span>
            <span class="text-indigo-700 dark:text-indigo-400 text-xl font-bold">{{
                number_format($dossiers->sum('total_depense'), 0, ',', ' ') }} MRU</span>
        </div>
    </div>

    <!-- Liste des dossiers -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Liste des Dossiers Médicaux</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Patient
                        </th>
                        <th
                            class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            N° Dossier
                        </th>
                        <th
                            class="hidden lg:table-cell px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Visites
                        </th>
                        <th
                            class="hidden md:table-cell px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Dernière Visite
                        </th>
                        <th
                            class="hidden lg:table-cell px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Total Dépenses
                        </th>
                        <th
                            class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Statut
                        </th>
                        <th
                            class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($dossiers as $dossier)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 sm:h-10 sm:w-10">
                                    <div
                                        class="h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                        <span class="text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ substr($dossier->patient->first_name, 0, 1) }}{{
                                            substr($dossier->patient->last_name, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-3 sm:ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                        {{ $dossier->patient->first_name }} {{ $dossier->patient->last_name }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $dossier->patient->phone }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                {{ $dossier->numero_dossier }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                Créé le {{ $dossier->date_creation->format('d/m/Y') }}
                            </div>
                        </td>
                        <td class="hidden lg:table-cell px-3 sm:px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                {{ $dossier->nombre_visites }} visite(s)
                            </div>
                        </td>
                        <td class="hidden md:table-cell px-3 sm:px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-200">
                                {{ $dossier->derniere_visite ? $dossier->derniere_visite->format('d/m/Y') : 'Aucune' }}
                            </div>
                        </td>
                        <td class="hidden lg:table-cell px-3 sm:px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                {{ number_format($dossier->total_depense, 0, ',', ' ') }} MRU
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                            @switch($dossier->statut)
                            @case('actif')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                Actif
                            </span>
                            @break
                            @case('inactif')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                Inactif
                            </span>
                            @break
                            @case('archive')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                                Archivé
                            </span>
                            @break
                            @endswitch
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex flex-col sm:flex-row gap-2">
                                <a href="{{ route('dossiers.show', $dossier->id) }}"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1">
                                    <i class="fas fa-eye"></i> <span class="hidden sm:inline">Voir</span>
                                </a>
                                <a href="{{ route('dossiers.edit', $dossier->id) }}"
                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 p-1">
                                    <i class="fas fa-edit"></i> <span class="hidden sm:inline">Modifier</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 sm:px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            Aucun dossier médical trouvé.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $dossiers->links() }}
        </div>
    </div>
</div>
@endsection
