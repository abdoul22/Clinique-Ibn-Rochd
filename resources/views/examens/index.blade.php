@extends('layouts.app')

@section('content')
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 space-y-4 lg:space-y-0">
    <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">Liste des examens</h1>
    <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0 w-full lg:w-auto">
        <!-- Bouton Ajouter -->
        <a href="{{ route('examens.create') }}"
            class="bg-cyan-600 dark:bg-cyan-800 hover:bg-cyan-700 dark:hover:bg-cyan-900 text-white text-sm px-4 py-2 rounded transition">
            + Ajouter un examen
        </a>
        <!-- Bouton PDF -->
        <a href="{{ route('examens.exportPdf') }}"
            class="bg-red-500 dark:bg-red-700 hover:bg-red-600 dark:hover:bg-red-900 text-white text-sm px-4 py-2 rounded flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Télécharger PDF
        </a>
        <!-- Bouton Impression -->
        <a href="{{ route('examens.print') }}" target="_blank"
            class="bg-gray-600 dark:bg-gray-800 hover:bg-gray-700 dark:hover:bg-gray-900 text-white text-sm px-4 py-2 rounded flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2h-2m-4 0h-4v4h4v-4z" />
            </svg>
            Imprimer
        </a>
    </div>
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
    <a href="{{ route('examens.index') }}" class="form-button form-button-secondary text-xs">Réinitialiser</a>
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
    <a href="{{ route('examens.index') }}" class="ml-2 text-sm text-gray-600 dark:text-gray-400 underline">Afficher
        tous</a>
</form>
<!-- Tableau -->
<div class="overflow-x-auto bg-white dark:bg-gray-800 rounded shadow dark:shadow-lg">
    <table class="min-w-full text-sm text-left">
        <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
            <tr>
                <th class="py-2 px-4">ID</th>
                <th class="py-2 px-4">Nom</th>
                <th class="py-2 px-4">Service</th>
                <th class="py-2 px-4">Tarif</th>
                <th class="py-2 px-4">Part Medecins</th>
                <th class="py-2 px-4">Part Cabinet</th>
                <th class="py-2 px-4">Créé le</th>
                <th class="py-2 px-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($examens as $examen)
            <tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900">
                <td class="py-2 px-4 text-gray-900 dark:text-gray-100">{{ $examen->id }}</td>
                <td class="py-2 px-4 text-gray-900 dark:text-gray-100">
                    @if($examen->service && $examen->service->type_service === 'PHARMACIE' &&
                    $examen->service->pharmacie)
                    <span class="font-semibold text-blue-600 dark:text-blue-400">{{ $examen->nom_affichage }}</span>
                    @else
                    {{ $examen->nom_affichage }}
                    @endif
                </td>
                <td class="py-2 px-4 text-gray-900 dark:text-gray-100">
                    @if($examen->service && $examen->service->type_service === 'PHARMACIE' &&
                    $examen->service->pharmacie)
                    <span class="font-semibold text-green-600 dark:text-green-400">{{ $examen->service_affichage
                        }}</span>
                    @else
                    {{ $examen->service_affichage }}
                    @endif
                </td>
                <td class="py-2 px-4 text-gray-900 dark:text-gray-100">{{ number_format($examen->tarif, 0, ',', ' ') }}
                    MRU</td>
                <td class="py-2 px-4 text-gray-900 dark:text-gray-100">{{ number_format($examen->part_medecin, 0, ',', '
                    ') }} MRU</td>
                <td class="py-2 px-4 text-gray-900 dark:text-gray-100">{{ number_format($examen->part_cabinet, 0, ',', '
                    ') }} MRU</td>
                <td class="py-2 px-4">
                    <span
                        class="inline-flex items-center gap-1 px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-medium">
                        <svg class="w-4 h-4 text-blue-500 mr-1" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ \Carbon\Carbon::parse($examen->created_at)->translatedFormat('d F Y') }}
                    </span>
                </td>
                <td class="py-2 px-4">
                    <div class="flex space-x-2">
                        <!-- Voir -->
                        <a href="{{ route(auth()->user()->role->name . '.examens.show', $examen->id) }}"
                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1">
                            <i class="fas fa-eye"></i>
                        </a>

                        <!-- Modifier -->
                        <a href="{{ route(auth()->user()->role->name . '.examens.edit', $examen->id) }}"
                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 p-1">
                            <i class="fas fa-edit"></i>
                        </a>

                        <!-- Supprimer -->
                        <form action="{{ route(auth()->user()->role->name . '.examens.destroy', $examen->id) }}"
                            method="POST" onsubmit="return confirm('Êtes-vous sûr ?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<!-- Pagination -->
<div class="py-4">
    {{ $examens->links() }}
</div>
@endsection
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