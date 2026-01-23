@extends('layouts.app')
@section('title', 'Liste des Patients')

@section('content')

<!-- Header avec gradient et titre -->
<div class="gradient-header mb-8">
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="mb-4 md:mb-0">
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                    <i class="fas fa-users mr-3"></i>Gestion des Patients
                </h1>
                <p class="text-blue-100 text-lg">Gérez vos patients et leurs informations médicales</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route(auth()->user()->role->name . '.patients.create') }}"
                    class="gradient-button flex items-center justify-center px-6 py-3 rounded-lg text-white font-semibold transition-all duration-300 shadow-lg hover:shadow-xl">
                    <i class="fas fa-plus mr-2"></i>Nouveau Patient
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

            <form method="GET" action="{{ route(auth()->user()->role->name . '.patients.index') }}" class="space-y-4">
                <!-- Première ligne de filtres -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Recherche générale -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-search mr-1"></i>Recherche générale
                        </label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Nom, téléphone, adresse..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                    </div>

                    <!-- Filtre par nom -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-user mr-1"></i>Nom du patient
                        </label>
                        <input type="text" name="name_filter" value="{{ request('name_filter') }}"
                            placeholder="Nom ou prénom..."
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

                    <!-- Filtre par sexe -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-venus-mars mr-1"></i>Sexe
                        </label>
                        <select name="gender_filter"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                            <option value="">Tous les sexes</option>
                            <option value="Homme" {{ request('gender_filter')=='Homme' ? 'selected' : '' }}>Homme
                            </option>
                            <option value="Femme" {{ request('gender_filter')=='Femme' ? 'selected' : '' }}>Femme
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Deuxième ligne de filtres -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Filtre par âge -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-birthday-cake mr-1"></i>Âge
                        </label>
                        <select name="age_filter" id="age_filter"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                            <option value="">Tous les âges</option>
                            <option value="0-10" {{ request('age_filter')=='0-10' ? 'selected' : '' }}>0 à 10 ans</option>
                            <option value="10-20" {{ request('age_filter')=='10-20' ? 'selected' : '' }}>10 à 20 ans</option>
                            <option value="20-30" {{ request('age_filter')=='20-30' ? 'selected' : '' }}>20 à 30 ans</option>
                            <option value="30-40" {{ request('age_filter')=='30-40' ? 'selected' : '' }}>30 à 40 ans</option>
                            <option value="40-50" {{ request('age_filter')=='40-50' ? 'selected' : '' }}>40 à 50 ans</option>
                            <option value="50-60" {{ request('age_filter')=='50-60' ? 'selected' : '' }}>50 à 60 ans</option>
                            <option value="60-70" {{ request('age_filter')=='60-70' ? 'selected' : '' }}>60 à 70 ans</option>
                            <option value="70-80" {{ request('age_filter')=='70-80' ? 'selected' : '' }}>70 à 80 ans</option>
                            <option value="80-90" {{ request('age_filter')=='80-90' ? 'selected' : '' }}>80 à 90 ans</option>
                            <option value="90-100" {{ request('age_filter')=='90-100' ? 'selected' : '' }}>90 à 100 ans</option>
                            <option value="100+" {{ request('age_filter')=='100+' ? 'selected' : '' }}>> 100 ans</option>
                        </select>
                    </div>

                    <!-- Filtre par période -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-clock mr-1"></i>Période d'inscription
                        </label>
                        <select name="period" id="period"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                            <option value="">Toutes les périodes</option>
                            <option value="day" {{ request('period')=='day' ? 'selected' : '' }}>Aujourd'hui</option>
                            <option value="week" {{ request('period')=='week' ? 'selected' : '' }}>Cette semaine</option>
                            <option value="month" {{ request('period')=='month' ? 'selected' : '' }}>Ce mois</option>
                            <option value="year" {{ request('period')=='year' ? 'selected' : '' }}>Cette année</option>
                        </select>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="flex items-end gap-3">
                        <button type="submit"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-search mr-2"></i>Filtrer
                        </button>
                        <a href="{{ route(auth()->user()->role->name . '.patients.index') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-times mr-2"></i>Réinitialiser
                        </a>
                    </div>
                </div>
            </form>

            <!-- Affichage des filtres actifs -->
            @if(request('search') || request('name_filter') || request('phone_filter') || request('gender_filter') ||
            request('age_filter') || request('period'))
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
                    @if(request('phone_filter'))
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                        Téléphone: {{ request('phone_filter') }}
                    </span>
                    @endif
                    @if(request('gender_filter'))
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200">
                        Sexe: {{ request('gender_filter') }}
                    </span>
                    @endif
                    @if(request('age_filter'))
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                        Âge: {{ request('age_filter') == '100+' ? '> 100 ans' : request('age_filter') . ' ans' }}
                    </span>
                    @endif
                    @if(request('period'))
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200">
                        Période: {{ request('period') == 'day' ? 'Aujourd\'hui' : (request('period') == 'week' ? 'Cette semaine' : (request('period') == 'month' ? 'Ce mois' : 'Cette année')) }}
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
    @if($patients->count() > 0)
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
                            Patient</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Sexe</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Date de naissance</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Téléphone</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Adresse</th>
                        <th
                            class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($patients as $patient)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            #{{ $patient->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div
                                        class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $patient->first_name }} {{ $patient->last_name }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        ID: {{ $patient->id }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $patient->gender == 'Homme' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200' }}">
                                <i class="fas {{ $patient->gender == 'Homme' ? 'fa-mars' : 'fa-venus' }} mr-1"></i>
                                {{ $patient->gender }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            <div class="flex items-center">
                                <i class="fas fa-birthday-cake text-gray-400 mr-2"></i>
                                {{ $patient->age }} ans
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            <div class="flex items-center">
                                <i class="fas fa-phone text-gray-400 mr-2"></i>
                                {{ $patient->phone }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                                {{ $patient->address ?: 'Non renseignée' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route(auth()->user()->role->name . '.patients.show', $patient->id) }}"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1 transition-colors duration-200"
                                    title="Voir les détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route(auth()->user()->role->name . '.patients.edit', [$patient->id, 'page' => request('page', 1)]) }}"
                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 p-1 transition-colors duration-200"
                                    title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form
                                    action="{{ route(auth()->user()->role->name . '.patients.destroy', $patient->id) }}"
                                    method="POST"
                                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce patient ?')"
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
        <div class="flex justify-center gap-2">
            <div class="sm:hidden">
                {{ $patients->appends(request()->query())->links('pagination::simple-tailwind') }}
            </div>
            <div class="hidden sm:block">
                {{ $patients->onEachSide(1)->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    @else
    <!-- État vide -->
    <div class="text-center py-12">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
            <i class="fas fa-users text-gray-400 text-xl"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Aucun patient trouvé</h3>
        <p class="text-gray-500 dark:text-gray-400 mb-6">
            @if(request('search') || request('name_filter') || request('phone_filter') || request('gender_filter') ||
            request('age_filter') || request('period'))
            Aucun patient ne correspond aux critères de recherche.
            @else
            Aucun patient n'a été ajouté pour le moment.
            @endif
        </p>
        <a href="{{ route(auth()->user()->role->name . '.patients.create') }}"
            class="gradient-button inline-flex items-center px-6 py-3 rounded-lg text-white font-semibold transition-all duration-300 shadow-lg hover:shadow-xl">
            <i class="fas fa-plus mr-2"></i>Ajouter le premier patient
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
        const resetButton = document.querySelector('a[href*="patients.index"]');
        if (resetButton) {
            resetButton.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = this.href;
            });
        }
    });
</script>
@endpush
