@extends('layouts.app')

@section('content')
<!-- Titre + Boutons -->
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 space-y-4 lg:space-y-0">
    <h1 class="page-title">Liste des États de caisse</h1>

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

<!-- Filtres -->
<div class="card mb-4">
    <form action="{{ route('etatcaisse.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
            <input type="date" name="date" value="{{ request('date') }}" class="form-input text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Désignation</label>
            <input type="text" name="designation" value="{{ request('designation') }}" class="form-input text-sm"
                placeholder="Ex: Général, crédit...">
        </div>
        <div>
            <button type="submit" class="form-button text-sm">Filtrer</button>
        </div>
    </form>
</div>

<div class="flex-1/6 items-center">
    <!-- Boutons de génération -->
    <div class="flex flex-wrap gap-2 mb-4">
        <form action="{{ route('etatcaisse.generer.general') }}" method="POST">
            @csrf
            <button type="submit" class="form-button text-sm">Générer État Général</button>
        </form>

        <form action="{{ route('etatcaisse.generer.personnels') }}" method="POST">
            @csrf
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm">Générer
                tous les crédits du personnel</button>
        </form>
        <div class="flex items-center">
            <!-- Select pour générer un personnel -->
            <form method="GET" class="mb-4 flex gap-4 items-end">
                <!-- Employé -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Employé</label>
                    <select name="personnel_id" class="form-select w-60">
                        <option value="">Tous</option>
                        @foreach($personnels as $perso)
                        <option value="{{ $perso->id }}" {{ request('personnel_id')==$perso->id ? 'selected' : '' }}>
                            {{ $perso->nom }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <button type="submit" class="form-button">Filtrer</button>
                </div>
            </form>
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
                <th class="py-4 px-4 text-left font-semibold text-xs uppercase tracking-wider">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z"
                                clip-rule="evenodd" />
                        </svg>
                        ID
                    </div>
                </th>
                <th class="py-4 px-4 text-left font-semibold text-xs uppercase tracking-wider">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        Désignation
                    </div>
                </th>
                <th class="py-4 px-4 text-left font-semibold text-xs uppercase tracking-wider">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path
                                d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                clip-rule="evenodd" />
                        </svg>
                        Recette
                    </div>
                </th>
                <th class="py-4 px-4 text-left font-semibold text-xs uppercase tracking-wider">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd" />
                        </svg>
                        Part Médecin
                    </div>
                </th>
                <th class="py-4 px-4 text-left font-semibold text-xs uppercase tracking-wider">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path
                                d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                        </svg>
                        Part Clinique
                    </div>
                </th>
                <th class="py-4 px-4 text-left font-semibold text-xs uppercase tracking-wider">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                            <path fill-rule="evenodd"
                                d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                                clip-rule="evenodd" />
                        </svg>
                        Paiement
                    </div>
                </th>
                <th class="py-4 px-4 text-left font-semibold text-xs uppercase tracking-wider">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                clip-rule="evenodd" />
                        </svg>
                        Validation
                    </div>
                </th>
                <th class="py-4 px-4 text-left font-semibold text-xs uppercase tracking-wider">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M5 5a3 3 0 015-2.236A3 3 0 0114.83 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.17C5.06 5.687 5 5.35 5 5zm4 1V5a1 1 0 10-1 1h1zm3 0a1 1 0 10-1-1v1h1z"
                                clip-rule="evenodd" />
                            <path d="M9 11H5v6a2 2 0 002 2h9a2 2 0 002-2v-6h-4z" />
                        </svg>
                        Assurance
                    </div>
                </th>
                <th class="py-4 px-4 text-left font-semibold text-xs uppercase tracking-wider">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path
                                d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                        </svg>
                        Médecin
                    </div>
                </th>
                <th class="py-4 px-4 text-left font-semibold text-xs uppercase tracking-wider">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path
                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Actions
                    </div>
                </th>
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

<!-- Résumé filtré -->
@if(request('date'))
<div class="alert alert-info my-6">
    <h2 class="text-blue-700 dark:text-blue-300 font-semibold mb-2">Résumé pour la date : {{ request('date') }}</h2>
    <!-- Résumé des opérations financières -->
    <div class="grid md:grid-cols-3 gap-4 my-6 bg-gray-100 dark:bg-gray-700 p-4 rounded">
        <div class="card text-sm">
            <div class="font-bold text-gray-700 dark:text-gray-300">Recette Caisse</div>
            <div class="text-blue-700 dark:text-blue-400 text-lg">{{ number_format($recetteCaisse, 0, ',', ' ') }} MRU
            </div>
        </div>
        <div class="card text-sm">
            <div class="font-bold text-gray-700 dark:text-gray-300">Part Médecin</div>
            <div class="text-purple-700 dark:text-purple-400 text-lg">{{ number_format($partMedecin, 0, ',', ' ') }} MRU
            </div>
        </div>
        <div class="card text-sm">
            <div class="font-bold text-gray-700 dark:text-gray-300">Part Clinique</div>
            <div class="text-green-700 dark:text-green-400 text-lg">{{ number_format($partCabinet, 0, ',', ' ') }} MRU
            </div>
        </div>
        <div class="card text-sm">
            <div class="font-bold text-gray-700 dark:text-gray-300">Dépense</div>
            <div class="text-red-700 dark:text-red-400 text-lg">{{ number_format($depense, 0, ',', ' ') }} MRU</div>
        </div>
        <div class="card text-sm">
            <div class="font-bold text-gray-700 dark:text-gray-300">Crédit Personnel</div>
            <div class="text-indigo-700 dark:text-indigo-400 text-lg">{{ number_format($creditPersonnel, 0, ',', ' ') }}
                MRU</div>
        </div>
        <div class="card text-sm">
            <div class="font-bold text-gray-700 dark:text-gray-300">Assurances</div>
            <ul class="list-disc list-inside text-gray-700 dark:text-gray-300">
                @foreach($assurances as $assurance)
                <li>
                    @if($assurance)
                    {{ $assurance->nom }}
                    @else
                    <span class="text-gray-400 dark:text-gray-500 italic">0 %</span>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

<!-- Pagination -->
<div class="pagination-container">
    {{ $etatcaisses->links() }}
</div>

<!-- Résumé global -->
<div class="card mt-4">
    <h2 class="card-title mb-2">Résumé global (toutes dates)</h2>
    <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 text-sm text-gray-700 dark:text-gray-300">
        <li>Recette Caisse : <strong class="text-blue-600 dark:text-blue-400">{{ number_format($resumeGlobal['recette'],
                0, ',', ' ') }} MRU</strong></li>
        <li>Part Médecin : <strong class="text-purple-600 dark:text-purple-400">{{
                number_format($resumeGlobal['part_medecin'], 0, ',', ' ') }} MRU</strong></li>
        <li>Part Clinique : <strong class="text-green-600 dark:text-green-400">{{
                number_format($resumeGlobal['part_cabinet'], 0, ',', ' ') }} MRU</strong></li>
        <li>Dépenses : <strong class="text-red-600 dark:text-red-400">{{ number_format($resumeGlobal['depense'], 0, ',',
                ' ') }} MRU</strong></li>
        <li>Crédit Personnel : <strong class="text-indigo-600 dark:text-indigo-400">{{
                number_format($resumeGlobal['credit_personnel'], 0, ',', ' ') }} MRU</strong></li>
        <li>Crédit Assurance : <strong class="text-cyan-600 dark:text-cyan-400">{{
                number_format($resumeGlobal['credit_assurance'], 0, ',', ' ') }} MRU</strong></li>
    </ul>
</div>

<div class="px-3 py-2">
    <!-- 📊 Graphique filtré -->
    @if(request('date'))
    <div class="mt-6">
        <h2 class="text-blue-700 dark:text-blue-300 font-semibold mb-2">Graphique - Résumé du {{ request('date') }}</h2>
        <canvas id="chartFiltre" height="100"></canvas>
    </div>
    @endif

    <!-- 📊 Graphique global -->
    <div class="mt-6">
        <h2 class="text-gray-800 dark:text-gray-200 font-semibold mb-2">Graphique - Résumé global (toutes dates)</h2>
        <canvas id="chartGlobal" height="100"></canvas>
    </div>
</div>

<script>
    window.onload = function () {
    const chartFiltreData = @json($chartFiltreData);
    const chartGlobalData = @json($chartGlobalData);

        const labels = ['Recette', 'Part Médecin', 'Part Clinique', 'Dépense', 'Crédit Personnel', 'Crédit Assurance'];
        const backgroundColors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#14b8a6'];

        if (chartFiltreData.length) {
            new Chart(document.getElementById('chartFiltre'), {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Montant (MRU)',
                        data: chartFiltreData,
                        backgroundColor: backgroundColors,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, title: { display: true, text: 'Montant (MRU)' } }
                    }
                }
            });
        }

       new Chart(document.getElementById('chartGlobal'), {
        type: 'bar',
        data: {
        labels: labels,
        datasets: [{
        label: 'Montant (MRU)',
        data: chartGlobalData,
        backgroundColor: backgroundColors,
        }]
        },
        options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
        y: { beginAtZero: true, title: { display: true, text: 'Montant (MRU)' } }
        }
        }
        });
    };
</script>

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

@endsection