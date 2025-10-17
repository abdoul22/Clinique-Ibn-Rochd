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
    if (request('source')) {
    $sourceLabels = [
    'facture' => 'Factures',
    'depense' => 'Dépenses',
    ];
    $summary .= ($summary ? ' • ' : '') . 'Source : ' . ($sourceLabels[request('source')] ?? request('source'));
    }
    @endphp
    @if($summary)
    <div class="mb-4 flex items-center gap-3">
        <div
            class="flex-1 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800 rounded-lg px-4 py-2.5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-blue-900 dark:text-blue-100">{{ $summary }}</span>
                </div>
                <a href="{{ route('modepaiements.index') }}"
                    class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium px-2 py-1 rounded hover:bg-blue-100 dark:hover:bg-blue-900/30 transition">
                    Réinitialiser
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Filtres Modernes -->
    <div
        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6 filters-card">
        <form method="GET" action="{{ route('modepaiements.index') }}" id="filter-form" autocomplete="off">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Filtre Période -->
                <div class="space-y-2">
                    <label for="period"
                        class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                        Période
                    </label>
                    <select name="period" id="period"
                        class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="">Toutes les dates</option>
                        <option value="day" {{ request('period')=='day' ? 'selected' : '' }}>Jour</option>
                        <option value="week" {{ request('period')=='week' ? 'selected' : '' }}>Semaine</option>
                        <option value="month" {{ request('period')=='month' ? 'selected' : '' }}>Mois</option>
                        <option value="year" {{ request('period')=='year' ? 'selected' : '' }}>Année</option>
                        <option value="range" {{ request('period')=='range' ? 'selected' : '' }}>Plage</option>
                    </select>
                </div>

                <!-- Filtre Type de paiement -->
                <div class="space-y-2">
                    <label for="type"
                        class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                        Type
                    </label>
                    <select name="type" id="type"
                        class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="">Tous les types</option>
                        @foreach($typesModes as $type)
                        <option value="{{ $type }}" {{ request('type')==$type ? 'selected' : '' }}>
                            {{ ucfirst($type) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtre Source -->
                <div class="space-y-2">
                    <label for="source"
                        class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">
                        Source
                    </label>
                    <select name="source" id="source"
                        class="w-full text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                        <option value="">Toutes les sources</option>
                        <option value="facture" {{ request('source')=='facture' ? 'selected' : '' }}>Factures</option>
                        <option value="depense" {{ request('source')=='depense' ? 'selected' : '' }}>Dépenses</option>
                    </select>
                </div>

                <!-- Boutons d'action -->
                <div class="space-y-2">
                    <label
                        class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide opacity-0">
                        Actions
                    </label>
                    <div class="flex gap-2">
                        <button type="submit" id="btn-filtrer"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors duration-200 shadow-sm hover:shadow">
                            Filtrer
                        </button>
                        <a href="{{ route('modepaiements.index') }}"
                            class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium py-2 px-3 rounded-lg transition-colors duration-200"
                            title="Réinitialiser">
                        </a>
                    </div>
                </div>
            </div>

            <!-- Inputs de période dynamiques -->
            <div class="mt-4 space-y-3">
                <div id="input-day"
                    class="period-input transition-all duration-300 {{ request('period') != 'day' ? 'hidden' : '' }}">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                    <input type="date" name="date" value="{{ request('date') }}"
                        class="w-full md:w-auto text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div id="input-week"
                    class="period-input transition-all duration-300 {{ request('period') != 'week' ? 'hidden' : '' }}">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Semaine</label>
                    <input type="week" name="week" value="{{ request('week') }}"
                        class="w-full md:w-auto text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div id="input-month"
                    class="period-input transition-all duration-300 {{ request('period') != 'month' ? 'hidden' : '' }}">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Mois</label>
                    <input type="month" name="month" value="{{ request('month') }}"
                        class="w-full md:w-auto text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div id="input-year"
                    class="period-input transition-all duration-300 {{ request('period') != 'year' ? 'hidden' : '' }}">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Année</label>
                    <input type="number" name="year" min="1900" max="2100" step="1"
                        value="{{ request('year', date('Y')) }}"
                        class="w-32 text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div id="input-range"
                    class="period-input transition-all duration-300 {{ request('period') != 'range' ? 'hidden' : '' }}">
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Plage de
                        dates</label>
                    <div class="flex flex-wrap gap-2 items-center">
                        <input type="date" name="date_start" value="{{ request('date_start') }}"
                            class="flex-1 min-w-[150px] text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <span class="text-gray-500 dark:text-gray-400 font-medium">→</span>
                        <input type="date" name="date_end" value="{{ request('date_end') }}"
                            class="flex-1 min-w-[150px] text-sm rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>
        </form>
    </div>
    @push('styles')
    <style>
        /* Supprimer toutes les icônes dans la carte des filtres si un style externe les agrandit */
        .filters-card svg {
            display: none !important;
        }
    </style>
    @endpush
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
                        @elseif($paiement->source === 'depense')
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                            Dépense
                        </span>
                        @elseif($paiement->source === 'part_medecin')
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                            Part médecin
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
    // Affichage dynamique des inputs selon la période
    function updatePeriodInputs() {
        const period = document.getElementById('period').value;
        document.querySelectorAll('.period-input').forEach(div => {
            div.classList.add('hidden');
            div.style.opacity = '0';
        });

        const targetInput = document.getElementById(`input-${period}`);
        if (targetInput) {
            targetInput.classList.remove('hidden');
            setTimeout(() => {
                targetInput.style.opacity = '1';
            }, 10);
        }

        updateFiltrerButtonState();
    }

    function updateFiltrerButtonState() {
        const period = document.getElementById('period').value;
        let valid = true;

        if (period === '') {
            valid = true;
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

        const btnFiltrer = document.getElementById('btn-filtrer');
        btnFiltrer.disabled = !valid;
        btnFiltrer.classList.toggle('opacity-50', !valid);
        btnFiltrer.classList.toggle('cursor-not-allowed', !valid);
    }

    // Initialisation
    document.getElementById('period').addEventListener('change', updatePeriodInputs);

    document.querySelectorAll('.period-input input').forEach(input => {
        input.addEventListener('input', updateFiltrerButtonState);
    });

    window.addEventListener('DOMContentLoaded', function() {
        updatePeriodInputs();
        updateFiltrerButtonState();
    });
</script>
@endpush

@endsection
