@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- En-tête -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-8 h-8 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                            </path>
                        </svg>
                        Détails de la chambre
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">{{ $chambre->nom_complet }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('lits.create', ['chambre_id' => $chambre->id]) }}"
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>Ajouter un lit
                    </a>
                    <a href="{{ route('chambres.edit', $chambre->id) }}"
                        class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i>Modifier
                    </a>
                    <a href="{{ route('chambres.index') }}"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Informations principales -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Informations de la chambre</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nom</label>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">{{ $chambre->nom }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                            <span
                                class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ ucfirst($chambre->type) }}
                            </span>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Étage</label>
                            <p class="mt-1 text-gray-900 dark:text-white">{{ $chambre->etage ?? 'Non spécifié' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tarif
                                journalier</label>
                            <p class="mt-1 text-gray-900 dark:text-white">
                                @if($chambre->tarif_journalier)
                                {{ number_format($chambre->tarif_journalier, 2) }} MRU
                                @else
                                <span class="text-gray-500">Non défini</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Statut</label>
                            @if($chambre->statut === 'active')
                            <span
                                class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Active
                            </span>
                            @elseif($chambre->statut === 'inactive')
                            <span
                                class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                Inactive
                            </span>
                            @else
                            <span
                                class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                Maintenance
                            </span>
                            @endif
                        </div>
                    </div>

                    @if($chambre->description)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                        <p class="mt-1 text-gray-900 dark:text-white">{{ $chambre->description }}</p>
                    </div>
                    @endif

                    @if($chambre->equipements)
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Équipements</label>
                        <p class="mt-1 text-gray-900 dark:text-white">{{ $chambre->equipements }}</p>
                    </div>
                    @endif
                </div>

                <!-- Statistiques -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mt-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Statistiques</h2>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['total_lits'] }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total lits</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['lits_libres']
                                }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Lits libres</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{
                                $stats['lits_occupes'] }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Lits occupés</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{
                                $stats['taux_occupation'] }}%</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Taux occupation</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions et statut -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Actions</h2>

                    <div class="space-y-3">
                        @if($chambre->statut === 'active')
                        <div class="p-3 bg-green-50 dark:bg-green-900 rounded-lg">
                            <p class="text-sm text-green-800 dark:text-green-200">
                                <i class="fas fa-check-circle mr-2"></i>Cette chambre est active et disponible
                            </p>
                        </div>
                        @elseif($chambre->statut === 'inactive')
                        <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded-lg">
                            <p class="text-sm text-gray-800 dark:text-gray-200">
                                <i class="fas fa-pause-circle mr-2"></i>Cette chambre est inactive
                            </p>
                        </div>
                        @else
                        <div class="p-3 bg-red-50 dark:bg-red-900 rounded-lg">
                            <p class="text-sm text-red-800 dark:text-red-200">
                                <i class="fas fa-tools mr-2"></i>Cette chambre est en maintenance
                            </p>
                        </div>
                        @endif

                        <a href="{{ route('lits.index', ['chambre_id' => $chambre->id]) }}"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200 text-center block">
                            <i class="fas fa-bed mr-2"></i>Voir tous les lits
                        </a>

                        <form action="{{ route('chambres.destroy', $chambre->id) }}" method="POST" class="inline w-full"
                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette chambre ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">
                                <i class="fas fa-trash mr-2"></i>Supprimer la chambre
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des lits -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mt-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Lits de la chambre</h2>
                <a href="{{ route('lits.create', ['chambre_id' => $chambre->id]) }}"
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>Ajouter un lit
                </a>
            </div>

            @if($chambre->lits->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Numéro</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Type</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Statut</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($chambre->lits as $lit)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $lit->numero }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ ucfirst($lit->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($lit->statut === 'libre')
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Libre
                                </span>
                                @elseif($lit->statut === 'occupe')
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    Occupé
                                </span>
                                @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                    Maintenance
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('lits.show', $lit->id) }}"
                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('lits.edit', $lit->id) }}"
                                        class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 21V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16m16 0h2m-2 0h-5m-9 0H2m2 0h5"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Aucun lit</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">Cette chambre n'a pas encore de lits</p>
                <a href="{{ route('lits.create', ['chambre_id' => $chambre->id]) }}"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                    Ajouter le premier lit
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
