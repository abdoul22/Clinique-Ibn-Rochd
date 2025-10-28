@extends('layouts.app')

@section('content')
<div class="card mb-5">
    <div class="card-header">
        <div class="flex justify-between items-center">
            <h2 class="card-title">Historique des Opérations</h2>
            <a href="{{ route('modepaiements.dashboard') }}"
                class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                ← Retour au Dashboard
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filtre avancé par période -->
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
            <a href="{{ route('modepaiements.historique') }}"
                class="form-button form-button-secondary text-xs">Réinitialiser</a>
        </div>
        @endif
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
            <a href="{{ route('modepaiements.historique') }}"
                class="ml-2 text-sm text-gray-600 dark:text-gray-400 underline">Afficher tous</a>
        </form>
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
        <!-- Fin filtre avancé -->

        <!-- Résumé des totaux -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-green-100 dark:bg-green-900/20 rounded-lg p-4">
                <h3 class="text-sm font-medium text-green-800 dark:text-green-200">Total Recettes</h3>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                    + {{ number_format($totalRecettesAvecCredits ?? $totalRecettes ?? 0, 0, ',', ' ') }} MRU
                </p>
            </div>
            <div class="bg-red-100 dark:bg-red-900/20 rounded-lg p-4">
                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Total Dépenses</h3>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                    - {{ number_format($totalDepenses ?? 0, 0, ',', ' ') }} MRU
                </p>
            </div>
            <div class="bg-blue-100 dark:bg-blue-900/20 rounded-lg p-4">
                <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">Solde Net</h3>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    {{ number_format($totalOperations ?? 0, 0, ',', ' ') }} MRU
                </p>
            </div>
        </div>

        <div class="table-container">
            <table class="table-main">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="table-header">
                            Date
                        </th>
                        <th class="table-header">
                            Type d'Opération
                        </th>
                        <th class="table-header">
                            Description
                        </th>
                        <th class="table-header">
                            Montant
                        </th>
                        <th class="table-header">
                            Mode de Paiement
                        </th>
                        <th class="table-header">
                            Source
                        </th>
                        <th class="table-header">
                            Type
                        </th>
                    </tr>
                </thead>
                <tbody class="table-body">
                    @forelse($historiquePaginated as $operation)
                    <tr class="table-row hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="table-cell">
                            {{ $operation['date'] ? $operation['date']->diffForHumans() : 'N/A' }}
                        </td>
                        <td class="table-cell">
                            @if($operation['type'] === 'recette' || $operation['type'] === 'paiement_credit_assurance')
                            Recette
                            @else
                            Dépense
                            @endif
                        </td>
                        <td class="table-cell">
                            {{ $operation['description'] }}
                        </td>
                        <td class="table-cell font-medium">
                            <span
                                class="{{ $operation['operation'] === 'entree' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $operation['operation'] === 'entree' ? '+' : '-' }}
                                {{ number_format($operation['montant'], 0, ',', ' ') }} MRU
                            </span>
                        </td>
                        <td class="table-cell">
                            {{ ucfirst($operation['mode_paiement']) }}
                        </td>
                        <td class="table-cell">
                            {{ ucfirst($operation['source']) }}
                        </td>
                        <td class="table-cell">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $operation['operation'] === 'entree' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' }}">
                                {{ $operation['operation'] === 'entree' ? 'Entrée' : 'Sortie' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="table-cell text-center text-gray-500 dark:text-gray-400">
                            Aucune opération trouvée.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            <div class="flex justify-center gap-2">
                <div class="sm:hidden">
                    {{ $historiquePaginated->appends(request()->query())->links('pagination::simple-tailwind') }}
                </div>
                <div class="hidden sm:block">
                    {{ $historiquePaginated->onEachSide(1)->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
