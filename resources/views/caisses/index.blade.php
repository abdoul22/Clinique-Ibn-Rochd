@extends('layouts.app')
@section('title', 'Liste des Caisses')

@section('content')
<!-- Titre + Boutons -->
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 space-y-4 lg:space-y-0">
    <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">Gestion des Factures</h1>

    <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0 w-full lg:w-auto">
        <!-- Bouton Ajouter -->
        <a href="{{ route('caisses.create') }}"
            class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-4 py-2 rounded transition flex items-center dark:bg-blue-700 dark:hover:bg-blue-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nouvel facture
        </a>

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
            <a href="{{ route('caisses.index') }}" class="form-button form-button-secondary text-xs">Réinitialiser</a>
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
            <a href="{{ route('caisses.index') }}"
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
    </div>
</div>

<!-- Tableau -->
<div class="overflow-x-auto bg-white dark:bg-gray-800 rounded shadow dark:shadow-lg">
    <table class="min-w-full text-sm text-left">
        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
            <tr>
                <th class="py-3 px-4">N° Entrée</th>
                <th class="py-3 px-4">Patient</th>
                <th class="py-3 px-4">Médecin</th>
                <th class="py-3 px-4">Date</th>
                <th class="py-3 px-4">Total</th>
                <th class="py-3 px-4">Caissier</th>
                <th class="py-3 px-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($caisses as $caisse)
            <tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900">
                <td class="py-3 px-4 font-medium">{{ $caisse->numero_entre }}</td>
                <td class="py-3 px-4">{{ $caisse->patient->first_name ?? 'N/A' }} {{ $caisse->patient->last_name ?? ''
                    }}</td>
                <td class="py-3 px-4">{{ $caisse->medecin->nom ?? 'N/A' }}</td>
                <td class="py-3 px-4">{{ $caisse->created_at->setTimezone('Africa/Nouakchott')->diffForHumans() }}</td>
                <td class="py-3 px-4">{{ number_format($caisse->total, 2) }} MRU</td>
                <td class="py-3 px-4">{{ $caisse->nom_caissier }}</td>
                <td class="py-3 px-4">
                    <div class="flex space-x-2">
                        <!-- Voir -->
                        <a href="{{ route(auth()->user()->role->name . '.caisses.show', $caisse->id) }}"
                            class="text-blue-500 hover:text-blue-700 p-1 rounded-full hover:bg-blue-50 dark:hover:bg-blue-900/40"
                            title="Voir détails">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>

                        <!-- Modifier -->
                        <a href="{{ route(auth()->user()->role->name . '.caisses.edit', $caisse->id) }}"
                            class="text-yellow-500 hover:text-yellow-700 p-1 rounded-full hover:bg-yellow-50 dark:hover:bg-yellow-900/40"
                            title="Modifier">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>

                        <!-- Supprimer -->
                        <form action="{{ route(auth()->user()->role->name . '.caisses.destroy', $caisse->id) }}"
                            method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet examen ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-50 dark:hover:bg-red-900/40"
                                title="Supprimer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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
<div class="py-4">
    {{ $caisses->links() }}
</div>
@endsection