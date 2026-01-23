@extends('layouts.app')
@section('title', 'Récapitulatif Médecins')

@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                📊 Récapitulatif Médecins
            </h1>
            <p class="text-gray-600 dark:text-gray-400">Vue d'ensemble complète des activités médicales</p>
        </div>
        <a href="{{ route('superadmin.medical.recap-medecins.exportPdf') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded-lg hover:bg-gray-900 dark:hover:bg-gray-600 transition">
            <i class="fas fa-file-pdf"></i>
            Exporter PDF
        </a>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Consultations ce mois</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalConsultationsMois) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-stethoscope text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Ordonnances ce mois</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalOrdonnancesMois) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-prescription text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Revenus ce mois</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalRevenusMois, 0) }} <span class="text-sm font-normal">MRU</span></p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-coins text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-8 border border-gray-200 dark:border-gray-700">
        <form method="GET" action="{{ route('superadmin.medical.recap-medecins.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rechercher un médecin</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Nom ou prénom..."
                       class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-500 dark:focus:ring-gray-400 focus:border-transparent">
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Spécialité</label>
                <select name="specialite" 
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-gray-500 dark:focus:ring-gray-400 focus:border-transparent">
                    <option value="">Toutes les spécialités</option>
                    @foreach($specialites as $specialite)
                        <option value="{{ $specialite }}" {{ request('specialite') == $specialite ? 'selected' : '' }}>
                            {{ $specialite }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" 
                        class="px-6 py-2 bg-gray-800 dark:bg-gray-700 text-white rounded-lg hover:bg-gray-900 dark:hover:bg-gray-600 transition">
                    <i class="fas fa-search mr-2"></i>Filtrer
                </button>
                <a href="{{ route('superadmin.medical.recap-medecins.index') }}" 
                   class="px-6 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition">
                    <i class="fas fa-times mr-2"></i>Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Top 5 Médecins (Bar Chart) -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-chart-bar mr-2"></i>Top 5 Médecins du Mois
            </h2>
            <div class="h-80">
                <canvas id="topMedecinsChart"></canvas>
            </div>
        </div>

        <!-- Évolution Mensuelle (Line Chart) -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-chart-line mr-2"></i>Évolution des 6 Derniers Mois
            </h2>
            <div class="h-80">
                <canvas id="evolutionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Médecins List -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
            <i class="fas fa-users-cog mr-2"></i>Liste des Médecins ({{ count($medecinStats) }})
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($medecinStats as $stat)
                <a href="{{ route('superadmin.medical.recap-medecins.show', $stat['medecin']->id) }}"
                   class="block bg-gray-50 dark:bg-gray-700 rounded-lg p-5 border border-gray-200 dark:border-gray-600 hover:shadow-lg hover:border-gray-400 dark:hover:border-gray-500 transition cursor-pointer">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center text-gray-700 dark:text-gray-200 text-lg font-bold">
                                {{ strtoupper(substr($stat['medecin']->nom, 0, 1)) }}{{ strtoupper(substr($stat['medecin']->prenom, 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white">
                                    {{ $stat['medecin']->fonction }} {{ $stat['medecin']->nom }} {{ $stat['medecin']->prenom }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $stat['medecin']->specialite }}</p>
                            </div>
                        </div>
                        @if($stat['derniere_activite'])
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                <i class="fas fa-check-circle mr-1"></i>Actif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300">
                                <i class="fas fa-clock mr-1"></i>Inactif
                            </span>
                        @endif
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <!-- Mois en cours -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-600">
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">CE MOIS</div>
                            <div class="space-y-1">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-700 dark:text-gray-300">
                                        <i class="fas fa-stethoscope mr-1 text-blue-500"></i>{{ $stat['mois']['consultations'] }}
                                    </span>
                                    <span class="text-gray-700 dark:text-gray-300">
                                        <i class="fas fa-prescription mr-1 text-green-500"></i>{{ $stat['mois']['ordonnances'] }}
                                    </span>
                                </div>
                                <div class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ number_format($stat['mois']['revenus'], 0) }} MRU
                                </div>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-600">
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-2">TOTAL</div>
                            <div class="space-y-1">
                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-user-injured mr-1 text-yellow-500"></i>{{ $stat['total']['patients'] }} patients
                                </div>
                                <div class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ number_format($stat['total']['revenus'], 0) }} MRU
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    @if($stat['derniere_activite'])
                        <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                            <i class="far fa-clock mr-2"></i>
                            Dernière activité: {{ $stat['derniere_activite']->diffForHumans() }}
                        </div>
                    @endif
                </a>
            @empty
                <div class="col-span-2 text-center py-12">
                    <i class="fas fa-user-doctor text-gray-400 dark:text-gray-600 text-6xl mb-4"></i>
                    <p class="text-gray-600 dark:text-gray-400 text-lg">Aucun médecin trouvé</p>
                </div>
            @endforelse
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

    // Top 5 Médecins (Bar Chart)
    const topMedecinsCtx = document.getElementById('topMedecinsChart').getContext('2d');
    const topMedecinsChart = new Chart(topMedecinsCtx, {
        type: 'bar',
        data: {
            labels: [
                @foreach($top5Medecins as $stat)
                    "{{ $stat['medecin']->fonction }} {{ $stat['medecin']->nom }}",
                @endforeach
            ],
            datasets: [{
                label: 'Consultations',
                data: [
                    @foreach($top5Medecins as $stat)
                        {{ $stat['mois']['consultations'] }},
                    @endforeach
                ],
                backgroundColor: '#3b82f6',
                borderRadius: 6,
            }, {
                label: 'Ordonnances',
                data: [
                    @foreach($top5Medecins as $stat)
                        {{ $stat['mois']['ordonnances'] }},
                    @endforeach
                ],
                backgroundColor: '#10b981',
                borderRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
                    beginAtZero: true,
                    grid: {
                        color: gridColor
                    },
                    ticks: {
                        color: textColor
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: textColor
                    }
                }
            }
        }
    });

    // Évolution Mensuelle (Line Chart)
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
                label: 'Consultations',
                data: [
                    @foreach($evolutionMensuelle as $mois)
                        {{ $mois['consultations'] }},
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
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
                    beginAtZero: true,
                    grid: {
                        color: gridColor
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
