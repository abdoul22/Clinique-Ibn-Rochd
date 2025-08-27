@extends('layouts.app')
@section('title', 'Liste des Médecins')

@section('content')

<!-- Header avec gradient et titre -->
<div class="gradient-header mb-8">
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="mb-4 md:mb-0">
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                    <i class="fas fa-user-md mr-3"></i>Gestion des Médecins
                </h1>
                <p class="text-blue-100 text-lg">Gérez vos médecins et leurs spécialités</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route(auth()->user()->role->name . '.medecins.create') }}"
                    class="gradient-button flex items-center justify-center px-6 py-3 rounded-lg text-white font-semibold transition-all duration-300 shadow-lg hover:shadow-xl">
                    <i class="fas fa-plus mr-2"></i>Nouveau Médecin
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Section des filtres -->
<div class="container mx-auto px-4 mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-filter mr-2 text-blue-500"></i>Filtres de recherche
            </h2>

            <form method="GET" action="{{ route(auth()->user()->role->name . '.medecins.index') }}" class="space-y-4">
                <!-- Première ligne de filtres -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Recherche générale -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-search mr-1"></i>Recherche générale
                        </label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Nom, spécialité, téléphone..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                    </div>

                    <!-- Filtre par nom -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-user mr-1"></i>Nom du médecin
                        </label>
                        <input type="text" name="name_filter" value="{{ request('name_filter') }}"
                            placeholder="Nom ou prénom..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                    </div>

                    <!-- Filtre par spécialité -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-stethoscope mr-1"></i>Spécialité
                        </label>
                        <input type="text" name="specialite_filter" value="{{ request('specialite_filter') }}"
                            placeholder="Spécialité..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                    </div>

                    <!-- Filtre par téléphone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-phone mr-1"></i>Numéro de téléphone
                        </label>
                        <input type="text" name="phone_filter" value="{{ request('phone_filter') }}"
                            placeholder="Numéro de téléphone..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                    </div>
                </div>

                <!-- Deuxième ligne de filtres -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Filtre par email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-envelope mr-1"></i>Email
                        </label>
                        <input type="email" name="email_filter" value="{{ request('email_filter') }}"
                            placeholder="Adresse email..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                    </div>

                    <!-- Filtre par statut -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-toggle-on mr-1"></i>Statut
                        </label>
                        <select name="status_filter"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                            <option value="">Tous les statuts</option>
                            <option value="actif" {{ request('status_filter')=='actif' ? 'selected' : '' }}>Actif
                            </option>
                            <option value="inactif" {{ request('status_filter')=='inactif' ? 'selected' : '' }}>Inactif
                            </option>
                        </select>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="flex items-end gap-3">
                        <button type="submit"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-search mr-2"></i>Filtrer
                        </button>
                        <a href="{{ route(auth()->user()->role->name . '.medecins.index') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-times mr-2"></i>Réinitialiser
                        </a>
                    </div>
                </div>
            </form>

            <!-- Affichage des filtres actifs -->
            @if(request('search') || request('name_filter') || request('specialite_filter') || request('phone_filter')
            || request('email_filter') || request('status_filter'))
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Filtres actifs :</h3>
                <div class="flex flex-wrap gap-2">
                    @if(request('search'))
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                        Recherche: {{ request('search') }}
                    </span>
                    @endif
                    @if(request('name_filter'))
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                        Nom: {{ request('name_filter') }}
                    </span>
                    @endif
                    @if(request('specialite_filter'))
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                        Spécialité: {{ request('specialite_filter') }}
                    </span>
                    @endif
                    @if(request('phone_filter'))
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200">
                        Téléphone: {{ request('phone_filter') }}
                    </span>
                    @endif
                    @if(request('email_filter'))
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                        Email: {{ request('email_filter') }}
                    </span>
                    @endif
                    @if(request('status_filter'))
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200">
                        Statut: {{ request('status_filter') }}
                    </span>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Section des résultats -->
<div class="container mx-auto px-4">
    @if($medecins->count() > 0)
    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            ID</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Médecin</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Fonction</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Spécialité</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Contact</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Statut</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($medecins as $medecin)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            #{{ $medecin->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div
                                        class="h-10 w-10 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                                        <i class="fas fa-user-md text-green-600 dark:text-green-400"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $medecin->nom_complet }} {{ $medecin->prenom }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        ID: {{ $medecin->id }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <i class="fas fa-user-tie text-blue-400 mr-2"></i>
                                <span class="text-sm text-gray-900 dark:text-white">{{ $medecin->fonction_complet
                                    }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <i class="fas fa-stethoscope text-gray-400 mr-2"></i>
                                <span class="text-sm text-gray-900 dark:text-white">{{ $medecin->specialite }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="space-y-1">
                                @if($medecin->telephone)
                                <div class="flex items-center text-sm text-gray-900 dark:text-white">
                                    <i class="fas fa-phone text-gray-400 mr-2"></i>
                                    {{ $medecin->telephone }}
                                </div>
                                @endif
                                @if($medecin->email)
                                <div class="flex items-center text-sm text-gray-900 dark:text-white">
                                    <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                    {{ $medecin->email }}
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $medecin->statut == 'actif' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                <i
                                    class="fas {{ $medecin->statut == 'actif' ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                {{ ucfirst($medecin->statut ?? 'actif') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route(auth()->user()->role->name . '.medecins.show', $medecin->id) }}"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1 transition-colors duration-200"
                                    title="Voir les détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route(auth()->user()->role->name . '.medecins.edit', $medecin->id) }}"
                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 p-1 transition-colors duration-200"
                                    title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('medecins.stats', $medecin->id) }}"
                                    class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 p-1 transition-colors duration-200"
                                    title="Statistiques">
                                    <i class="fas fa-chart-bar"></i>
                                </a>
                                <form
                                    action="{{ route(auth()->user()->role->name . '.medecins.destroy', $medecin->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce médecin ?')"
                                    class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1 transition-colors duration-200"
                                        title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $medecins->links() }}
    </div>

    @else
    <!-- État vide -->
    <div class="text-center py-12">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
            <i class="fas fa-user-md text-gray-400 text-xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Aucun médecin trouvé</h3>
        <p class="text-gray-500 dark:text-gray-400 mb-6">
            @if(request('search') || request('name_filter') || request('specialite_filter') || request('phone_filter')
            || request('email_filter') || request('status_filter'))
            Aucun médecin ne correspond aux critères de recherche.
            @else
            Aucun médecin n'a été ajouté pour le moment.
            @endif
        </p>
        <a href="{{ route(auth()->user()->role->name . '.medecins.create') }}"
            class="gradient-button inline-flex items-center px-6 py-3 rounded-lg text-white font-semibold transition-all duration-300 shadow-lg hover:shadow-xl">
            <i class="fas fa-plus mr-2"></i>Ajouter le premier médecin
        </a>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
    // Gestion des filtres dynamiques
    document.addEventListener('DOMContentLoaded', function() {
        // Réinitialisation des filtres
        const resetButton = document.querySelector('a[href="{{ route(auth()->user()->role->name . ".medecins.index") }}"]');
        if (resetButton) {
            resetButton.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = this.href;
            });
        }
    });
</script>
@endpush