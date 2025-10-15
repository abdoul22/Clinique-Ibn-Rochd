@extends('layouts.app')
@section('title', 'Liste des Paiements')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="page-title">Liste des Paiements</h2>
    </div>

    @if (session('success'))
    <div class="alert alert-success mb-4">
        {{ session('success') }}
    </div>
    @endif

    <!-- Résumé de la période sélectionnée -->
    @php
    $period = request('period');
    $summary = '';
    if ($period === 'day' && request('date')) {
    $summary = 'Filtré sur le jour du ' . \Carbon\Carbon::parse(request('date'))->translatedFormat('d F Y');
    } elseif ($period === 'week' && request('week')) {
    $parts = explode('-W', request('week'));
    if (count($parts) === 2) {
    $start = \Carbon\Carbon::now()->setISODate($parts[0], $parts[1])->startOfWeek();
    $end = \Carbon\Carbon::now()->setISODate($parts[0], $parts[1])->endOfWeek();
    $summary = 'Filtré sur la semaine du ' . $start->translatedFormat('d F Y') . ' au ' . $end->translatedFormat('d F
    Y');
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
    if (request('type')) {
    $summary .= ($summary ? ' • ' : '') . 'Type : ' . ucfirst(request('type'));
    }
    @endphp
    @if($summary)
    <div class="mb-4 flex items-center gap-3">
        <span
            class="inline-block bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full text-sm font-medium transition">{{
            $summary }}</span>
        <a href="{{ route('modepaiements.index') }}" class="form-button form-button-secondary text-xs">Réinitialiser</a>
    </div>
    @endif

    <!-- Filtres -->
    <div class="card mb-6">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Filtres</h3>

        <!-- Filtre par période -->
        <form method="GET" action="{{ route('modepaiements.index') }}" class="mb-4 flex flex-wrap gap-2 items-center"
            id="periode-filter-form" autocomplete="off">
            <!-- Préserver le filtre de type -->
            @if(request('type'))
            <input type="hidden" name="type" value="{{ request('type') }}">
            @endif

            <label for="period" class="text-sm font-medium text-gray-700 dark:text-gray-300">Période :</label>
            <select name="period" id="period" class="form-select text-sm" aria-label="Choisir la période">
                <option value="">Toutes les dates</option>
                <option value="day" {{ request('period')=='day' ? 'selected' : '' }}>Jour</option>
                <option value="week" {{ request('period')=='week' ? 'selected' : '' }}>Semaine</option>
                <option value="month" {{ request('period')=='month' ? 'selected' : '' }}>Mois</option>
                <option value="year" {{ request('period')=='year' ? 'selected' : '' }}>Année</option>
                <option value="range" {{ request('period')=='range' ? 'selected' : '' }}>Plage personnalisée</option>
            </select>
            <div id="input-day"
                class="period-input transition-all duration-300 {{ request('period') != 'day' ? 'hidden' : '' }}">
                <input type="date" name="date" value="{{ request('date') }}" class="form-input text-sm"
                    placeholder="Choisir une date" aria-label="Date du jour">
            </div>
            <div id="input-week"
                class="period-input transition-all duration-300 {{ request('period') != 'week' ? 'hidden' : '' }}">
                <input type="week" name="week" value="{{ request('week') }}" class="form-input text-sm"
                    placeholder="Choisir une semaine" aria-label="Semaine">
            </div>
            <div id="input-month"
                class="period-input transition-all duration-300 {{ request('period') != 'month' ? 'hidden' : '' }}">
                <input type="month" name="month" value="{{ request('month') }}" class="form-input text-sm"
                    placeholder="Choisir un mois" aria-label="Mois">
            </div>
            <div id="input-year"
                class="period-input transition-all duration-300 {{ request('period') != 'year' ? 'hidden' : '' }}">
                <input type="number" name="year" min="1900" max="2100" step="1" value="{{ request('year', date('Y')) }}"
                    class="form-input text-sm w-24" placeholder="Année" aria-label="Année">
            </div>
            <div id="input-range"
                class="period-input flex gap-2 items-center transition-all duration-300 {{ request('period') != 'range' ? 'hidden' : '' }}">
                <input type="date" name="date_start" value="{{ request('date_start') }}" class="form-input text-sm"
                    placeholder="Début" aria-label="Date de début">
                <span class="text-gray-500 dark:text-gray-400">à</span>
                <input type="date" name="date_end" value="{{ request('date_end') }}" class="form-input text-sm"
                    placeholder="Fin" aria-label="Date de fin">
            </div>
            <button type="submit" class="form-button text-sm" id="btn-filtrer">Filtrer</button>
            <a href="{{ route('modepaiements.index') }}"
                class="ml-2 text-sm text-gray-600 dark:text-gray-400 underline">Afficher tous</a>
        </form>

        <!-- Filtre par type de paiement -->
        <form method="GET" action="{{ route('modepaiements.index') }}" class="flex flex-wrap gap-2 items-center">
            <!-- Préserver les filtres de période -->
            @if(request('period'))
            <input type="hidden" name="period" value="{{ request('period') }}">
            @endif
            @if(request('date'))
            <input type="hidden" name="date" value="{{ request('date') }}">
            @endif
            @if(request('week'))
            <input type="hidden" name="week" value="{{ request('week') }}">
            @endif
            @if(request('month'))
            <input type="hidden" name="month" value="{{ request('month') }}">
            @endif
            @if(request('year'))
            <input type="hidden" name="year" value="{{ request('year') }}">
            @endif
            @if(request('date_start'))
            <input type="hidden" name="date_start" value="{{ request('date_start') }}">
            @endif
            @if(request('date_end'))
            <input type="hidden" name="date_end" value="{{ request('date_end') }}">
            @endif

            <label for="type" class="text-sm font-medium text-gray-700 dark:text-gray-300">Type de paiement :</label>
            <select name="type" id="type" class="form-select text-sm" onchange="this.form.submit()">
                <option value="">Tous les types</option>
                @foreach($typesModes as $type)
                <option value="{{ $type }}" {{ request('type')==$type ? 'selected' : '' }}>
                    {{ ucfirst($type) }}
                </option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="table-container">
        <table class="table-main">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="table-header">#</th>
                    <th class="table-header">Type</th>
                    <th class="table-header">Montant</th>
                    <th class="table-header">Facture Caisse</th>
                    <th class="table-header">Date</th>

                </tr>
            </thead>
            <tbody class="table-body">
                @forelse($paiements as $paiement)
                <tr class="table-row">
                    <td class="table-cell">{{ $paiement->id }}</td>
                    <td class="table-cell capitalize">{{ $paiement->type }}</td>
                    <td class="table-cell">{{ number_format($paiement->montant, 2, ',', ' ') }} MRU
                    </td>
                    <td class="table-cell">
                        @if ($paiement->caisse)
                        <a href="{{ route('caisses.show', $paiement->caisse_id) }}"
                            class="text-blue-600 dark:text-blue-400 hover:underline">
                            Facture n°{{ $paiement->caisse->id }}
                        </a>
                        @else
                        @if($paiement->source === 'credit_assurance')
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            Paiement crédit assurance
                        </span>
                        @else
                        <span class="text-gray-400 dark:text-gray-500 italic">Aucune</span>
                        @endif
                        @endif
                    </td>
                    <td class="table-cell">
                        {{ optional($paiement->created_at)->format('d/m/Y - H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="table-cell text-center text-gray-500 dark:text-gray-400">Aucun paiement
                        trouvé.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-container">
        {{ $paiements->links() }}
    </div>
</div>

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
        let valid = true;

        if (period === '') {
            valid = true; // Pas de période sélectionnée = afficher tout
        } else if (period === 'day') {
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
@endpush

@endsection
