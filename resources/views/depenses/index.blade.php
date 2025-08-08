@extends('layouts.app')

@section('content')

<!-- Titre + Boutons -->
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 space-y-4 lg:space-y-0">
    <h1 class="page-title">Liste des Dépenses</h1>

    <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0 w-full lg:w-auto">
        <!-- Bouton Ajouter -->
        <a href="{{ route('depenses.create') }}"
            class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-4 py-2 rounded transition">
            + Ajouter une Dépense
        </a>

        <!-- Boutons Export/Print -->
        <a href="{{ route('depenses.exportPdf') }}"
            class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded transition">PDF</a>
        <a href="{{ route('depenses.print') }}" target="_blank"
            class="bg-green-500 hover:bg-green-600 text-white text-sm px-4 py-2 rounded transition">Imprimer</a>
    </div>
</div>

<!-- Filtres -->
<div class="card mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            <div class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Total Salaires Bruts
                (Mois en cours)</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{
                number_format(\App\Models\Personnel::sum('salaire'), 0, ',', ' ') }} MRU</div>
        </div>
        <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            <div class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Crédits Personnel
                (Restants)</div>
            <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{
                number_format(\App\Models\Credit::where('source_type', App\Models\Personnel::class)->sum('montant') -
                \App\Models\Credit::where('source_type', App\Models\Personnel::class)->sum('montant_paye'), 0, ',', ' ')
                }} MRU</div>
        </div>
        <div class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
            <div class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Total Salaires Nets
                (Brut - Crédits)</div>
            @php
            $totalBrut = \App\Models\Personnel::sum('salaire');
            $creditsRestants = \App\Models\Credit::where('source_type', App\Models\Personnel::class)->sum('montant') -
            \App\Models\Credit::where('source_type', App\Models\Personnel::class)->sum('montant_paye');
            $totalNet = max($totalBrut - $creditsRestants, 0);
            @endphp
            <div class="text-2xl font-bold text-green-700 dark:text-green-400">{{ number_format($totalNet, 0, ',', ' ')
                }} MRU</div>
        </div>
        <div
            class="p-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex items-center justify-between">
            <div>
                <div class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Salaire</div>
                <div class="text-sm text-gray-700 dark:text-gray-300">Payer les salaires (redirige vers les crédits)
                </div>
            </div>
            <a href="{{ route('credits.index') }}"
                class="inline-flex items-center px-3 py-2 rounded-lg bg-purple-600 hover:bg-purple-700 text-white text-sm transition">
                <i class="fas fa-file-invoice-dollar mr-2"></i>Payer les salaires
            </a>
        </div>
    </div>
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
    @endphp
    @if($summary)
    <div class="mb-4 flex items-center gap-3">
        <span
            class="inline-block bg-blue-100 dark:bg-blue-900/20 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full text-sm font-medium transition">{{
            $summary }}</span>
        <a href="{{ route('depenses.index') }}" class="form-button form-button-secondary text-xs">Réinitialiser</a>
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
        <a href="{{ route('depenses.index') }}" class="ml-2 text-sm text-gray-600 dark:text-gray-400 underline">Afficher
            tous</a>
    </form>

    <!-- Filtres supplémentaires -->
    <form method="GET" action="" class="flex flex-wrap gap-2 items-center mt-4" autocomplete="off">
        <label for="mode_paiement" class="text-sm font-medium text-gray-700 dark:text-gray-300">Mode de paiement
            :</label>
        <select name="mode_paiement" id="mode_paiement" class="form-select text-sm" onchange="this.form.submit()">
            <option value="">-- Tous --</option>
            <option value="espèces" {{ request('mode_paiement')=='espèces' ? 'selected' : '' }}>Espèces</option>
            <option value="bankily" {{ request('mode_paiement')=='bankily' ? 'selected' : '' }}>Bankily</option>
            <option value="masrivi" {{ request('mode_paiement')=='masrivi' ? 'selected' : '' }}>Masrivi</option>
            <option value="sedad" {{ request('mode_paiement')=='sedad' ? 'selected' : '' }}>Sedad</option>

        </select>

        <label for="source" class="text-sm font-medium text-gray-700 dark:text-gray-300 ml-4">Source :</label>
        <select name="source" id="source" class="form-select text-sm" onchange="this.form.submit()">
            <option value="">-- Toutes --</option>
            <option value="manuelle" {{ request('source')=='manuelle' ? 'selected' : '' }}>Manuelle</option>
            <option value="automatique" {{ request('source')=='automatique' ? 'selected' : '' }}>Automatique</option>
            <option value="part_medecin" {{ request('source')=='part_medecin' ? 'selected' : '' }}>Part médecin</option>

        </select>
    </form>
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
</div>

<!-- Tableau -->
<div class="table-container">
    <table class="table-main">
        <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
            <tr>
                <th class="table-header">Nom</th>
                <th class="table-header">Montant</th>
                <th class="table-header">Mode de paiement</th>
                <th class="table-header">Source</th>
                <th class="table-header">Date</th>
                <th class="table-header">Actions</th>
            </tr>
        </thead>
        <tbody class="table-body">
            @foreach($depenses as $depense)
            <tr class="table-row hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="table-cell">
                    <div class="font-medium">{{ $depense->nom }}</div>
                    @if(isset($depense->etat_caisse_id) && $depense->etat_caisse_id)
                    <div class="text-xs text-gray-500 dark:text-gray-400">État caisse #{{ $depense->etat_caisse_id }}
                    </div>
                    @endif
                    @if(isset($depense->is_credit_personnel) && $depense->is_credit_personnel)
                    <div class="text-xs text-green-600 dark:text-green-400 font-semibold">Dépense crédit
                        personnel</div>
                    @endif
                </td>
                <td class="table-cell">
                    <span class="font-bold text-red-600 dark:text-red-400">{{ number_format($depense->montant, 0, ',', '
                        ') }} MRU</span>
                </td>
                <td class="table-cell">
                    @if($depense->mode_paiement_id === 'salaire')
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300">
                        Déduction salariale
                    </span>
                    @else
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                        {{ ucfirst($depense->mode_paiement_id ?? 'Non défini') }}
                    </span>
                    @endif
                </td>
                <td class="table-cell">
                    @if($depense->mode_paiement_id === 'salaire')
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300">
                        Déduction salariale
                    </span>
                    @elseif(str_contains($depense->nom, 'Part médecin'))
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                        Part médecin
                    </span>
                    @elseif($depense->source === 'automatique')
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                        Généré automatiquement
                    </span>
                    @else
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                        {{ ucfirst($depense->source ?? 'Manuelle') }}
                    </span>
                    @endif
                </td>
                <td class="table-cell text-gray-600 dark:text-gray-400">
                    {{ $depense->created_at->diffForHumans() }}
                </td>
                <td class="table-cell">
                    @if(empty($depense->is_credit_personnel))
                    <div class="flex space-x-2">
                        <!-- Voir -->
                        <a href="{{ route('depenses.show', $depense->id) }}"
                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1">
                            <i class="fas fa-eye"></i>
                        </a>
                        <!-- Modifier -->
                        <a href="{{ route('depenses.edit', $depense->id) }}"
                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 p-1">
                            <i class="fas fa-edit"></i>
                        </a>
                        <!-- Supprimer -->
                        <form action="{{ route('depenses.destroy', $depense->id) }}" method="POST"
                            onsubmit="return confirm('Supprimer cette dépense ?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="pagination-container">
    {{ $depenses->links() }}
</div>

@endsection
