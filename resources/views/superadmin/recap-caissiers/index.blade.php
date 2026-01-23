@extends('layouts.app')
@section('title', 'Récapitulatif Caissiers')

@section('content')
<div class="p-6 max-w-7xl mx-auto">
    <!-- Header avec filtres -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                💰 Récapitulatif Caissiers
            </h1>
            <p class="text-gray-600 dark:text-gray-400">Analyse complète des performances et activités</p>
        </div>

    </div>

    <!-- Filtres période et recherche -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Période</label>
                <select name="periode" onchange="this.form.submit()"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                    <option value="jour" {{ request('periode')=='jour' ? 'selected' : '' }}>Aujourd'hui</option>
                    <option value="semaine" {{ request('periode')=='semaine' ? 'selected' : '' }}>Cette semaine</option>
                    <option value="mois" {{ request('periode', 'mois' )=='mois' ? 'selected' : '' }}>Ce mois</option>
                    <option value="annee" {{ request('periode')=='annee' ? 'selected' : '' }}>Cette année</option>
                    <option value="tout" {{ request('periode')=='tout' ? 'selected' : '' }}>Tout</option>
                </select>
            </div>
            <div class="flex-1 min-w-[250px]">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rechercher</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom du caissier..."
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
            </div>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-search mr-2"></i> Filtrer
            </button>
        </form>
    </div>

    <!-- Stats globales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-blue-500 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium">Factures Total</span>
                <i class="fas fa-receipt text-2xl"></i>
            </div>
            <div class="text-3xl font-bold">{{ number_format($totalFacturesPeriode) }}</div>
            <div class="text-xs mt-1">{{ ucfirst($periode) }} en cours</div>
        </div>

        <div class="bg-green-500 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium">Montant Encaissé</span>
                <i class="fas fa-coins text-2xl"></i>
            </div>
            <div class="text-3xl font-bold">{{ number_format($totalMontantPeriode, 0, ',', ' ') }}</div>
            <div class="text-xs mt-1">MRU</div>
        </div>

        <div class="bg-purple-500 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium">Transactions</span>
                <i class="fas fa-exchange-alt text-2xl"></i>
            </div>
            <div class="text-3xl font-bold">{{ number_format($totalTransactionsPeriode) }}</div>
            <div class="text-xs mt-1">Paiements enregistrés</div>
        </div>

        <div class="bg-emerald-800 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm font-medium">Moyenne/Caissier</span>
                <i class="fas fa-chart-line text-2xl"></i>
            </div>
            <div class="text-3xl font-bold">{{ number_format($moyenneFactures, 1) }}</div>
            <div class="text-xs mt-1">Factures/caissier</div>
        </div>
    </div>

    <!-- Top 5 Caissiers -->
    @if(count($top5Caissiers) > 0)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-8 overflow-hidden">
        <div class="px-6 py-4 bg-indigo-600 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-white flex items-center">
                🏆 Top 5 Caissiers - {{ ucfirst($periode) }}
            </h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($top5Caissiers as $index => $stat)
                <div
                    class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:shadow-md transition">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg
                            {{ $index === 0 ? 'bg-yellow-500' : '' }}
                            {{ $index === 1 ? 'bg-gray-500' : '' }}
                            {{ $index === 2 ? 'bg-orange-500' : '' }}
                            {{ $index > 2 ? 'bg-blue-500' : '' }}">
                            {{ $index + 1 }}
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-white">
                                {{ $stat['caissier']->name }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $stat['caissier']->email }}
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($stat['periode']['factures']) }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            {{ number_format($stat['periode']['montant'], 0, ',', ' ') }} MRU
                        </div>
                        <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                            ⚡ {{ $stat['periode']['vitesse_traitement'] }} fact./h
                        </div>
                    </div>
                    <div class="ml-4">
                        <a href="{{ route('superadmin.recap-caissiers.show', $stat['caissier']->id) }}"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                            Détails →
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Liste complète des caissiers avec statistiques -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div
            class="px-6 py-4 bg-gradient-to-r from-blue-500 to-indigo-600 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-bold text-white flex items-center">
                <i class="fas fa-users mr-2"></i>
                Tous les Caissiers ({{ count($caissierStats) }})
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Caissier</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Factures</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Recettes</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Dépenses</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Montant Net</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Moyenne/Jour</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Taux Activité</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Dernière Activité</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($caissierStats as $stat)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div
                                    class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                    <span class="text-blue-600 dark:text-blue-300 font-semibold text-sm">
                                        {{ substr($stat['caissier']->name, 0, 2) }}
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $stat['caissier']->name }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $stat['caissier']->email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ number_format($stat['periode']['factures']) }}
                            </span>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                Total: {{ number_format($stat['total']['factures']) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm font-semibold text-green-600 dark:text-green-400">
                                {{ number_format($stat['periode']['montant'], 0, ',', ' ') }} MRU
                            </span>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                Total: {{ number_format($stat['total']['montant'], 0, ',', ' ') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm font-semibold text-red-600 dark:text-red-400">
                                {{ number_format($stat['periode']['depenses'], 0, ',', ' ') }} MRU
                            </span>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                Total: {{ number_format($stat['total']['depenses'], 0, ',', ' ') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">
                                {{ number_format($stat['periode']['montant_net'], 0, ',', ' ') }} MRU
                            </span>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                Total: {{ number_format($stat['total']['montant_net'], 0, ',', ' ') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ number_format($stat['periode']['moyenne_par_jour'], 1) }}
                            </span>
                            <div class="text-xs text-gray-500 dark:text-gray-400">factures/jour</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center">
                                <div class="w-16 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2">
                                    <div class="bg-green-500 h-2 rounded-full"
                                        style="width: {{ min($stat['periode']['taux_activite'], 100) }}%"></div>
                                </div>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $stat['periode']['taux_activite'] }}%
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($stat['derniere_activite'])
                            <div class="text-sm text-gray-900 dark:text-white">
                                {{ $stat['derniere_activite']->created_at->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $stat['derniere_activite']->created_at->diffForHumans() }}
                            </div>
                            @else
                            <span class="text-sm text-gray-500 dark:text-gray-400">Aucune</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('superadmin.recap-caissiers.show', ['id' => $stat['caissier']->id, 'periode' => request('periode', 'mois')]) }}"
                                class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm transition">
                                <i class="fas fa-eye mr-1"></i> Voir
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-4 opacity-50"></i>
                            <p class="text-lg">Aucun caissier trouvé</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Graphique d'évolution (derniers 12 mois) -->
    @if(count($evolutionMensuelle) > 0)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mt-8 p-6">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
            <i class="fas fa-chart-area mr-2 text-blue-600"></i>
            Évolution sur 12 mois
        </h3>
        <div class="h-64">
            <canvas id="evolutionChart"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#E5E7EB' : '#374151';
        const gridColor = isDark ? '#374151' : '#E5E7EB';

        const ctx = document.getElementById('evolutionChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($evolutionMensuelle, 'mois')) !!},
                datasets: [{
                    label: 'Recettes (MRU)',
                    data: {!! json_encode(array_column($evolutionMensuelle, 'recettes')) !!},
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.2)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Dépenses (MRU)',
                    data: {!! json_encode(array_column($evolutionMensuelle, 'depenses')) !!},
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.2)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Montant Net (MRU)',
                    data: {!! json_encode(array_column($evolutionMensuelle, 'montant')) !!},
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3
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
                        labels: {
                            color: textColor,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('fr-FR').format(context.parsed.y) + ' MRU';
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: textColor,
                            callback: function(value) {
                                return new Intl.NumberFormat('fr-FR').format(value) + ' MRU';
                            }
                        },
                        grid: {
                            color: gridColor
                        }
                    },
                    x: {
                        ticks: {
                            color: textColor
                        },
                        grid: {
                            color: gridColor
                        }
                    }
                }
            }
        });
    </script>
    @endif
</div>
@endsection
