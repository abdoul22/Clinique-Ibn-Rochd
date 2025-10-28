@extends('layouts.app')
@section('title', 'Liste des Caisses')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 p-4">
    <!-- En-tête moderne avec gradient -->
    <div class="mb-8">
        <div class="relative overflow-hidden rounded-3xl gradient-header shadow-2xl">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="relative p-8 md:p-12">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="mb-6 md:mb-0">
                        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                            💳 Gestion des Factures
                        </h1>
                        <p class="text-blue-100 text-lg">
                            Gérez et consultez toutes vos factures en un clin d'œil
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="{{ route('caisses.create') }}"
                            class="inline-flex items-center px-6 py-3 bg-white/20 backdrop-blur-sm border border-white/30 rounded-xl text-white font-semibold hover:bg-white/30 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Nouvelle Facture
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Zone de filtrage moderne -->
    <div class="mb-8">
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div
                class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 p-6 border-b border-gray-200 dark:border-gray-600">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filtres de recherche
                </h2>
            </div>

            <form method="GET" action="" class="p-6" id="filter-form">
                <!-- Première ligne de filtres -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Recherche générale -->
                    <div class="col-span-1 md:col-span-2 lg:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            🔍 Recherche générale
                        </label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="N° entrée, patient, médecin..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>

                    <!-- Filtre Patient -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            👤 Patient
                        </label>
                        <input type="text" name="patient_filter" value="{{ request('patient_filter') }}"
                            placeholder="Nom du patient..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>

                    <!-- Filtre Numéro Patient -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            📱 N° Téléphone
                        </label>
                        <input type="text" name="numero_patient_filter" value="{{ request('numero_patient_filter') }}"
                            placeholder="Numéro de téléphone..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>

                    <!-- Filtre Médecin -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            👨‍⚕️ Médecin
                        </label>
                        <input type="text" name="medecin_filter" value="{{ request('medecin_filter') }}"
                            placeholder="Nom du médecin..."
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                </div>

                <!-- Deuxième ligne - Filtres de période -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            📅 Période
                        </label>
                        <select name="period" id="period"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="day" {{ request('period', 'day' )=='day' ? 'selected' : '' }}>Jour</option>
                            <option value="week" {{ request('period')=='week' ? 'selected' : '' }}>Semaine</option>
                            <option value="month" {{ request('period')=='month' ? 'selected' : '' }}>Mois</option>
                            <option value="year" {{ request('period')=='year' ? 'selected' : '' }}>Année</option>
                            <option value="range" {{ request('period')=='range' ? 'selected' : '' }}>Plage personnalisée
                            </option>
                        </select>
                    </div>

                    <!-- Inputs de période dynamiques -->
                    <div id="input-day" class="period-input">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date</label>
                        <input type="date" name="date" value="{{ request('date') }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>

                    <div id="input-week" class="period-input hidden">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Semaine</label>
                        <input type="week" name="week" value="{{ request('week') }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>

                    <div id="input-month" class="period-input hidden">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mois</label>
                        <input type="month" name="month" value="{{ request('month') }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>

                    <div id="input-year" class="period-input hidden">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Année</label>
                        <input type="number" name="year" min="1900" max="2100" value="{{ request('year', date('Y')) }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>

                    <div id="input-range" class="period-input hidden col-span-2 grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Début</label>
                            <input type="date" name="date_start" value="{{ request('date_start') }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fin</label>
                            <input type="date" name="date_end" value="{{ request('date_end') }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex flex-wrap gap-3">
                    <button type="submit"
                        class="px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-700 hover:to-purple-700 focus:ring-2 focus:ring-blue-500 transition-all duration-300 shadow-lg">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filtrer
                    </button>
                    <a href="{{ route('caisses.index') }}"
                        class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:ring-2 focus:ring-gray-500 transition-all duration-300">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Affichage des filtres actifs -->
    @php
    $hasFilters = request('search') || request('patient_filter') || request('numero_patient_filter') ||
    request('medecin_filter') || request('date') || request('week') ||
    request('month') || request('year') || request('date_start');
    @endphp

    @if($hasFilters)
    <div class="mb-6">
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-blue-800 dark:text-blue-200 font-medium">Filtres actifs :</span>
                </div>
                <a href="{{ route('caisses.index') }}"
                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200 font-medium">
                    Effacer tous les filtres
                </a>
            </div>
            <div class="mt-2 flex flex-wrap gap-2">
                @if(request('search'))
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200">
                    Recherche: "{{ request('search') }}"
                </span>
                @endif
                @if(request('patient_filter'))
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200">
                    Patient: "{{ request('patient_filter') }}"
                </span>
                @endif
                @if(request('numero_patient_filter'))
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-purple-100 dark:bg-purple-800 text-purple-800 dark:text-purple-200">
                    Téléphone: "{{ request('numero_patient_filter') }}"
                </span>
                @endif
                @if(request('medecin_filter'))
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-indigo-100 dark:bg-indigo-800 text-indigo-800 dark:text-indigo-200">
                    Médecin: "{{ request('medecin_filter') }}"
                </span>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Tableau moderne -->
    <div
        class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gradient-to-r from-gray-800 to-gray-900 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold">N° Entrée</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Patient</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Téléphone</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Médecin</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Date</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Total</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Caissier</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($caisses as $caisse)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                #{{ $caisse->numero_entre }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $caisse->patient->first_name ?? 'N/A' }} {{ $caisse->patient->last_name ?? '' }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $caisse->patient->phone ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 dark:text-white">
                                @if($caisse->medecin)
                                {{ $caisse->medecin->nom_complet_avec_prenom }}
                                @else
                                N/A
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $caisse->created_at->setTimezone('Africa/Nouakchott')->format('d/m/Y H:i') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $caisse->created_at->setTimezone('Africa/Nouakchott')->diffForHumans() }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span
                                class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                {{ number_format($caisse->total, 2) }} MRU
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ $caisse->nom_caissier }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center space-x-2">
                                @php
                                $role = auth()->user()->role->name;
                                $showRoute = $role === 'superadmin' || $role === 'admin' ? $role . '.caisses.show' :
                                'caisses.show';
                                $destroyRoute = $role === 'superadmin' || $role === 'admin' ? $role . '.caisses.destroy'
                                : 'caisses.destroy';
                                @endphp

                                <a href="{{ route($showRoute, $caisse->id) }}"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($role === 'superadmin')
                                <form action="{{ route($destroyRoute, $caisse->id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette facture ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Aucune facture
                                    trouvée</h3>
                                <p class="text-gray-500 dark:text-gray-400 mb-4">Aucune facture ne correspond à vos
                                    critères de recherche</p>
                                <a href="{{ route('caisses.create') }}"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Créer la première facture
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($caisses->hasPages())
    <div class="mt-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4">
            <div class="flex justify-center gap-2">
                <div class="sm:hidden">
                    {{ $caisses->appends(request()->query())->links('pagination::simple-tailwind') }}
                </div>
                <div class="hidden sm:block">
                    {{ $caisses->onEachSide(1)->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- JavaScript pour la gestion des filtres -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const periodSelect = document.getElementById('period');

    function updatePeriodInputs() {
        const period = periodSelect.value;
        document.querySelectorAll('.period-input').forEach(div => div.classList.add('hidden'));

        if (period === 'day') document.getElementById('input-day').classList.remove('hidden');
        else if (period === 'week') document.getElementById('input-week').classList.remove('hidden');
        else if (period === 'month') document.getElementById('input-month').classList.remove('hidden');
        else if (period === 'year') document.getElementById('input-year').classList.remove('hidden');
        else if (period === 'range') document.getElementById('input-range').classList.remove('hidden');
    }

    periodSelect.addEventListener('change', updatePeriodInputs);
    updatePeriodInputs(); // Initialiser l'affichage
});
</script>

@if(session('timestamp'))
<script>
    // Si on vient d'une modification, forcer le rafraîchissement du cache
    if (performance.navigation.type === performance.navigation.TYPE_BACK_FORWARD) {
        window.location.reload(true);
    }
</script>
@endif
@endsection
