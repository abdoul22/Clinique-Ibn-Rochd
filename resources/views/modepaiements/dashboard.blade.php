@extends('layouts.app')

@section('content')
<div class="card mb-5">
    <div class="card-header">
        <div class="flex justify-between items-center">
            <h2 class="card-title">Mode des Paiements</h2>
            <a href="{{ route('modepaiements.historique') }}" class="form-button text-sm">
                Voir l'Historique
            </a>
        </div>
    </div>
    <div class="card-body">
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
        $summary = 'Filtré sur la semaine du ' . $start->translatedFormat('d F Y') . ' au ' . $end->translatedFormat('d
        F Y');
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
            <a href="{{ route('modepaiements.dashboard') }}"
                class="form-button form-button-secondary text-xs">Réinitialiser</a>
        </div>
        @endif
        <!-- Filtre avancé par période -->
        <form method="GET" action="" class="mb-6 flex flex-wrap gap-2 items-center" id="periode-filter-form"
            autocomplete="off">
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
                <input type="number" name="year" min="2000" max="2100" step="1" value="{{ request('year', date('Y')) }}"
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
            <a href="{{ route('modepaiements.dashboard') }}"
                class="ml-2 text-sm text-gray-600 dark:text-gray-400 underline">Afficher le total global</a>
        </form>
        <div class="space-y-5">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($data as $item)
                <div class="card p-4 bg-gradient-to-br from-gray-100 to-white dark:from-gray-800 dark:to-gray-700">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $item['mode'] }}</p>
                    <p class="text-sm text-green-600 dark:text-green-400">Entrée : {{ number_format($item['entree'], 0,
                        ',', ' ') }} MRU</p>
                    <p class="text-sm text-red-600 dark:text-red-400">Sortie : {{ number_format($item['sortie'], 0, ',',
                        ' ') }} MRU</p>
                    <p class="text-xl font-semibold text-gray-900 dark:text-gray-100 mt-1">Solde : {{
                        number_format($item['solde'], 0, ',', ' ') }} MRU</p>
                </div>
                @endforeach
                <div class="bg-lime-200 dark:bg-lime-800 rounded-xl p-4 col-span-2 md:col-span-1">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Trésorerie</p>
                    <p class="text-xl font-semibold text-gray-900 dark:text-gray-100 mt-1">{{
                        number_format($totalGlobal, 0, ',', ' ') }} MRU</p>
                </div>
            </div>
        </div>
        <!-- Chart.js -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">Statistiques graphiques</h3>
            <canvas id="paiementChart" height="120"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('paiementChart').getContext('2d');
        const paiementChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [
                    {
                        label: 'Entrée',
                        data: @json($chartEntrees),
                        backgroundColor: 'rgba(16, 185, 129, 0.6)', // vert
                    },
                    {
                        label: 'Sortie',
                        data: @json($chartSorties),
                        backgroundColor: 'rgba(239, 68, 68, 0.6)', // rouge
                    },
                    {
                        label: 'Solde',
                        data: @json($chartSoldes),
                        backgroundColor: 'rgba(59, 130, 246, 0.6)', // bleu
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#374151'
                        }
                    },
                    title: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#374151',
                            callback: function(value) {
                                return value.toLocaleString() + ' MRU';
                            }
                        },
                        grid: {
                            color: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb'
                        }
                    },
                    x: {
                        ticks: {
                            color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#374151'
                        },
                        grid: {
                            color: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb'
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
