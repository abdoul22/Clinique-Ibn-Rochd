@extends('layouts.app')
@section('title', 'Récapitulatif des Opérateurs')

@section('content')
<!-- Titre + Boutons -->
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 space-y-4 lg:space-y-0">
    <h1 class="page-title">Récapitulatif des Opérateurs</h1>
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
    @php
    $role = auth()->user()->role->name;
    $resetRoute = ($role === 'superadmin' || $role === 'admin') ? route($role . '.recap-operateurs.index') :
    route('recap-operateurs.index');
    @endphp
    <a href="{{ $resetRoute }}" class="form-button form-button-secondary text-xs">Réinitialiser</a>
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
    @php
    $role = auth()->user()->role->name;
    $showAllRoute = ($role === 'superadmin' || $role === 'admin') ? route($role . '.recap-operateurs.index') :
    route('recap-operateurs.index');
    @endphp
    <a href="{{ $showAllRoute }}" class="ml-2 text-sm text-gray-600 dark:text-gray-400 underline">Afficher
        tous</a>
</form>

<!-- Filtres supplémentaires -->
<div class="card mb-4">
    <form method="GET" action="" class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0">
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

        <!-- Filtre par médecin -->
        <div class="flex items-center space-x-2">
            <label for="medecin_id" class="text-sm font-medium text-gray-700 dark:text-gray-300">Médecin :</label>
            <select name="medecin_id" id="medecin_id" class="form-select text-sm" onchange="this.form.submit()">
                <option value="">Tous les médecins</option>
                @foreach($medecins as $medecin)
                <option value="{{ $medecin->id }}" {{ request('medecin_id')==$medecin->id ? 'selected' : '' }}>
                    {{ $medecin->nom }}
                </option>
                @endforeach
            </select>
        </div>

        <!-- Filtre par examen -->
        <div class="flex items-center space-x-2">
            <label for="examen_id" class="text-sm font-medium text-gray-700 dark:text-gray-300">Examen :</label>
            <select name="examen_id" id="examen_id" class="form-select text-sm" onchange="this.form.submit()">
                <option value="">Tous les examens</option>
                @foreach($examens as $examen)
                <option value="{{ $examen->id }}" {{ request('examen_id')==$examen->id ? 'selected' : '' }}>
                    {{ $examen->nom }}
                </option>
                @endforeach
            </select>
        </div>

        <!-- Boutons Export -->
        @php
        $role = auth()->user()->role->name;
        $exportPdfRoute = ($role === 'superadmin' || $role === 'admin') ? route($role . '.recap-operateurs.exportPdf',
        request()->query()) : route('recap-operateurs.exportPdf', request()->query());
        $printRoute = ($role === 'superadmin' || $role === 'admin') ? route($role . '.recap-operateurs.print',
        request()->query()) : route('recap-operateurs.print', request()->query());
        @endphp
        <a href="{{ $exportPdfRoute }}"
            class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded transition">PDF</a>
        <a href="{{ $printRoute }}" target="_blank"
            class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2 rounded transition">Imprimer</a>
    </form>
</div>

<!-- Résumé des totaux -->
@if($resume)
<div
    class="alert alert-info my-6 rounded-xl shadow-lg border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/10">
    <h2 class="text-blue-700 dark:text-blue-300 font-semibold mb-4 text-lg flex items-center gap-2">
        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
        </svg>
        Résumé des données filtrées
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 my-4">
        <div class="card text-sm flex flex-col items-center">
            <span class="font-bold text-gray-700 dark:text-gray-300 mb-1">Total Examens</span>
            <span class="text-blue-700 dark:text-blue-400 text-xl font-bold">{{ number_format($resume['total_examens'],
                0,
                ',', ' ') }}</span>
        </div>
        <div class="card text-sm flex flex-col items-center">
            <span class="font-bold text-gray-700 dark:text-gray-300 mb-1">Total Recettes</span>
            <span class="text-green-700 dark:text-green-400 text-xl font-bold">{{
                number_format($resume['total_recettes'], 0, ',', ' ') }} MRU</span>
        </div>
        <div class="card text-sm flex flex-col items-center">
            <span class="font-bold text-gray-700 dark:text-gray-300 mb-1">Part Médecin</span>
            <span class="text-purple-700 dark:text-purple-400 text-xl font-bold">{{
                number_format($resume['total_part_medecin'], 0, ',', ' ') }} MRU</span>
        </div>
        <div class="card text-sm flex flex-col items-center">
            <span class="font-bold text-gray-700 dark:text-gray-300 mb-1">Part Clinique</span>
            <span class="text-indigo-700 dark:text-indigo-400 text-xl font-bold">{{
                number_format($resume['total_part_clinique'], 0, ',', ' ') }} MRU</span>
        </div>
    </div>
</div>
@endif

<!-- Tableau -->
<div class="table-container">
    <table class="table-main border border-gray-200 dark:border-gray-700">
        <thead class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
            <tr>
                <th class="py-2 px-2 text-left font-semibold text-xs uppercase tracking-wider">ID</th>
                <th class="py-2 px-2 text-left font-semibold text-xs uppercase tracking-wider">Médecin</th>
                <th class="py-2 px-2 text-left font-semibold text-xs uppercase tracking-wider">Examen</th>
                <th class="py-2 px-2 text-left font-semibold text-xs uppercase tracking-wider">Nombre</th>
                <th class="py-2 px-2 text-left font-semibold text-xs uppercase tracking-wider">Tarif</th>
                <th class="py-2 px-2 text-left font-semibold text-xs uppercase tracking-wider">Recettes</th>
                <th class="py-2 px-2 text-left font-semibold text-xs uppercase tracking-wider">Part Médecin</th>
                <th class="py-2 px-2 text-left font-semibold text-xs uppercase tracking-wider">Part Clinique</th>
                <th class="py-2 px-2 text-left font-semibold text-xs uppercase tracking-wider">Date</th>
            </tr>
        </thead>
        <tbody class="table-body">
            @forelse($recapOperateurs as $recap)
            <tr class="table-row">
                <td class="table-cell py-2 px-2">{{ $loop->iteration }}</td>
                <td class="table-cell py-2 px-2">
                    @if($recap->medecin)
                    @if($recap->examen && $recap->examen->nom === 'Hospitalisation')
                    {{-- Pour les hospitalisations, afficher un lien vers les détails des médecins de la journée --}}
                    @php
                    $role = auth()->user()->role->name;
                    $routeName = ($role === 'superadmin' || $role === 'admin') ? $role .
                    '.hospitalisations.doctors.by-date' : 'hospitalisations.doctors.by-date';
                    @endphp
                    <a href="{{ route($routeName, $recap->jour ? \Carbon\Carbon::parse($recap->jour)->format('Y-m-d') : date('Y-m-d')) }}"
                        class="text-blue-600 dark:text-blue-400 hover:underline font-medium flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        Détails Médecins
                    </a>
                    @else
                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $recap->medecin->nom }}</span>
                    @endif
                    @else
                    <span class="text-gray-500 dark:text-gray-400">—</span>
                    @endif
                </td>
                <td class="table-cell py-2 px-2">
                    @if($recap->examen)
                    <span class="font-medium text-indigo-600 dark:text-indigo-400">{{ $recap->examen->nom }}</span>
                    @else
                    <span class="text-gray-500 dark:text-gray-400">—</span>
                    @endif
                </td>
                <td class="table-cell py-2 px-2">
                    <div class="flex items-center justify-center">
                        <span
                            class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-sm font-bold px-3 py-1 rounded-full">
                            {{ $recap->nombre }}
                        </span>
                    </div>
                </td>
                <td class="table-cell py-2 px-2">
                    <span class="font-mono text-sm">{{ number_format($recap->tarif, 0, ',', ' ') }} MRU</span>
                </td>
                <td class="table-cell py-2 px-2">
                    <div
                        class="flex items-center justify-between bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg p-2">
                        <div class="flex items-center space-x-1">
                            <div class="bg-emerald-500/10 p-1 rounded-md">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 text-emerald-600 dark:text-emerald-400" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="font-mono font-bold text-emerald-700 dark:text-emerald-400 text-sm">
                                {{ number_format($recap->recettes, 0, ',', ' ') }}
                            </div>
                        </div>
                        <span
                            class="bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 text-xs font-semibold px-2 py-0.5 rounded-full">
                            MRU
                        </span>
                    </div>
                </td>
                <td class="table-cell py-2 px-2">
                    <div
                        class="flex items-center justify-between bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-lg p-2">
                        <div class="flex items-center space-x-1">
                            <div class="bg-blue-500/10 p-1 rounded-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600 dark:text-blue-400"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="font-mono font-bold text-blue-700 dark:text-blue-400 text-sm">
                                {{ number_format($recap->part_medecin, 0, ',', ' ') }}
                            </div>
                        </div>
                        <span
                            class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-xs font-semibold px-2 py-0.5 rounded-full">
                            MRU
                        </span>
                    </div>
                </td>
                <td class="table-cell py-2 px-2">
                    <div
                        class="flex items-center justify-between bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-lg p-2">
                        <div class="flex items-center space-x-1">
                            <div class="bg-indigo-500/10 p-1 rounded-md">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 text-indigo-600 dark:text-indigo-400" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path
                                        d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                                </svg>
                            </div>
                            <div class="font-mono font-bold text-indigo-700 dark:text-indigo-400 text-sm">
                                {{ number_format($recap->part_clinique, 0, ',', ' ') }}
                            </div>
                        </div>
                        <span
                            class="bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300 text-xs font-semibold px-2 py-0.5 rounded-full">
                            MRU
                        </span>
                    </div>
                </td>
                <td class="table-cell py-2 px-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        {{ \Carbon\Carbon::parse($recap->jour)->format('d/m/Y') }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center text-gray-500 dark:text-gray-400 py-4">Aucun enregistrement trouvé.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="pagination-container">
    {{ $recapOperateurs->links() }}
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
@endpush

@endsection
