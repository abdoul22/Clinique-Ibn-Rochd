@extends('layouts.app')

@section('title', 'État de Caisse')

@section('content')

<!-- Header avec gradient -->
<div class="gradient-header mb-8">
    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="mb-4 md:mb-0">
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                    <i class="fas fa-calculator mr-3"></i>État de Caisse
                </h1>
                <p class="text-blue-100 text-lg">Gérez et analysez vos états de caisse</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="{{ route('modepaiements.dashboard') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center">
                    <i class="fas fa-chart-pie mr-2"></i>Trésorerie Globale
                </a>
                <a href="{{ route('etatcaisse.exportPdf', request()->query()) }}"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-3 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center">
                    <i class="fas fa-file-pdf mr-2"></i>Exporter PDF
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
            $summary = 'Filtré sur la semaine du ' . $start->translatedFormat('d F Y') . ' au ' .
            $end->translatedFormat('d F Y');
            }
            } elseif ($period === 'month' && request('month')) {
            $parts = explode('-', request('month'));
            if (count($parts) === 2) {
            $summary = 'Filtré sur le mois de ' . \Carbon\Carbon::create($parts[0], $parts[1])->translatedFormat('F Y');
            }
            } elseif ($period === 'year' && request('year')) {
            $summary = 'Filtré sur l\'année ' . request('year');
            } elseif ($period === 'range' && request('date_start') && request('date_end')) {
            $summary = 'Filtré du ' . \Carbon\Carbon::parse(request('date_start'))->translatedFormat('d F Y') . ' au ' .
            \Carbon\Carbon::parse(request('date_end'))->translatedFormat('d F Y');
            }
            @endphp

            @if($summary)
            <div class="mb-4 flex items-center gap-3">
                <span
                    class="inline-block bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full text-sm font-medium transition">
                    {{ $summary }}
                </span>
                <a href="{{ route('etatcaisse.index') }}"
                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm underline">
                    Réinitialiser
                </a>
            </div>
            @endif

            <!-- Formulaire de filtres -->
            <form method="GET" action="" class="space-y-4" id="periode-filter-form" autocomplete="off">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Période -->
                    <div>
                        <label for="period" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-calendar mr-1"></i>Période
                        </label>
                        <select name="period" id="period"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                            <option value="day" {{ request('period', 'day' )=='day' ? 'selected' : '' }}>Jour</option>
                            <option value="week" {{ request('period')=='week' ? 'selected' : '' }}>Semaine</option>
                            <option value="month" {{ request('period')=='month' ? 'selected' : '' }}>Mois</option>
                            <option value="year" {{ request('period')=='year' ? 'selected' : '' }}>Année</option>
                            <option value="range" {{ request('period')=='range' ? 'selected' : '' }}>Plage personnalisée
                            </option>
                        </select>
                    </div>

                    <!-- Input Jour -->
                    <div id="input-day" class="period-input">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-calendar-day mr-1"></i>Date
                        </label>
                        <input type="date" name="date" value="{{ request('date') }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                            placeholder="Choisir une date">
                    </div>

                    <!-- Input Semaine -->
                    <div id="input-week" class="period-input hidden">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-calendar-week mr-1"></i>Semaine
                        </label>
                        <input type="week" name="week" value="{{ request('week') }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                            placeholder="Choisir une semaine">
                    </div>

                    <!-- Input Mois -->
                    <div id="input-month" class="period-input hidden">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-calendar-alt mr-1"></i>Mois
                        </label>
                        <input type="month" name="month" value="{{ request('month') }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                            placeholder="Choisir un mois">
                    </div>

                    <!-- Input Année -->
                    <div id="input-year" class="period-input hidden">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-calendar mr-1"></i>Année
                        </label>
                        <input type="number" name="year" min="1900" max="2100" step="1"
                            value="{{ request('year', date('Y')) }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                            placeholder="Année">
                    </div>
                    <!-- Filtre Médecin -->
                    <div>
                        <label for="medecin_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-user-md mr-1"></i>Médecin
                        </label>
                        <select name="medecin_id" id="medecin_id"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                            <option value="">Tous</option>
                            @foreach(($medecins ?? []) as $m)
                            <option value="{{ $m->id }}" {{ (string)request('medecin_id')===(string)$m->id ? 'selected'
                                : '' }}>
                                {{ $m->nom_complet_avec_specialite ?? $m->nom_complet ?? $m->nom }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtre Statut -->
                    <div>
                        <label for="statut" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-check-circle mr-1"></i>Statut
                        </label>
                        <select name="statut" id="statut"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                            <option value="">Tous</option>
                            <option value="valide" {{ request('statut') == 'valide' ? 'selected' : '' }}>Validé</option>
                            <option value="non_valide" {{ request('statut') == 'non_valide' ? 'selected' : '' }}>Non Validé</option>
                        </select>
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
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                                placeholder="Début">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Date de fin</label>
                            <input type="date" name="date_end" value="{{ request('date_end') }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-900 text-gray-900 dark:text-white"
                                placeholder="Fin">
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit" id="btn-filtrer"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-search mr-2"></i>Filtrer
                    </button>
                    <a href="{{ route('etatcaisse.index') }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-6 rounded-lg transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-times mr-2"></i>Afficher tous
                    </a>
                    <a href="{{ route('etatcaisse.print', request()->query()) }}" target="_blank"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-lg transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-print mr-2"></i>Imprimer
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Affichage des crédits personnel -->
@if(request('personnel_id'))
@php
$employe = $personnels->where('id', request('personnel_id'))->first();
@endphp
<div class="container mx-auto px-4 mb-6">
    <div class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg p-4">
        <div class="flex items-center">
            <i class="fas fa-user-circle text-green-500 mr-3 text-xl"></i>
            <div>
                <h3 class="text-lg font-semibold text-green-800 dark:text-green-200">
                    Total des crédits de {{ $employe->nom }}
                </h3>
                <p class="text-green-700 dark:text-green-300 text-2xl font-bold">
                    {{ number_format($employe->credit, 2) }} MRU
                </p>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Bouton de validation en masse (affiché dynamiquement) -->
<div id="bulkValidationBar" class="container mx-auto px-4 mb-4 hidden">
    <div class="bg-blue-50 dark:bg-blue-900/30 border-2 border-blue-300 dark:border-blue-700 rounded-lg p-4 flex items-center justify-between shadow-lg">
        <div class="flex items-center space-x-3">
            <div class="bg-blue-100 dark:bg-blue-800 p-2 rounded-full">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <span class="font-semibold text-blue-900 dark:text-blue-100 flex items-center space-x-2">
                    <span id="selectedCount">0</span> 
                    <span>part(s) médecin sélectionnée(s)</span>
                    <span id="multiPageIndicator" class="hidden px-2 py-0.5 bg-blue-200 dark:bg-blue-700 text-blue-800 dark:text-blue-200 text-xs rounded-full font-medium">
                        Multi-pages
                    </span>
                </span>
                <p class="text-sm text-blue-700 dark:text-blue-300">
                    Total: <span id="selectedTotal" class="font-bold">0</span> MRU
                </p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <button type="button" onclick="validateBulkSelection()" 
                class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>Valider la sélection</span>
            </button>
            <button type="button" onclick="clearSelection()" 
                class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-4 py-2 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<!-- Tableau -->
<div class="table-container">
    <table class="table-main border border-gray-200 dark:border-gray-700">
        <thead class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
            <tr>
                <th class="py-2 px-2 text-center font-semibold text-xs uppercase tracking-wider w-16">
                    <input type="checkbox" id="selectAll" 
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 cursor-pointer"
                        title="Sélectionner toutes les parts médecin non validées de toutes les pages">
                </th>
                <th class="py-2 px-2 text-left font-semibold text-xs uppercase tracking-wider">Désignation</th>
                <th class="py-2 px-2 text-left font-semibold text-xs uppercase tracking-wider">Recette</th>
                <th class="py-2 px-2 text-left font-semibold text-xs uppercase tracking-wider">Part Médecin</th>
                <th class="py-2 px-2 text-left font-semibold text-xs uppercase tracking-wider">Part Clinique</th>
                <th class="py-2 px-2 text-left font-semibold text-xs uppercase tracking-wider">Paiement</th>
                <th class="py-2 px-2 text-left font-semibold text-xs uppercase tracking-wider">Validation</th>
                <th class="py-2 px-2 text-left font-semibold text-xs uppercase tracking-wider">Assurance</th>
                <th class="py-2 px-2 text-left font-semibold text-xs uppercase tracking-wider">Médecin</th>
                <th class="py-2 px-2 text-left font-semibold text-xs uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody id="etatCaisseTableBody" class="table-body">
            @forelse($etatcaisses as $etat)
            <tr class="table-row">
                @include('etatcaisse.partials.row', ['etat' => $etat])
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center text-gray-500 dark:text-gray-400 py-4">Aucun état de caisse
                    enregistré.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="pagination-container my-4">
    <div class="flex justify-center gap-2">
        <div class="sm:hidden">
            {{ $etatcaisses->appends(request()->query())->links('pagination::simple-tailwind') }}
        </div>
        <div class="hidden sm:block">
            {{ $etatcaisses->onEachSide(1)->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- Résumé filtré moderne (toujours affiché, même sans filtre) -->
@php
$isFiltre = (
request('date') || request('week') || request('month') || request('year') || (request('date_start') &&
request('date_end'))
);
$resume = $isFiltre ? $resumeFiltre : $resumeGlobal;
@endphp
@if($resume)
<div
    class="alert alert-info my-6 rounded-xl shadow-lg border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/10">
    <h2 class="text-blue-700 dark:text-blue-300 font-semibold mb-4 text-lg flex items-center gap-2">
        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
        </svg>
        Résumé de la période sélectionnée
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 my-4">
        <div class="card text-sm flex flex-col items-center">
            <span class="font-bold text-gray-700 dark:text-gray-300 mb-1">Recette Caisse</span>
            <span class="text-blue-700 dark:text-blue-400 text-xl font-bold">{{ number_format($resume['recette'], 0,
                ',', ' ') }} MRU</span>
        </div>
        <div class="card text-sm flex flex-col items-center">
            <span class="font-bold text-gray-700 dark:text-gray-300 mb-1">Part Médecin</span>
            <span class="text-purple-700 dark:text-purple-400 text-xl font-bold">{{
                number_format($resume['part_medecin'], 0, ',', ' ') }} MRU</span>
        </div>
        <div class="card text-sm flex flex-col items-center">
            <span class="font-bold text-gray-700 dark:text-gray-300 mb-1">Part Clinique</span>
            <span class="text-green-700 dark:text-green-400 text-xl font-bold">{{ number_format($resume['part_cabinet'],
                0, ',', ' ') }} MRU</span>
        </div>
        <div class="card text-sm flex flex-col items-center">
            <span class="font-bold text-gray-700 dark:text-gray-300 mb-1">Dépense</span>
            <span class="text-red-700 dark:text-red-400 text-xl font-bold">{{ number_format($resume['depense'], 0, ',',
                ' ') }} MRU</span>
        </div>
        <div class="card text-sm flex flex-col items-center">
            <span class="font-bold text-gray-700 dark:text-gray-300 mb-1">Crédit Personnel</span>
            <span class="text-indigo-700 dark:text-indigo-400 text-xl font-bold">{{
                number_format($resume['credit_personnel'], 0, ',', ' ') }} MRU</span>
        </div>
        <div class="card text-sm flex flex-col items-center">
            <span class="font-bold text-gray-700 dark:text-gray-300 mb-1">Crédit Assurance</span>
            <span class="text-cyan-700 dark:text-cyan-400 text-xl font-bold">{{
                number_format($resume['credit_assurance'], 0, ',', ' ') }} MRU</span>
        </div>
    </div>
    <div class="mt-4">
        <canvas id="chartFiltre" height="100" class="w-full h-48 sm:h-32 md:h-48 lg:h-80 xl:h-96"></canvas>
    </div>
</div>
@endif

<script>
    function ajouterEtatCaisse(data) {
        const tbody = document.getElementById('etatCaisseTableBody');

        // Supprime le message "aucun état enregistré"
        const emptyRow = document.querySelector('#etatCaisseTableBody tr td[colspan]');
        if (emptyRow) emptyRow.parentElement.remove();

        // Ajouter la nouvelle ligne rendue côté serveur
        tbody.insertAdjacentHTML('afterbegin', data.view);
    }
</script>

<!-- Script pour soumettre automatiquement le form du personnel sélectionné -->
<script>
    const personnelSelect = document.getElementById('personnelSelect');
    if (personnelSelect) {
        personnelSelect.addEventListener('change', function () {
            if (this.value) {
                const form = document.getElementById('personnelForm');
                form.action = `/etatcaisse/generer/personnel/${this.value}`;
                form.submit();
            }
        });
    }
</script>

@push('scripts')
<script>
    // Affichage dynamique des inputs selon la période + accessibilité + transitions
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
    function updateFiltrerButtonState() {
        const period = document.getElementById('period').value;
        const medecinSelected = !!(document.getElementById('medecin_id') && document.getElementById('medecin_id').value);
        const statutSelected = !!(document.getElementById('statut') && document.getElementById('statut').value);
        let hasValidPeriod = false;
        if (period === 'day') {
            hasValidPeriod = !!document.querySelector('input[name="date"]').value;
        } else if (period === 'week') {
            hasValidPeriod = !!document.querySelector('input[name="week"]').value;
        } else if (period === 'month') {
            hasValidPeriod = !!document.querySelector('input[name="month"]').value;
        } else if (period === 'year') {
            hasValidPeriod = !!document.querySelector('input[name="year"]').value;
        } else if (period === 'range') {
            const start = document.querySelector('input[name="date_start"]').value;
            const end = document.querySelector('input[name="date_end"]').value;
            hasValidPeriod = !!start && !!end && start <= end;
        }
        const valid = hasValidPeriod || medecinSelected || statutSelected; // Autoriser filtre par médecin ou statut seul
        document.getElementById('btn-filtrer').disabled = !valid;
        document.getElementById('btn-filtrer').classList.toggle('opacity-50', !valid);
        document.getElementById('btn-filtrer').classList.toggle('cursor-not-allowed', !valid);
    }
    function updateFiltrerButtonLabel() {
        const period = document.getElementById('period').value;
        let label = 'Filtrer';
        if (period === 'day') label = 'Filtrer par jour';
        else if (period === 'week') label = 'Filtrer par semaine';
        else if (period === 'month') label = 'Filtrer par mois';
        else if (period === 'year') label = 'Filtrer par année';
        else if (period === 'range') label = 'Filtrer par plage';
        document.getElementById('btn-filtrer').textContent = label;
    }
    document.getElementById('period').addEventListener('change', updatePeriodInputs);
    document.querySelectorAll('.period-input input').forEach(input => {
        input.addEventListener('input', function() {
            updateFiltrerButtonState();
        });
    });
    window.addEventListener('DOMContentLoaded', function() {
        updatePeriodInputs();
        updateFiltrerButtonState();
        updateFiltrerButtonLabel();
    });

    // Mise à jour quand le médecin change
    const medecinSelect = document.getElementById('medecin_id');
    if (medecinSelect) {
        medecinSelect.addEventListener('change', function() {
            updateFiltrerButtonState();
        });
    }

    // Mise à jour quand le statut change
    const statutSelect = document.getElementById('statut');
    if (statutSelect) {
        statutSelect.addEventListener('change', function() {
            updateFiltrerButtonState();
        });
    }
</script>

<script>
    // Chart.js dynamique pour le résumé filtré
    function renderChartFiltre() {
        const ctx = document.getElementById('chartFiltre');
        if (!ctx) return;
        // Détruire l'ancien graphique si existant
        if (window.chartFiltreInstance) {
            window.chartFiltreInstance.destroy();
        }
        const data = @json($chartFiltreData);
        if (!data.length) return;
        window.chartFiltreInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Recette', 'Part Médecin', 'Part Clinique', 'Dépense', 'Crédit Personnel', 'Crédit Assurance'],
                datasets: [{
                    label: 'Montant (MRU)',
                    data: data,
                    backgroundColor: ['#3b82f6', '#a78bfa', '#10b981', '#ef4444', '#8b5cf6', '#14b8a6'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    title: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#374151',
                            callback: function(value) { return value.toLocaleString() + ' MRU'; },
                            font: {
                                size: window.innerWidth < 640 ? 10 : 12
                            }
                        },
                        grid: { color: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb' }
                    },
                    x: {
                        ticks: {
                            color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#374151',
                            font: {
                                size: window.innerWidth < 640 ? 10 : 12
                            },
                            maxRotation: window.innerWidth < 640 ? 45 : 0
                        },
                        grid: { color: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb' }
                    }
                }
            }
        });
    }
    document.addEventListener('DOMContentLoaded', renderChartFiltre);
    document.addEventListener('turbo:load', renderChartFiltre); // Pour Turbo/Hotwire éventuel
</script>

<script>
    // Chart.js dynamique pour le graphique global
    function renderChartGlobal() {
        const ctx = document.getElementById('chartGlobal');
        if (!ctx) return;
        if (window.chartGlobalInstance) {
            window.chartGlobalInstance.destroy();
        }
        const data = @json($chartGlobalData);
        if (!data.length) return;
        window.chartGlobalInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Recette', 'Part Médecin', 'Part Clinique', 'Dépense', 'Crédit Personnel', 'Crédit Assurance'],
                datasets: [{
                    label: 'Montant (MRU)',
                    data: data,
                    backgroundColor: ['#3b82f6', '#a78bfa', '#10b981', '#ef4444', '#8b5cf6', '#14b8a6'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    title: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#374151',
                            callback: function(value) { return value.toLocaleString() + ' MRU'; },
                            font: {
                                size: window.innerWidth < 640 ? 10 : 12
                            }
                        },
                        grid: { color: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb' }
                    },
                    x: {
                        ticks: {
                            color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#374151',
                            font: {
                                size: window.innerWidth < 640 ? 10 : 12
                            },
                            maxRotation: window.innerWidth < 640 ? 45 : 0
                        },
                        grid: { color: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb' }
                    }
                }
            }
        });
    }
    document.addEventListener('DOMContentLoaded', renderChartGlobal);
    document.addEventListener('turbo:load', renderChartGlobal);
</script>
@endpush

@if(session('timestamp'))
<script>
    // Si on vient d'une modification de caisse, forcer le rafraîchissement
    if (performance.navigation.type === performance.navigation.TYPE_BACK_FORWARD) {
        window.location.reload(true);
    }
</script>
@endif

<!-- Modale de sélection du mode de paiement -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0"
        id="paymentModalContent">
        <div class="p-6">
            <!-- En-tête de la modale -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Mode de paiement</h3>
                        <p id="modalDescription" class="text-sm text-gray-500 dark:text-gray-400">Sélectionnez le mode de paiement pour la
                            part médecin</p>
                    </div>
                </div>
                <button onclick="closePaymentModal()"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Montant à payer -->
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-6">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Part médecin à payer :</span>
                    <span id="partMedecinAmount" class="text-lg font-bold text-blue-600 dark:text-blue-400"></span>
                </div>
            </div>

            <!-- Options de paiement -->
            <form id="paymentForm" method="POST">
                @csrf
                <div class="space-y-3 mb-6">
                    <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Choisissez le mode de
                        paiement :</div>

                    <label
                        class="payment-option flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <input type="radio" name="mode_paiement" value="especes" class="sr-only" required>
                        <div class="payment-icon bg-green-100 dark:bg-green-900 p-2 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 dark:text-white">Espèces</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Paiement en liquide</div>
                        </div>
                        <div class="payment-check hidden">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </label>

                    <label
                        class="payment-option flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <input type="radio" name="mode_paiement" value="bankily" class="sr-only">
                        <div class="payment-icon bg-blue-100 dark:bg-blue-900 p-2 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 dark:text-white">Bankily</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Paiement mobile</div>
                        </div>
                        <div class="payment-check hidden">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </label>

                    <label
                        class="payment-option flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <input type="radio" name="mode_paiement" value="masrivi" class="sr-only">
                        <div class="payment-icon bg-purple-100 dark:bg-purple-900 p-2 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 dark:text-white">Masrivi</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Paiement mobile</div>
                        </div>
                        <div class="payment-check hidden">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </label>

                    <label
                        class="payment-option flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <input type="radio" name="mode_paiement" value="sedad" class="sr-only">
                        <div class="payment-icon bg-indigo-100 dark:bg-indigo-900 p-2 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900 dark:text-white">Sedad</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Paiement mobile</div>
                        </div>
                        <div class="payment-check hidden">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </label>
                </div>

                <!-- Boutons d'action -->
                <div class="flex space-x-3">
                    <button type="button" onclick="closePaymentModal()"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 py-3 px-4 rounded-lg font-medium transition-colors">
                        Annuler
                    </button>
                    <button type="submit"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        Valider le paiement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentEtatId = null;
    let isBulkValidation = false;
    let selectedEtatIds = [];

function openPaymentModal(etatId, partMedecin) {
    currentEtatId = etatId;
    isBulkValidation = false;

    // Mettre à jour le texte de description
    document.getElementById('modalDescription').textContent = 'Sélectionnez le mode de paiement pour la part médecin';

    // Mettre à jour le montant
    document.getElementById('partMedecinAmount').textContent = new Intl.NumberFormat('fr-FR').format(partMedecin) + ' MRU';

    // Mettre à jour l'action du formulaire
    const baseUrl = '{{ route("etatcaisse.valider", ":id") }}';
    document.getElementById('paymentForm').action = baseUrl.replace(':id', etatId);

    // Afficher la modale avec animation
    const modal = document.getElementById('paymentModal');
    const content = document.getElementById('paymentModalContent');

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);

    // Bloquer le scroll du body
    document.body.style.overflow = 'hidden';
}

// Gestion de la sélection multiple avec persistance
function updateBulkSelection() {
    const bar = document.getElementById('bulkValidationBar');
    const selectAllCheckbox = document.getElementById('selectAll');

    // Récupérer toutes les sélections depuis sessionStorage
    let allSelections = JSON.parse(sessionStorage.getItem('etatcaisse_selections') || '{}');
    
    // Mettre à jour avec l'état actuel de la page
    const currentPageCheckboxes = document.querySelectorAll('.part-medecin-checkbox');
    currentPageCheckboxes.forEach(cb => {
        const etatId = cb.dataset.etatId;
        if (cb.checked) {
            allSelections[etatId] = parseFloat(cb.dataset.partMedecin);
        } else {
            delete allSelections[etatId];
        }
    });

    // Sauvegarder dans sessionStorage
    sessionStorage.setItem('etatcaisse_selections', JSON.stringify(allSelections));

    // Compter le total de toutes les sélections
    const selectedIds = Object.keys(allSelections);
    const count = selectedIds.length;
    
    if (count > 0) {
        bar.classList.remove('hidden');
        
        // Calculer le total
        let total = 0;
        selectedEtatIds = [];
        selectedIds.forEach(id => {
            total += allSelections[id];
            selectedEtatIds.push(id);
        });

        document.getElementById('selectedCount').textContent = count;
        document.getElementById('selectedTotal').textContent = new Intl.NumberFormat('fr-FR').format(total);
        
        // Afficher l'indicateur multi-pages si des sélections existent hors de la page actuelle
        const currentPageIds = Array.from(currentPageCheckboxes).map(cb => cb.dataset.etatId);
        const hasSelectionFromOtherPages = selectedIds.some(id => !currentPageIds.includes(id));
        const multiPageIndicator = document.getElementById('multiPageIndicator');
        
        if (hasSelectionFromOtherPages) {
            multiPageIndicator.classList.remove('hidden');
        } else {
            multiPageIndicator.classList.add('hidden');
        }
    } else {
        bar.classList.add('hidden');
        selectedEtatIds = [];
        const multiPageIndicator = document.getElementById('multiPageIndicator');
        if (multiPageIndicator) {
            multiPageIndicator.classList.add('hidden');
        }
    }

    // Gérer l'état du checkbox "Tout sélectionner"
    // Il doit rester coché seulement si TOUTES les parts de la page actuelle sont cochées
    const currentPageChecked = document.querySelectorAll('.part-medecin-checkbox:checked').length;
    const currentPageTotal = currentPageCheckboxes.length;
    
    // Vérifier si toutes les parts de la page actuelle sont cochées
    if (currentPageTotal > 0 && currentPageChecked === currentPageTotal) {
        selectAllCheckbox.checked = true;
    } else {
        selectAllCheckbox.checked = false;
    }
}

function toggleSelectAll(checkbox) {
    if (checkbox.checked) {
        // Lancer la sélection de toutes les pages
        selectAllPagesAsync(checkbox);
    } else {
        // Désélectionner seulement les checkboxes de la page actuelle
        const checkboxes = document.querySelectorAll('.part-medecin-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = false;
        });
        
        // Retirer toutes les sélections de cette page depuis sessionStorage
        const allSelections = JSON.parse(sessionStorage.getItem('etatcaisse_selections') || '{}');
        checkboxes.forEach(cb => {
            delete allSelections[cb.dataset.etatId];
        });
        sessionStorage.setItem('etatcaisse_selections', JSON.stringify(allSelections));
        
        // Mettre à jour l'affichage
        updateBulkSelection();
    }
}

async function selectAllPagesAsync(checkbox) {
    // Désactiver temporairement le checkbox pendant le chargement
    checkbox.disabled = true;
    
    try {
        // Construire l'URL avec les paramètres de filtre actuels
        const urlParams = new URLSearchParams(window.location.search);
        const baseApiUrl = '{{ route("etatcaisse.getNonValidatedIds") }}';
        const apiUrl = baseApiUrl + (urlParams.toString() ? '?' + urlParams.toString() : '');
        
        console.log('🔍 URL de base:', baseApiUrl);
        console.log('🔍 Paramètres:', urlParams.toString());
        console.log('🔍 URL complète:', apiUrl);
        
        // Récupérer tous les IDs non validés depuis l'API
        const response = await fetch(apiUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        });
        
        console.log('📡 Réponse HTTP:', response.status, response.statusText);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('❌ Erreur serveur:', errorText);
            throw new Error(`Erreur HTTP ${response.status}: ${response.statusText}`);
        }
        
        const allNonValidatedData = await response.json();
        console.log('✅ Données reçues:', allNonValidatedData);
        
        // Sauvegarder dans sessionStorage
        sessionStorage.setItem('etatcaisse_selections', JSON.stringify(allNonValidatedData));
        
        // Cocher les checkboxes de la page actuelle
        const checkboxes = document.querySelectorAll('.part-medecin-checkbox');
        checkboxes.forEach(cb => {
            if (allNonValidatedData[cb.dataset.etatId]) {
                cb.checked = true;
            }
        });
        
        // Mettre à jour l'affichage
        updateBulkSelection();
        
        // Réactiver le checkbox
        checkbox.disabled = false;
        
        // Afficher un message de confirmation
        const count = Object.keys(allNonValidatedData).length;
        showNotification(`✅ ${count} part(s) médecin sélectionnée(s) sur toutes les pages`, 'success');
        
    } catch (error) {
        console.error('Erreur lors de la sélection de toutes les pages:', error);
        console.error('Détails de l\'erreur:', error.message);
        checkbox.checked = false;
        checkbox.disabled = false;
        showNotification('❌ Erreur lors de la sélection: ' + error.message, 'error');
    }
}

// Fonction pour afficher une notification temporaire
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white font-medium transition-all duration-300 transform translate-x-0 ${
        type === 'success' ? 'bg-green-600' : type === 'error' ? 'bg-red-600' : 'bg-blue-600'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Animation d'entrée
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 10);
    
    // Supprimer après 3 secondes
    setTimeout(() => {
        notification.style.transform = 'translateX(150%)';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

function clearSelection() {
    // Décocher tous les checkboxes de la page actuelle
    const checkboxes = document.querySelectorAll('.part-medecin-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = false;
    });
    document.getElementById('selectAll').checked = false;
    
    // Vider complètement le sessionStorage
    sessionStorage.removeItem('etatcaisse_selections');
    
    // Mettre à jour l'affichage
    updateBulkSelection();
    
    // Notification
    showNotification('✅ Toutes les sélections ont été effacées', 'info');
}

// Restaurer les sélections au chargement de la page
function restoreSelections() {
    const savedSelections = JSON.parse(sessionStorage.getItem('etatcaisse_selections') || '{}');
    const savedIds = Object.keys(savedSelections);
    
    if (savedIds.length > 0) {
        // Cocher les checkboxes correspondantes sur cette page
        const checkboxes = document.querySelectorAll('.part-medecin-checkbox');
        checkboxes.forEach(cb => {
            if (savedIds.includes(cb.dataset.etatId)) {
                cb.checked = true;
            }
        });
        
        // Mettre à jour l'affichage
        updateBulkSelection();
    }
}

function validateBulkSelection() {
    if (selectedEtatIds.length === 0) {
        alert('Veuillez sélectionner au moins une part médecin à valider.');
        return;
    }

    isBulkValidation = true;
    
    // Récupérer toutes les sélections depuis sessionStorage
    const allSelections = JSON.parse(sessionStorage.getItem('etatcaisse_selections') || '{}');
    const selectedIds = Object.keys(allSelections);
    let total = 0;
    selectedIds.forEach(id => {
        total += allSelections[id];
    });

    // Mettre à jour le texte de description pour la validation en masse
    document.getElementById('modalDescription').textContent = `Sélectionnez le mode de paiement pour ${selectedIds.length} part(s) médecin`;

    // Mettre à jour le montant dans la modal
    document.getElementById('partMedecinAmount').textContent = new Intl.NumberFormat('fr-FR').format(total) + ' MRU';

    // Mettre à jour l'action du formulaire pour la validation en masse
    document.getElementById('paymentForm').action = '{{ route("etatcaisse.validerEnMasse") }}';

    // Afficher la modale
    const modal = document.getElementById('paymentModal');
    const content = document.getElementById('paymentModalContent');

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);

    document.body.style.overflow = 'hidden';
}

function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    const content = document.getElementById('paymentModalContent');

    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');

    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';

        // Reset form
        document.getElementById('paymentForm').reset();
        
        // Supprimer les champs cachés des IDs si présents
        const oldInputs = document.querySelectorAll('input[name^="etat_ids"]');
        oldInputs.forEach(input => input.remove());
        
        // Réinitialiser les variables
        isBulkValidation = false;
        currentEtatId = null;
        
        updatePaymentSelection();
    }, 300);
}

// Gestion de la sélection des options de paiement
document.addEventListener('DOMContentLoaded', function() {
    // Si un message de succès est présent (validation réussie), nettoyer les sélections
    @if(session('success'))
        sessionStorage.removeItem('etatcaisse_selections');
    @endif

    // Restaurer les sélections depuis sessionStorage au chargement de la page
    restoreSelections();

    // Attacher l'événement au checkbox "Tout sélectionner"
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            toggleSelectAll(this);
        });
    }

    const paymentOptions = document.querySelectorAll('.payment-option');
    const paymentForm = document.getElementById('paymentForm');

    paymentOptions.forEach(option => {
        option.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
            updatePaymentSelection();
        });
    });

    // Intercepter la soumission du formulaire pour ajouter les IDs en mode validation en masse
    paymentForm.addEventListener('submit', function(e) {
        if (isBulkValidation && selectedEtatIds.length > 0) {
            // Supprimer les anciens champs etat_ids s'ils existent
            const oldInputs = paymentForm.querySelectorAll('input[name^="etat_ids"]');
            oldInputs.forEach(input => input.remove());

            // Ajouter les IDs sélectionnés
            selectedEtatIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'etat_ids[]';
                input.value = id;
                paymentForm.appendChild(input);
            });
        }
    });

    // Fermer la modale en cliquant à l'extérieur
    document.getElementById('paymentModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closePaymentModal();
        }
    });

    // Fermer avec Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('paymentModal').classList.contains('hidden')) {
            closePaymentModal();
        }
    });
});

function updatePaymentSelection() {
    const paymentOptions = document.querySelectorAll('.payment-option');

    paymentOptions.forEach(option => {
        const radio = option.querySelector('input[type="radio"]');
        const check = option.querySelector('.payment-check');

        if (radio.checked) {
            option.classList.add('border-green-500', 'bg-green-50', 'dark:bg-green-900/20');
            option.classList.remove('border-gray-200', 'dark:border-gray-600');
            check.classList.remove('hidden');
        } else {
            option.classList.remove('border-green-500', 'bg-green-50', 'dark:bg-green-900/20');
            option.classList.add('border-gray-200', 'dark:border-gray-600');
            check.classList.add('hidden');
        }
    });
}
</script>

@endsection
