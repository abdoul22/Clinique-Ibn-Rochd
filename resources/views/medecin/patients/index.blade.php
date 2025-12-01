@extends('layouts.app')
@section('title', 'Mes Patients')

@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <!-- En-tête -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-users text-yellow-600 mr-2"></i>Mes Patients
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    Liste des patients ayant des caisses associées à votre compte
                </p>
            </div>
            <a href="{{ route('medecin.dashboard') }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                <i class="fas fa-arrow-left mr-2"></i>Retour au Dashboard
            </a>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow">
            <div class="flex items-center">
                <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded-full mr-3">
                    <i class="fas fa-users text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Patients</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_patients'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow">
            <div class="flex items-center">
                <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full mr-3">
                    <i class="fas fa-clipboard-list text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Caisses</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_consultations'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow">
            <div class="flex items-center">
                <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full mr-3">
                    <i class="fas fa-user-check text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Patients Actifs (6 mois)</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['patients_actifs'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Barre de Recherche et Filtres -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('medecin.patients.index') }}" class="flex flex-col lg:flex-row gap-3">
            <!-- Recherche -->
            <div class="flex-grow">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors shadow-sm"
                           placeholder="Rechercher un patient (Nom, Téléphone)...">
                </div>
            </div>

            <!-- Filtre par Période -->
            <div>
                <select name="periode" 
                        class="block w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                    <option value="">Toutes les périodes</option>
                    <option value="aujourdhui" {{ request('periode') == 'aujourdhui' ? 'selected' : '' }}>Aujourd'hui</option>
                    <option value="semaine" {{ request('periode') == 'semaine' ? 'selected' : '' }}>Cette semaine</option>
                    <option value="mois" {{ request('periode') == 'mois' ? 'selected' : '' }}>Ce mois</option>
                    <option value="3mois" {{ request('periode') == '3mois' ? 'selected' : '' }}>3 derniers mois</option>
                    <option value="6mois" {{ request('periode') == '6mois' ? 'selected' : '' }}>6 derniers mois</option>
                    <option value="annee" {{ request('periode') == 'annee' ? 'selected' : '' }}>Cette année</option>
                </select>
            </div>

            <!-- Boutons -->
            <div class="flex gap-2">
                <button type="submit" 
                        class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-all shadow-sm flex-shrink-0">
                    <i class="fas fa-filter mr-2"></i>Filtrer
                </button>
                
                @if(request()->has('search') || request()->has('periode'))
                <a href="{{ route('medecin.patients.index') }}" 
                   class="px-4 py-2.5 bg-gray-500 text-white border border-gray-600 font-medium rounded-lg hover:bg-gray-600 transition-all flex-shrink-0 flex items-center">
                    <i class="fas fa-times mr-2"></i>Réinitialiser
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Boutons d'Export -->
    <div class="flex justify-end gap-3 mb-4">
        <!-- Export PDF -->
        <a href="{{ route('medecin.patients.export.pdf', request()->query()) }}" 
           class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition shadow">
            <i class="fas fa-file-pdf mr-2"></i>Exporter PDF
        </a>
        
        <!-- Imprimer -->
        <button onclick="window.print()" 
                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition shadow">
            <i class="fas fa-print mr-2"></i>Imprimer
        </button>
    </div>

    <!-- Tableau des patients -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Patient
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Contact
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Dernière Caisse
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Nb Caisses
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($patients as $patient)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center text-white font-bold text-sm shadow">
                                        {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $patient->first_name }} {{ $patient->last_name }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        @if($patient->date_naissance)
                                            {{ \Carbon\Carbon::parse($patient->date_naissance)->age }} ans
                                        @else
                                            Âge non renseigné
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-white">
                                <i class="fas fa-phone text-gray-400 mr-1"></i>
                                {{ $patient->phone ?? 'N/A' }}
                            </div>
                            @if($patient->email)
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                <i class="fas fa-envelope text-gray-400 mr-1"></i>
                                {{ $patient->email }}
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($patient->caisses->count() > 0)
                                @php
                                    $lastCaisse = $patient->caisses->first();
                                @endphp
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $lastCaisse->date_examen->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $lastCaisse->date_examen->diffForHumans() }}
                                </div>
                            @else
                                <span class="text-sm text-gray-400">Aucune caisse</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                <i class="fas fa-clipboard-list mr-1"></i>
                                {{ $patient->caisses_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <!-- Voir le dossier patient -->
                            <a href="{{ route('medecin.patients.show', $patient->id) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                <i class="fas fa-eye mr-1"></i>
                                Voir
                            </a>
                            
                            <!-- Nouvelle consultation pour ce patient -->
                            <a href="{{ route('medecin.consultations.create', ['patient_id' => $patient->id]) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                <i class="fas fa-plus mr-1"></i>
                                Consulter
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-users text-gray-400 text-3xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                    Aucun patient trouvé
                                </h3>
                                <p class="text-gray-500 dark:text-gray-400 mb-4">
                                    Vous n'avez pas encore de patients avec des caisses associées.
                                </p>
                                <a href="{{ route('medecin.consultations.create') }}" 
                                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                    <i class="fas fa-plus mr-2"></i>Créer une consultation
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($patients->hasPages())
        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
            {{ $patients->links() }}
        </div>
        @endif
    </div>

    <!-- Graphiques de Suivi -->
    @if($patients->count() > 0)
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6 no-print">
        <!-- Graphique : Consultations par patient (Top 10) -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                Top 10 Patients (Nb Caisses)
            </h3>
            <canvas id="chartConsultations"></canvas>
        </div>

        <!-- Graphique : Évolution des consultations -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-chart-line text-green-600 mr-2"></i>
                Répartition par Activité
            </h3>
            <canvas id="chartActivite"></canvas>
        </div>
    </div>
    @endif
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    @if($patients->count() > 0)
    // Données pour le graphique caisses
    const topPatients = @json($patients->take(10)->map(function($patient) {
        return [
            'name' => $patient->first_name . ' ' . $patient->last_name,
            'count' => $patient->caisses_count
        ];
    }));

    // Graphique : Top 10 Caisses
    const ctxConsultations = document.getElementById('chartConsultations').getContext('2d');
    new Chart(ctxConsultations, {
        type: 'bar',
        data: {
            labels: topPatients.map(p => p.name),
            datasets: [{
                label: 'Nombre de Caisses',
                data: topPatients.map(p => p.count),
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Graphique : Répartition par Activité
    const totalPatients = {{ $stats['total_patients'] }};
    const patientsActifs = {{ $stats['patients_actifs'] }};
    const patientsInactifs = totalPatients - patientsActifs;

    const ctxActivite = document.getElementById('chartActivite').getContext('2d');
    new Chart(ctxActivite, {
        type: 'doughnut',
        data: {
            labels: ['Patients Actifs (6 mois)', 'Patients Inactifs'],
            datasets: [{
                data: [patientsActifs, patientsInactifs],
                backgroundColor: [
                    'rgba(34, 197, 94, 0.7)',
                    'rgba(249, 115, 22, 0.7)'
                ],
                borderColor: [
                    'rgba(34, 197, 94, 1)',
                    'rgba(249, 115, 22, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    @endif
</script>

<!-- Styles pour l'impression -->
<style>
    @media print {
        /* Masquer les éléments non imprimables */
        .no-print,
        button,
        a[href*="export"],
        form,
        nav,
        header,
        footer,
        .pagination {
            display: none !important;
        }

        /* Optimiser pour l'impression */
        body {
            background: white;
            color: black;
        }

        .bg-white,
        .dark\\:bg-gray-800 {
            background: white !important;
        }

        .text-gray-900,
        .dark\\:text-white {
            color: black !important;
        }

        table {
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        thead {
            display: table-header-group;
        }

        /* Titre de la page */
        h1::before {
            content: "📋 LISTE DE MES PATIENTS - Dr. {{ $medecin->nom_complet_avec_prenom }}";
            display: block;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1e40af;
            padding-bottom: 10px;
        }
    }
</style>

@endsection

