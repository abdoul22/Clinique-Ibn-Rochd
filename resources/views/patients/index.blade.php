@extends('layouts.app')
@section('title', 'Liste des Patients')

@section('content')

<!-- Titre + Bouton Ajouter + Formulaire -->
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 space-y-4 lg:space-y-0">
    <h1 class="page-title">Liste des Patients</h1>

    <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0 w-full lg:w-auto">
        <!-- Bouton Ajouter -->
        <a href="{{ route(auth()->user()->role->name . '.patients.create') }}" class="form-button text-sm">
            + Ajouter un Patient
        </a>

        <!-- Formulaire de recherche -->
        <form method="GET" action="{{ route('patients.index') }}" class="flex flex-wrap gap-2 items-center">
            <!-- Recherche -->
            <input type="text" name="search" placeholder="Rechercher..." value="{{ request('search') }}"
                class="form-input text-sm w-full md:w-auto">

            <!-- Sexe -->
            <select name="sexe" class="form-select text-sm w-full md:w-auto">
                <option value="">Tous les sexes</option>
                <option value="Homme" {{ request('sexe')=='male' ? 'selected' : '' }}>Homme</option>
                <option value="Femme" {{ request('sexe')=='female' ? 'selected' : '' }}>Femme</option>
            </select>

            <!-- Type -->
            <select name="type_patient" class="form-select text-sm w-full md:w-auto">
                <option value="">Tous les types</option>
                <option value="Interne" {{ request('type_patient')=='Interne' ? 'selected' : '' }}>Interne</option>
                <option value="Externe" {{ request('type_patient')=='Externe' ? 'selected' : '' }}>Externe</option>
            </select>

            <!-- Bouton Filtrer -->
            <button type="submit" class="form-button text-sm">
                Filtrer
            </button>
        </form>
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
    <a href="{{ route('patients.index') }}" class="form-button form-button-secondary text-xs">Réinitialiser</a>
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
    <a href="{{ route('patients.index') }}" class="ml-2 text-sm text-gray-600 dark:text-gray-400 underline">Afficher
        tous</a>
</form>

<!-- Tableau -->
<div class="table-container">
    <table class="table-main">
        <thead class="table-header">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Sexe</th>
                <th>Date de naissance</th>
                <th>Téléphone</th>
                <th>Adresse</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody class="table-body">
            @foreach($patients as $patient)
            <tr class="table-row">
                <td class="table-cell">{{ $patient->id }}</td>
                <td class="table-cell-medium">{{ $patient->first_name }} {{ $patient->last_name }}</td>
                <td class="table-cell">{{ $patient->gender }}</td>
                <td class="table-cell">
                    <span
                        class="inline-flex items-center gap-1 px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-xs font-medium">
                        <svg class="w-4 h-4 text-blue-500 mr-1" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ \Carbon\Carbon::parse($patient->date_of_birth)->translatedFormat('d F Y') }}
                    </span>
                </td>
                <td class="table-cell">{{ $patient->phone }}</td>
                <td class="table-cell">{{ $patient->address }}</td>
                <td class="table-cell">
                    <div class="table-actions">
                        <!-- Voir -->
                        <a href="{{ route(auth()->user()->role->name . '.patients.show', $patient->id) }}"
                            class="action-btn action-btn-view">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.522 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S3.732 16.057 2.458 12z" />
                            </svg>
                        </a>

                        <!-- Modifier -->
                        <a href="{{ route(auth()->user()->role->name . '.patients.edit', $patient->id) }}"
                            class="action-btn action-btn-edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536M16.768 4.768a2 2 0 112.828 2.828l-9.192 9.192H7v-3.192l9.768-9.828z" />
                            </svg>
                        </a>

                        <!-- Supprimer -->
                        <form action="{{ route(auth()->user()->role->name . '.patients.destroy', $patient->id) }}"
                            method="POST" onsubmit="return confirm('Êtes-vous sûr ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn action-btn-delete">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22" />
                                </svg>
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
<div class="pagination-container">
    {{ $patients->links() }}
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
