@extends('layouts.app')
@section('title', 'Détails Médecin - ' . $medecin->nom . ' ' . $medecin->prenom)

@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('superadmin.medical.recap-medecins.index') }}" 
               class="w-10 h-10 flex items-center justify-center bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $medecin->fonction }} {{ $medecin->nom }} {{ $medecin->prenom }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400">{{ $medecin->specialite }}</p>
            </div>
        </div>
    </div>

    <!-- Profile Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-start gap-6">
            <div class="w-24 h-24 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center text-gray-700 dark:text-gray-200 text-3xl font-bold">
                {{ strtoupper(substr($medecin->nom, 0, 1)) }}{{ strtoupper(substr($medecin->prenom, 0, 1)) }}
            </div>
            <div class="flex-1">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Fonction</p>
                        <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $medecin->fonction }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Spécialité</p>
                        <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $medecin->specialite }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Téléphone</p>
                        <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $medecin->telephone ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                        <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $medecin->user->email ?? $medecin->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Statut</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $medecin->statut == 'actif' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300' }}">
                            {{ ucfirst($medecin->statut) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-600 dark:text-gray-400">Rapports (Mois)</p>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-file-medical text-blue-600 dark:text-blue-400"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $rapportsMois ?? 0 }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-600 dark:text-gray-400">Ordonnances (Mois)</p>
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-prescription text-green-600 dark:text-green-400"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $ordonnancesMois->count() }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-600 dark:text-gray-400">Revenus (Mois)</p>
                <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-coins text-yellow-600 dark:text-yellow-400"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($revenusMois, 0) }} <span class="text-sm font-normal">MRU</span></p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-600 dark:text-gray-400">Patients (Total)</p>
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-injured text-purple-600 dark:text-purple-400"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $patientsTotal }}</p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Évolution mensuelle -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-chart-line mr-2"></i>Évolution des 6 Derniers Mois
            </h2>
            <div class="h-80">
                <canvas id="evolutionChart"></canvas>
            </div>
        </div>

        <!-- Stats Total -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-chart-pie mr-2"></i>Statistiques Totales
            </h2>
            <div class="space-y-4">
                <a href="{{ route('superadmin.medical.recap-medecins.consultations', $medecin->id) }}" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors cursor-pointer group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center group-hover:bg-blue-200 dark:group-hover:bg-blue-900/50 transition-colors">
                            <i class="fas fa-file-medical text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <span class="text-gray-900 dark:text-white font-medium group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Rapports</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $rapportsTotal ?? 0 }}</span>
                        <i class="fas fa-arrow-right text-gray-400 dark:text-gray-500 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors"></i>
                    </div>
                </a>

                <a href="{{ route('superadmin.medical.recap-medecins.ordonnances', $medecin->id) }}" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors cursor-pointer group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center group-hover:bg-green-200 dark:group-hover:bg-green-900/50 transition-colors">
                            <i class="fas fa-prescription-bottle text-green-600 dark:text-green-400"></i>
                        </div>
                        <span class="text-gray-900 dark:text-white font-medium group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">Ordonnances</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl font-bold text-gray-900 dark:text-white group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">{{ $ordonnancesTotal }}</span>
                        <i class="fas fa-arrow-right text-gray-400 dark:text-gray-500 group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors"></i>
                    </div>
                </a>

                <a href="{{ route('superadmin.medical.recap-medecins.patients', $medecin->id) }}" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors cursor-pointer group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center group-hover:bg-purple-200 dark:group-hover:bg-purple-900/50 transition-colors">
                            <i class="fas fa-users text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <span class="text-gray-900 dark:text-white font-medium group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">Patients</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-2xl font-bold text-gray-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">{{ $patientsTotal }}</span>
                        <i class="fas fa-arrow-right text-gray-400 dark:text-gray-500 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors"></i>
                    </div>
                </a>

                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-wallet text-yellow-600 dark:text-yellow-400"></i>
                        </div>
                        <span class="text-gray-900 dark:text-white font-medium">Revenus Total</span>
                    </div>
                    <span class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($revenusTotal, 0) }} MRU</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Derniers Rapports -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-history mr-2"></i>Derniers Rapports
            </h2>
            <div class="space-y-3">
                @forelse($dernieresConsultations as $consultation)
                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center justify-between mb-1">
                            <p class="font-semibold text-gray-900 dark:text-white">
                                {{ $consultation->patient->first_name }} {{ $consultation->patient->last_name }}
                            </p>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $consultation->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        @if($consultation->motif)
                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ $consultation->motif }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">Aucun rapport récent</p>
                @endforelse
            </div>
        </div>

        <!-- Dernières Ordonnances -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-history mr-2"></i>Dernières Ordonnances
            </h2>
            <div class="space-y-3">
                @forelse($dernieresOrdonnances as $ordonnance)
                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center justify-between mb-1">
                            <p class="font-semibold text-gray-900 dark:text-white">
                                {{ $ordonnance->patient->first_name }} {{ $ordonnance->patient->last_name }}
                            </p>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $ordonnance->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <i class="fas fa-pills mr-1"></i>{{ $ordonnance->medicaments->count() }} médicament(s)
                        </p>
                    </div>
                @empty
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">Aucune ordonnance récente</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Charts JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Configuration commune des graphiques
    Chart.defaults.font.family = "'Inter', sans-serif";
    
    // Detect dark mode
    const isDarkMode = document.documentElement.classList.contains('dark');
    const textColor = isDarkMode ? '#e5e7eb' : '#374151';
    const gridColor = isDarkMode ? '#374151' : '#e5e7eb';

    // Évolution Mensuelle (Line Chart avec deux axes Y)
    const evolutionCtx = document.getElementById('evolutionChart').getContext('2d');
    const evolutionChart = new Chart(evolutionCtx, {
        type: 'line',
        data: {
            labels: [
                @foreach($evolutionMensuelle as $mois)
                    "{{ $mois['mois'] }}",
                @endforeach
            ],
            datasets: [{
                label: 'Rapports',
                data: [
                    @foreach($evolutionMensuelle as $mois)
                        {{ $mois['rapports'] ?? 0 }},
                    @endforeach
                ],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#3b82f6',
                yAxisID: 'y',
            }, {
                label: 'Ordonnances',
                data: [
                    @foreach($evolutionMensuelle as $mois)
                        {{ $mois['ordonnances'] }},
                    @endforeach
                ],
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#10b981',
                yAxisID: 'y',
            }, {
                label: 'Revenus (MRU)',
                data: [
                    @foreach($evolutionMensuelle as $mois)
                        {{ $mois['revenus'] }},
                    @endforeach
                ],
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#f59e0b',
                yAxisID: 'y1',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        color: textColor,
                        font: {
                            size: 12,
                            weight: 600
                        },
                        padding: 15
                    }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Nombre',
                        color: textColor
                    },
                    grid: {
                        color: gridColor
                    },
                    ticks: {
                        color: textColor
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Revenus (MRU)',
                        color: textColor
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                    ticks: {
                        color: textColor
                    }
                },
                x: {
                    grid: {
                        color: gridColor,
                        display: true
                    },
                    ticks: {
                        color: textColor
                    }
                }
            }
        }
    });
</script>
@endsection

