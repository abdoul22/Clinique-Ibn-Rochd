@extends('layouts.app')

@section('content')
<!-- Titre + Boutons -->
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 space-y-4 lg:space-y-0">
    <h1 class="text-xl md:text-2xl font-bold">Liste des États de caisse</h1>

    <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0 w-full lg:w-auto">
        <a href="{{ route('etatcaisse.create') }}"
            class="bg-purple-800 hover:bg-purple-600 text-white text-sm px-4 py-2 rounded transition">Voir Un Etat</a>
        <!-- Boutons Export -->
        <a href="{{ route('etatcaisse.exportPdf') }}"
            class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded transition">PDF</a>
        <a href="{{ route('etatcaisse.print') }}" target="_blank"
            class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2 rounded transition">Imprimer</a>
    </div>
</div>
<!-- Filtres -->
<div class="bg-gray-100 p-4 rounded mb-4">
    <form action="{{ route('etatcaisse.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-sm font-medium">Date</label>
            <input type="date" name="date" value="{{ request('date') }}"
                class="rounded border-gray-300 px-3 py-2 text-sm w-full">
        </div>
        <div>
            <label class="block text-sm font-medium">Désignation</label>
            <input type="text" name="designation" value="{{ request('designation') }}"
                class="rounded border-gray-300 px-3 py-2 text-sm w-full" placeholder="Ex: Général, crédit...">
        </div>
        <div>
            <button type="submit"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">Filtrer</button>
        </div>
    </form>
</div>
<div class=" flex-1/6 items-center ">
    <!-- Boutons de génération -->
    <div class="flex flex-wrap gap-2 mb-4">
        <form action="{{ route('etatcaisse.generer.general') }}" method="POST">
            @csrf
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">Générer
                État
                Général</button>
        </form>

        <form action="{{ route('etatcaisse.generer.personnels') }}" method="POST">
            @csrf
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm">Générer
                tous les crédits du personnel</button>
        </form>
        <div class=" flex items-center">
            <!-- Select pour générer un personnel -->
            <form method="GET" class="mb-4 flex gap-4 items-end">
                <!-- Employé -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Employé</label>
                    <select name="personnel_id" class="border rounded px-3 py-2 w-60">
                        <option value="">Tous</option>
                        @foreach($personnels as $perso)
                        <option value="{{ $perso->id }}" {{ request('personnel_id')==$perso->id ? 'selected' : '' }}>
                            {{ $perso->nom }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filtrer</button>
                </div>
            </form>
            <div class=" display block">
                @if(request('personnel_id'))
                @php
                $employe = $personnels->where('id', request('personnel_id'))->first();
                @endphp
                <div class="mb-4 bg-gray-100 p-3 rounded text-sm text-gray-700">
                    Total des crédits de {{ $employe->nom }} :
                    <strong>{{ number_format($employe->credit, 2) }} MRU</strong>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>



<!-- Tableau -->
<div class="overflow-x-auto bg-white rounded shadow">
    <table class="min-w-full text-sm text-left border border-gray-200">
     <thead class="bg-gray-200 text-gray-700">
            <tr>
                <th class="py-2 px-4 border">ID</th>
                <th class="py-2 px-4 border">Désignation</th>
                <th class="py-2 px-4 border">Recette Caisse</th>
                <th class="py-2 px-4 border">Part Médecin</th>
                <th class="py-2 px-4 border">Part Clinique</th>
                <th class="py-2 px-4 border">Mode de Paiement</th>
                <th class="py-2 px-4 border">Personnel</th>
                <th class="py-2 px-4 border">Assurance</th>
                <th class="py-2 px-4 border">Médecin</th> {{-- ✅ nouvelle colonne --}}
                <th class="py-2 px-4 border">Actions</th>
            </tr>
        </thead>
        <tbody id="etatCaisseTableBody">
            @forelse($etatcaisses as $etat)
            <tr class="border-b">
                @include('etatcaisse.partials.row', ['etat' => $etat])
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center text-gray-500 py-4">Aucun état de caisse enregistré.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <!-- Résumé filtré -->
    @if(request('date'))
    <div class="bg-blue-50 border border-blue-200 rounded p-4 my-6">
        <h2 class="text-blue-700 font-semibold mb-2">Résumé pour la date : {{ request('date') }}</h2>
        <!-- Résumé des opérations financières -->
        <div class="grid md:grid-cols-3 gap-4 my-6 bg-gray-100 p-4 rounded">
            <div class="bg-white p-4 rounded shadow text-sm">
                <div class="font-bold text-gray-700">Recette Caisse</div>
                <div class="text-blue-700 text-lg">{{ number_format($recetteCaisse, 0, ',', ' ') }} MRU</div>
            </div>
            <div class="bg-white p-4 rounded shadow text-sm">
                <div class="font-bold text-gray-700">Part Médecin</div>
                <div class="text-purple-700 text-lg">{{ number_format($partMedecin, 0, ',', ' ') }} MRU</div>
            </div>
            <div class="bg-white p-4 rounded shadow text-sm">
                <div class="font-bold text-gray-700">Part Clinique</div>
                <div class="text-green-700 text-lg">{{ number_format($partCabinet, 0, ',', ' ') }} MRU</div>
            </div>
            <div class="bg-white p-4 rounded shadow text-sm">
                <div class="font-bold text-gray-700">Dépense</div>
                <div class="text-red-700 text-lg">{{ number_format($depense, 0, ',', ' ') }} MRU</div>
            </div>
            <div class="bg-white p-4 rounded shadow text-sm">
                <div class="font-bold text-gray-700">Crédit Personnel</div>
                <div class="text-indigo-700 text-lg">{{ number_format($creditPersonnel, 0, ',', ' ') }} MRU</div>
            </div>
            <div class="bg-white p-4 rounded shadow text-sm">
                <div class="font-bold text-gray-700">Assurances</div>
                <ul class="list-disc list-inside">
                    @foreach($assurances as $assurance)
                    <li>{{ $assurance->nom }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif
<!-- Pagination -->
<div class="py-4">
    {{ $etatcaisses->links() }}
</div>
    <!-- Résumé global -->
    <div class="bg-gray-50 border border-gray-300 rounded p-4 mt-4">
        <h2 class="text-gray-800 font-semibold mb-2">Résumé global (toutes dates)</h2>
        <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2 text-sm text-gray-700">
            <li>Recette Caisse : <strong>{{ number_format($resumeGlobal['recette'], 0, ',', ' ') }} MRU</strong></li>
            <li>Part Médecin : <strong>{{ number_format($resumeGlobal['part_medecin'], 0, ',', ' ') }} MRU</strong></li>
            <li>Part Clinique : <strong>{{ number_format($resumeGlobal['part_cabinet'], 0, ',', ' ') }} MRU</strong>
            </li>
            <li>Dépenses : <strong>{{ number_format($resumeGlobal['depense'], 0, ',', ' ') }} MRU</strong></li>
            <li>Crédit Personnel : <strong>0
                    MRU</strong>
            </li>
            <li>Crédit Assurance : <strong>{{ number_format($resumeGlobal['credit_assurance'], 0, ',', ' ') }}
                    MRU</strong>
            </li>
        </ul>
    </div>
    <div class="px-3 py-2 ">
        <!-- 📊 Graphique filtré -->
        @if(request('date'))
        <div class="mt-6">
            <h2 class="text-blue-700 font-semibold mb-2">Graphique - Résumé du {{ request('date') }}</h2>
            <canvas id="chartFiltre" height="100"></canvas>
        </div>
        @endif

        <!-- 📊 Graphique global -->
        <div class="mt-6">
            <h2 class="text-gray-800 font-semibold mb-2">Graphique - Résumé global (toutes dates)</h2>
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
</div>


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
