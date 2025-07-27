@extends('layouts.app')



@section('title', 'État de Caisse')

@section('content')
<!-- Titre + Boutons -->
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 space-y-4 lg:space-y-0">
    <h1 class="page-title">Liste des États de caisse</h1>


</div>

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
$summary = 'Filtré du ' . \Carbon\Carbon::parse(request('date_start'))->translatedFormat('d F Y') . ' au ' .
\Carbon\Carbon::parse(request('date_end'))->translatedFormat('d F Y');
}
@endphp
@if($summary)
<div class="mb-4 flex items-center gap-3">
    <span
        class="inline-block bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full text-sm font-medium transition">{{
        $summary }}</span>
    <a href="{{ route('etatcaisse.index') }}" class="form-button form-button-secondary text-xs">Réinitialiser</a>
</div>
@endif
<!-- Filtre avancé par période -->
<form method="GET" action="" class="mb-6 flex flex-wrap gap-2 items-center" id="periode-filter-form" autocomplete="off">
    <label for="period" class="text-sm font-medium text-gray-700 dark:text-gray-300">Période :</label>
    <select name="period" id="period" class="form-select text-sm" aria-label="Choisir la période">
        <option value="day" {{ request('period', 'day' )=='day' ? 'selected' : '' }}>Jour</option>
        <option value="week" {{ request('period')=='week' ? 'selected' : '' }}>Semaine</option>
        <option value="month" {{ request('period')=='month' ? 'selected' : '' }}>Mois</option>
        <option value="year" {{ request('period')=='year' ? 'selected' : '' }}>Année</option>
        <option value="range" {{ request('period')=='range' ? 'selected' : '' }}>Plage personnalisée</option>
    </select>
    <div id="input-day" class="period-input transition-all duration-300">
        <input type="date" name="date" value="{{ request('date') }}" class="form-input text-sm"
            placeholder="Choisir une date" aria-label="Date du jour">
    </div>
    <div id="input-week" class="period-input hidden transition-all duration-300">
        <input type="week" name="week" value="{{ request('week') }}" class="form-input text-sm"
            placeholder="Choisir une semaine" aria-label="Semaine">
    </div>
    <div id="input-month" class="period-input hidden transition-all duration-300">
        <input type="month" name="month" value="{{ request('month') }}" class="form-input text-sm"
            placeholder="Choisir un mois" aria-label="Mois">
    </div>
    <div id="input-year" class="period-input hidden transition-all duration-300">
        <input type="number" name="year" min="1900" max="2100" step="1" value="{{ request('year', date('Y')) }}"
            class="form-input text-sm w-24" placeholder="Année" aria-label="Année">
    </div>
    <div id="input-range" class="period-input hidden flex gap-2 items-center transition-all duration-300">
        <input type="date" name="date_start" value="{{ request('date_start') }}" class="form-input text-sm"
            placeholder="Début" aria-label="Date de début">
        <span class="text-gray-500 dark:text-gray-400">à</span>
        <input type="date" name="date_end" value="{{ request('date_end') }}" class="form-input text-sm"
            placeholder="Fin" aria-label="Date de fin">
    </div>
    <button type="submit" class="form-button text-sm" id="btn-filtrer">Filtrer</button>
    <a href="{{ route('etatcaisse.index') }}" class="ml-2 text-sm text-gray-600 dark:text-gray-400 underline">Afficher
        tous</a>
</form>

<!-- Filtres -->
<div class="card mb-4">
    <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0 w-full lg:w-auto">
        <a href="{{ route('modepaiements.dashboard') }}" class="form-button">
            Voir la trésorerie globale </a>
        <!-- Boutons Export -->
        <a href="{{ route('etatcaisse.exportPdf') }}"
            class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded transition">PDF</a>
        <a href="{{ route('etatcaisse.print') }}" target="_blank"
            class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2 rounded transition">Imprimer</a>
    </div>
</div>

<div class="flex-1/6 items-center">
    <!-- Boutons de génération -->
    <div class="flex flex-wrap gap-2 mb-4">



        <div class="flex items-center">

            <div class="display block">
                @if(request('personnel_id'))
                @php
                $employe = $personnels->where('id', request('personnel_id'))->first();
                @endphp
                <div class="mb-4 card">
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        Total des crédits de {{ $employe->nom }} :
                        <strong class="text-indigo-600 dark:text-indigo-400">{{ number_format($employe->credit, 2) }}
                            MRU</strong>
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Tableau -->
<div class="table-container">
    <table class="table-main border border-gray-200 dark:border-gray-700">
        <thead class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
            <tr>
                <th class="py-2 px-2 text-left font-semibold text-xs uppercase tracking-wider">ID</th>
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
                <td colspan="9" class="text-center text-gray-500 dark:text-gray-400 py-4">Aucun état de caisse
                    enregistré.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="pagination-container my-4">
    {{ $etatcaisses->links() }}
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
    document.getElementById('personnelSelect').addEventListener('change', function () {
        if (this.value) {
            const form = document.getElementById('personnelForm');
            form.action = `/etatcaisse/generer/personnel/${this.value}`;
            form.submit();
        }
    });
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
        let valid = false;
        if (period === 'day') {
            valid = !!document.querySelector('input[name="date"]').value;
        } else if (period === 'week') {
            valid = !!document.querySelector('input[name="week"]').value;
        } else if (period === 'month') {
            valid = !!document.querySelector('input[name="month"]').value;
        } else if (period === 'year') {
            valid = !!document.querySelector('input[name="year"]').value;
        } else if (period === 'range') {
            const start = document.querySelector('input[name="date_start"]').value;
            const end = document.querySelector('input[name="date_end"]').value;
            valid = !!start && !!end && start <= end;
        }
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

@endsection
