@extends('layouts.app')

@section('content')
<style>
    /* Masquer complètement la flèche native du datalist (Chrome, Edge, Safari) */
    #search[list]::-webkit-calendar-picker-indicator,
    #search[list]::-webkit-list-button {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        width: 0 !important;
        height: 0 !important;
        position: absolute !important;
        left: -9999px !important;
    }
    /* Conteneur de recherche : bouton toujours à droite */
    .search-input-wrapper {
        position: relative;
        width: 100%;
    }
    .search-input-wrapper #search-clear-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        right: 0.5rem;
        left: auto;
    }
</style>

<!-- Header -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg mb-8 border border-gray-200 dark:border-gray-700">
    <div class="container mx-auto px-6 py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="mb-4 md:mb-0">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-2">
                    <i class="fas fa-stethoscope mr-3 text-cyan-600 dark:text-cyan-400"></i>Liste des examens
                </h1>
                <p class="text-gray-600 dark:text-gray-400 text-lg">Gérez et consultez tous vos examens</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                @php
                $role = auth()->user()->role?->name;
                $routePrefix = ($role === 'superadmin' || $role === 'admin') ? $role . '.' : '';
                @endphp

                <a href="{{ route($routePrefix . 'examens.create') }}"
                    class="bg-cyan-600 hover:bg-cyan-700 dark:bg-cyan-700 dark:hover:bg-cyan-800 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center">
                    <i class="fas fa-plus mr-2"></i>Ajouter un examen
                </a>
                <a href="{{ route($routePrefix . 'examens.exportPdf') }}"
                    class="bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center">
                    <i class="fas fa-file-pdf mr-2"></i>Exporter PDF
                </a>
                <a href="{{ route($routePrefix . 'examens.print') }}" target="_blank"
                    class="bg-gray-600 hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-800 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center">
                    <i class="fas fa-print mr-2"></i>Imprimer
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Section des filtres -->
<div class="container mx-auto mb-8">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-filter mr-2 text-cyan-500"></i>Filtres de recherche
            </h2>

<!-- Résumé de la période sélectionnée -->
@php
$period = request('period', 'day');
$summary = '';
if ($period === 'day' && request('date')) {
$summary = 'Filtré sur le jour du ' . \Carbon\Carbon::parse(request('date'))->translatedFormat('d F Y');
} elseif ($period === 'week' && request('week')) {
$parts = explode('-W', request('week'));
if (count($parts) === 2) {
$start = \Carbon\Carbon::now()->setISODate($parts[0], $parts[1])->startOfWeek();
$end = \Carbon\Carbon::now()->setISODate($parts[0], $parts[1])->endOfWeek();
$summary = 'Filtré sur la semaine du ' . $start->translatedFormat('d F Y') . ' au ' . $end->translatedFormat('d F Y');
}
} elseif ($period === 'month' && request('month')) {
$parts = explode('-', request('month'));
if (count($parts) === 2) {
$summary = 'Filtré sur le mois de ' . \Carbon\Carbon::create($parts[0], $parts[1])->translatedFormat('F Y');
}
} elseif ($period === 'year' && request('year')) {
$summary = 'Filtré sur l\'année ' . request('year');
} elseif ($period === 'range' && request('date_start') && request('date_end')) {
                $summary = 'Filtré du ' . \Carbon\Carbon::parse(request('date_start'))->translatedFormat('d F Y') . ' au ' . \Carbon\Carbon::parse(request('date_end'))->translatedFormat('d F Y');
}
// Vérifier si au moins un filtre est actif (pour afficher le bouton Réinitialiser)
$hasActiveFilters = request('search') || request('service_id')
    || ($period === 'day' && request('date'))
    || ($period === 'week' && request('week'))
    || ($period === 'month' && request('month'))
    || ($period === 'year' && request('year'))
    || ($period === 'range' && request('date_start') && request('date_end'));
@endphp

            @if($summary || request('search') || request('service_id'))
            <div class="mb-4 flex flex-wrap items-center gap-3">
@if($summary)
                <span class="inline-block bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full text-sm font-medium transition">
                    <i class="fas fa-calendar mr-1"></i>{{ $summary }}
                </span>
                @endif
                @if(request('search'))
                <span class="inline-block bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 px-3 py-1 rounded-full text-sm font-medium transition">
                    <i class="fas fa-search mr-1"></i>Nom: "{{ request('search') }}"
                </span>
                @endif
                @if(request('service_id'))
                @php
                $selectedService = $services->firstWhere('id', request('service_id'));
                @endphp
                <span class="inline-block bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-3 py-1 rounded-full text-sm font-medium transition">
                    <i class="fas fa-hospital mr-1"></i>Service: {{ $selectedService ? $selectedService->nom : 'Inconnu' }}
                </span>
                @endif
                <a href="{{ route('examens.index') }}"
                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm underline">
                    Réinitialiser
                </a>
</div>
@endif

            <!-- Formulaire de filtres -->
            <form method="GET" action="" class="space-y-4" id="periode-filter-form" autocomplete="off">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Recherche par nom -->
                    <div class="relative">
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-search mr-1"></i>Rechercher par nom
                        </label>
                        <div class="search-input-wrapper relative">
                            <input type="text" name="search" id="search" value="{{ request('search') }}" list="examens-list"
                                class="w-full pl-10 pr-10 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                                placeholder="Tapez pour filtrer (ex: con, echo, radio...)">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                            <button type="button" id="search-clear-btn" aria-label="Effacer la recherche"
                                class="w-6 h-6 flex items-center justify-center rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-200 dark:hover:text-gray-300 dark:hover:bg-gray-600 transition-colors cursor-pointer z-10 hidden">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                        <datalist id="examens-list">
                            @php
                            $allExamens = \App\Models\Examen::with('service')
                                ->where('nom', 'NOT LIKE', '%Hospitalisation%')
                                ->orderBy('nom')
                                ->get();
                            @endphp
                            @foreach($allExamens as $ex)
                                @php
                                $isMedicament = $ex->service && (
                                    $ex->service->type_service === 'medicament' ||
                                    $ex->service->type_service === 'PHARMACIE'
                                ) && $ex->service->pharmacie;
                                $displayName = $isMedicament ? $ex->service->pharmacie->nom_medicament : $ex->nom;
                                @endphp
                                <option value="{{ $displayName }}" data-name="{{ $displayName }}">
                            @endforeach
                        </datalist>
                    </div>

                    <!-- Filtre par service -->
                    <div>
                        <label for="service_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-hospital mr-1"></i>Service
                        </label>
                        <select name="service_id" id="service_id"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                            <option value="">Tous les services</option>
                            @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ (string)request('service_id') === (string)$service->id ? 'selected' : '' }}>
                                {{ $service->nom }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Période -->
                    <div>
                        <label for="period" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-calendar mr-1"></i>Période
                        </label>
                        <select name="period" id="period"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                            <option value="day" {{ request('period', 'day') == 'day' ? 'selected' : '' }}>Jour</option>
                            <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Semaine</option>
                            <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Mois</option>
                            <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Année</option>
                            <option value="range" {{ request('period') == 'range' ? 'selected' : '' }}>Plage personnalisée</option>
    </select>
                    </div>

                    <!-- Input Jour -->
                    <div id="input-day" class="period-input">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-calendar-day mr-1"></i>Date
                        </label>
                        <input type="date" name="date" value="{{ request('date') }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                            placeholder="Choisir une date">
                    </div>

                    <!-- Input Semaine -->
                    <div id="input-week" class="period-input hidden">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-calendar-week mr-1"></i>Semaine
                        </label>
                        <input type="week" name="week" value="{{ request('week') }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                            placeholder="Choisir une semaine">
                    </div>

                    <!-- Input Mois -->
                    <div id="input-month" class="period-input hidden">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-calendar-alt mr-1"></i>Mois
                        </label>
                        <input type="month" name="month" value="{{ request('month') }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                            placeholder="Choisir un mois">
                    </div>

                    <!-- Input Année -->
                    <div id="input-year" class="period-input hidden">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-calendar mr-1"></i>Année
                        </label>
                        <input type="number" name="year" min="1900" max="2100" step="1"
                            value="{{ request('year', date('Y')) }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                            placeholder="Année">
                    </div>
                </div>

                <!-- Plage personnalisée -->
                <div id="input-range" class="period-input hidden">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-calendar-range mr-1"></i>Plage de dates
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Date de début</label>
                            <input type="date" name="date_start" value="{{ request('date_start') }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                                placeholder="Début">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Date de fin</label>
                            <input type="date" name="date_end" value="{{ request('date_end') }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                                placeholder="Fin">
                        </div>
                    </div>
    </div>

                <!-- Boutons d'action -->
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit" id="btn-filtrer"
                        class="bg-cyan-600 hover:bg-cyan-700 text-white font-semibold py-2 px-6 rounded-lg transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-search mr-2"></i>Filtrer
                    </button>
                    <div id="btn-reinitialiser-wrapper" class="{{ $hasActiveFilters ? '' : 'hidden' }}">
                        <a href="{{ route('examens.index') }}" id="btn-reinitialiser"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-6 rounded-lg transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-redo mr-2"></i>Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
    </div>
    </div>
    </div>
<!-- Tableau -->
<div class="container mx-auto">
    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <table class="min-w-full text-sm">
            <thead class="bg-gradient-to-r from-cyan-600 to-blue-600 text-white">
                <tr>
                    <th class="py-3 px-4 text-left font-semibold text-xs uppercase tracking-wider">ID</th>
                    <th class="py-3 px-4 text-left font-semibold text-xs uppercase tracking-wider">Nom</th>
                    <th class="py-3 px-4 text-left font-semibold text-xs uppercase tracking-wider">Service</th>
                    <th class="py-3 px-4 text-left font-semibold text-xs uppercase tracking-wider">Tarif</th>
                    <th class="py-3 px-4 text-left font-semibold text-xs uppercase tracking-wider">Part Médecin</th>
                    <th class="py-3 px-4 text-left font-semibold text-xs uppercase tracking-wider">Part Cabinet</th>
                    <th class="py-3 px-4 text-left font-semibold text-xs uppercase tracking-wider">Créé le</th>
                    <th class="py-3 px-4 text-center font-semibold text-xs uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($examens as $examen)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150" data-service-id="{{ $examen->idsvc }}">
                    <td class="py-3 px-4 text-gray-900 dark:text-gray-100 font-medium">#{{ $examen->id }}</td>
                    <td class="py-3 px-4">
                        @if($examen->service && $examen->service->type_service === 'PHARMACIE' && $examen->service->pharmacie)
                        <span class="font-semibold text-blue-600 dark:text-blue-400">
                            <i class="fas fa-pills mr-1"></i>{{ $examen->nom_affichage }}
                        </span>
                    @else
                        <span class="text-gray-900 dark:text-gray-100">{{ $examen->nom_affichage }}</span>
                    @endif
                </td>
                    <td class="py-3 px-4">
                        @if($examen->service && $examen->service->type_service === 'PHARMACIE' && $examen->service->pharmacie)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                            <i class="fas fa-prescription-bottle mr-1"></i>{{ $examen->service_affichage }}
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300">
                            <i class="fas fa-hospital mr-1"></i>{{ $examen->service_affichage }}
                        </span>
                    @endif
                </td>
                    <td class="py-3 px-4">
                        <span class="font-semibold text-cyan-600 dark:text-cyan-400">
                            {{ number_format($examen->tarif, 0, ',', ' ') }} MRU
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="font-medium text-purple-600 dark:text-purple-400">
                            {{ number_format($examen->part_medecin, 0, ',', ' ') }} MRU
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="font-medium text-green-600 dark:text-green-400">
                            {{ number_format($examen->part_cabinet, 0, ',', ' ') }} MRU
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-medium">
                            <i class="fas fa-calendar text-blue-500"></i>
                            {{ \Carbon\Carbon::parse($examen->created_at)->translatedFormat('d M Y') }}
                    </span>
                </td>
                    <td class="py-3 px-4">
                        <div class="flex justify-center space-x-2">
                        <!-- Voir -->
                        <a href="{{ route(auth()->user()->role->name . '.examens.show', $examen->id) }}"
                                class="p-2 text-blue-600 hover:text-white hover:bg-blue-600 dark:text-blue-400 dark:hover:bg-blue-500 rounded-lg transition-all duration-200"
                                title="Voir les détails">
                            <i class="fas fa-eye"></i>
                        </a>

                        <!-- Modifier -->
                        <a href="{{ route(auth()->user()->role->name . '.examens.edit', [$examen->id, 'page' => request('page', 1)]) }}"
                                class="p-2 text-indigo-600 hover:text-white hover:bg-indigo-600 dark:text-indigo-400 dark:hover:bg-indigo-500 rounded-lg transition-all duration-200"
                                title="Modifier">
                            <i class="fas fa-edit"></i>
                        </a>

                        <!-- Supprimer -->
                        <form action="{{ route(auth()->user()->role->name . '.examens.destroy', $examen->id) }}"
                                method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet examen ?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="p-2 text-red-600 hover:text-white hover:bg-red-600 dark:text-red-400 dark:hover:bg-red-500 rounded-lg transition-all duration-200"
                                    title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-8">
                        <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-3"></i>
                            <p class="text-lg font-medium">Aucun examen trouvé</p>
                            <p class="text-sm">Essayez de modifier vos critères de recherche</p>
                        </div>
                    </td>
                </tr>
                @endforelse
        </tbody>
    </table>
</div>
</div>

<!-- Pagination -->
<div class="container mx-auto py-6">
    <div class="flex justify-center gap-2">
        <div class="sm:hidden">
            {{ $examens->appends(request()->query())->links('pagination::simple-tailwind') }}
        </div>
        <div class="hidden sm:block">
            {{ $examens->onEachSide(1)->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Affichage dynamique des inputs selon la période
    function updatePeriodInputs() {
        const period = document.getElementById('period').value;
        document.querySelectorAll('.period-input').forEach(div => div.classList.add('hidden'));
        if (period === 'day') document.getElementById('input-day').classList.remove('hidden');
        if (period === 'week') document.getElementById('input-week').classList.remove('hidden');
        if (period === 'month') document.getElementById('input-month').classList.remove('hidden');
        if (period === 'year') document.getElementById('input-year').classList.remove('hidden');
        if (period === 'range') document.getElementById('input-range').classList.remove('hidden');
            updateFiltrerButtonState();
        updateFiltrerButtonLabel();
    }

    function hasAnyFilterFilled() {
        const period = document.getElementById('period').value;
        const searchValue = document.getElementById('search') ? document.getElementById('search').value : '';
        const serviceValue = document.getElementById('service_id') ? document.getElementById('service_id').value : '';

        const hasSearchFilter = searchValue.trim() !== '';
        const hasServiceFilter = serviceValue !== '';

        let hasValidPeriod = false;
        if (period === 'day') {
            hasValidPeriod = !!document.querySelector('input[name="date"]')?.value;
        } else if (period === 'week') {
            hasValidPeriod = !!document.querySelector('input[name="week"]')?.value;
        } else if (period === 'month') {
            hasValidPeriod = !!document.querySelector('input[name="month"]')?.value;
        } else if (period === 'year') {
            hasValidPeriod = !!document.querySelector('input[name="year"]')?.value;
        } else if (period === 'range') {
            const start = document.querySelector('input[name="date_start"]')?.value;
            const end = document.querySelector('input[name="date_end"]')?.value;
            hasValidPeriod = !!start && !!end && start <= end;
        }

        return hasValidPeriod || hasSearchFilter || hasServiceFilter;
    }

    function updateFiltrerButtonState() {
        const valid = hasAnyFilterFilled();

        document.getElementById('btn-filtrer').disabled = !valid;
        document.getElementById('btn-filtrer').classList.toggle('opacity-50', !valid);
        document.getElementById('btn-filtrer').classList.toggle('cursor-not-allowed', !valid);
        updateResetButtonVisibility();
    }

    function updateResetButtonVisibility() {
        const wrapper = document.getElementById('btn-reinitialiser-wrapper');
        if (wrapper) {
            wrapper.classList.toggle('hidden', !hasAnyFilterFilled());
        }
    }

    function updateSearchClearButton() {
        const searchInput = document.getElementById('search');
        const clearBtn = document.getElementById('search-clear-btn');
        if (searchInput && clearBtn) {
            clearBtn.classList.toggle('hidden', !searchInput.value.trim());
        }
    }

    function updateFiltrerButtonLabel() {
        // Le bouton affiche toujours "Filtrer"
        document.getElementById('btn-filtrer').innerHTML = '<i class="fas fa-search mr-2"></i>Filtrer';
    }

    // Filtrage instantané du tableau pendant la saisie
    function filterTableInRealTime() {
        const searchInput = document.getElementById('search');
        const serviceSelect = document.getElementById('service_id');
        const clearBtn = document.getElementById('search-clear-btn');
        const tableRows = document.querySelectorAll('tbody tr:not(:has([colspan]))');
        
        // Bouton X : visible uniquement quand le champ a une valeur, efface au clic
        if (clearBtn && searchInput) {
            clearBtn.addEventListener('click', function(e) {
                e.preventDefault();
                searchInput.value = '';
                searchInput.focus();
                updateSearchClearButton();
                updateFiltrerButtonState();
                const selectedService = serviceSelect.value;
                let visibleCount = 0;
                tableRows.forEach(row => {
                    const nomCell = row.querySelector('td:nth-child(2)');
                    const serviceCell = row.querySelector('td:nth-child(3)');
                    if (!nomCell || !serviceCell) return;
                    const matchesService = selectedService === '' || row.dataset.serviceId === selectedService;
                    if (matchesService) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                updateNoResultsMessage(visibleCount);
            });
        }
        
        searchInput.addEventListener('input', function() {
            updateSearchClearButton();
            const searchTerm = this.value.toLowerCase().trim();
            const selectedService = serviceSelect.value;

            let visibleCount = 0;
            tableRows.forEach(row => {
                const nomCell = row.querySelector('td:nth-child(2)');
                const serviceCell = row.querySelector('td:nth-child(3)');

                if (!nomCell || !serviceCell) return;

                const nomText = nomCell.textContent.toLowerCase();
                const serviceText = serviceCell.textContent.toLowerCase();

                // Vérifier si le nom contient le terme de recherche
                const matchesSearch = searchTerm === '' || nomText.includes(searchTerm);

                // Vérifier si le service correspond (si un service est sélectionné)
                const matchesService = selectedService === '' || row.dataset.serviceId === selectedService;

                if (matchesSearch && matchesService) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Afficher un message si aucun résultat
            updateNoResultsMessage(visibleCount);
            updateFiltrerButtonState();
        });

        serviceSelect.addEventListener('change', function() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const selectedService = this.value;

            let visibleCount = 0;
            tableRows.forEach(row => {
                const nomCell = row.querySelector('td:nth-child(2)');

                if (!nomCell) return;

                const nomText = nomCell.textContent.toLowerCase();

                // Vérifier si le nom contient le terme de recherche
                const matchesSearch = searchTerm === '' || nomText.includes(searchTerm);

                // Vérifier si le service correspond
                const matchesService = selectedService === '' || row.dataset.serviceId === selectedService;

                if (matchesSearch && matchesService) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            updateNoResultsMessage(visibleCount);
            updateFiltrerButtonState();
        });
    }

    function updateNoResultsMessage(visibleCount) {
        let noResultsRow = document.querySelector('#no-results-instant');

        if (visibleCount === 0) {
            if (!noResultsRow) {
                const tbody = document.querySelector('tbody');
                noResultsRow = document.createElement('tr');
                noResultsRow.id = 'no-results-instant';
                noResultsRow.innerHTML = `
                    <td colspan="8" class="text-center py-8">
                        <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-search text-4xl mb-3"></i>
                            <p class="text-lg font-medium">Aucun examen trouvé</p>
                            <p class="text-sm">Aucun résultat ne correspond à votre recherche</p>
                        </div>
                    </td>
                `;
                tbody.appendChild(noResultsRow);
            }
            noResultsRow.style.display = '';
        } else {
            if (noResultsRow) {
                noResultsRow.style.display = 'none';
            }
        }
    }

    // Event listeners
    document.getElementById('period').addEventListener('change', updatePeriodInputs);

    document.querySelectorAll('.period-input input').forEach(input => {
        input.addEventListener('input', function() {
            updateFiltrerButtonState();
        });
    });

    // Initialisation au chargement
    window.addEventListener('DOMContentLoaded', function() {
        updatePeriodInputs();
        updateFiltrerButtonState();
        updateFiltrerButtonLabel();
        updateSearchClearButton();
        filterTableInRealTime(); // Activer le filtrage en temps réel
    });
</script>
@endpush
