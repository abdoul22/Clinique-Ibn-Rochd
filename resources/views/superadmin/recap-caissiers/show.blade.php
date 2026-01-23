@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                📊 Détails Caissier : {{ $caissier->name }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Email: {{ $caissier->email }} • Fonction: {{ $caissier->fonction }}
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('superadmin.recap-caissiers.index') }}" 
                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                ← Retour
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-6">
        <form method="GET" class="flex items-center gap-4">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Période:</label>
            <select name="periode" onchange="this.form.submit()" 
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500">
                <option value="jour" {{ $periode == 'jour' ? 'selected' : '' }}>Aujourd'hui</option>
                <option value="semaine" {{ $periode == 'semaine' ? 'selected' : '' }}>Cette semaine</option>
                <option value="mois" {{ $periode == 'mois' ? 'selected' : '' }}>Ce mois</option>
                <option value="trimestre" {{ $periode == 'trimestre' ? 'selected' : '' }}>Ce trimestre</option>
                <option value="annee" {{ $periode == 'annee' ? 'selected' : '' }}>Cette année</option>
            </select>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-blue-500 rounded-xl p-6 shadow-lg">
            <div class="text-white text-sm mb-2">Factures</div>
            <div class="text-3xl font-bold text-white">{{ number_format($facturesPeriode) }}</div>
            <div class="text-sm text-white mt-1">Total: {{ number_format($facturesTotal) }}</div>
        </div>
        <div class="bg-green-500 rounded-xl p-6 shadow-lg">
            <div class="text-white text-sm mb-2">Recettes</div>
            <div class="text-3xl font-bold text-white">{{ number_format($montantPeriode, 0, ',', ' ') }} MRU</div>
            <div class="text-sm text-white mt-1">Total: {{ number_format($montantTotal, 0, ',', ' ') }} MRU</div>
        </div>
        <div class="bg-red-500 rounded-xl p-6 shadow-lg">
            <div class="text-white text-sm mb-2">Dépenses</div>
            <div class="text-3xl font-bold text-white">{{ number_format($depensesPeriode, 0, ',', ' ') }} MRU</div>
            <div class="text-sm text-white mt-1">Total: {{ number_format($depensesTotal, 0, ',', ' ') }} MRU</div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-purple-500 rounded-xl p-6 shadow-lg">
            <div class="text-white text-sm mb-2">💰 Montant Net (Période)</div>
            <div class="text-4xl font-bold text-white">{{ number_format($montantNetPeriode, 0, ',', ' ') }} MRU</div>
            <div class="text-sm text-white mt-2">Recettes - Dépenses</div>
        </div>
        <div class="bg-amber-600 rounded-xl p-6 shadow-lg">
            <div class="text-white text-sm mb-2">💰 Montant Net (Total)</div>
            <div class="text-4xl font-bold text-white">{{ number_format($montantNetTotal, 0, ',', ' ') }} MRU</div>
            <div class="text-sm text-white mt-2">Depuis le début</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">📅 Jours de Travail</h2>
            <div class="text-4xl font-bold text-amber-600 dark:text-amber-400 mb-2">{{ $joursActifs }}</div>
            <p class="text-gray-600 dark:text-gray-400">jours actifs sur la période</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">🏆 Meilleure Journée</h2>
            @if($meilleureJournee)
                <div class="text-2xl font-bold text-green-600 dark:text-green-400 mb-2">
                    {{ \Carbon\Carbon::parse($meilleureJournee->date)->format('d/m/Y') }}
                </div>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ $meilleureJournee->count }} factures • {{ number_format($meilleureJournee->montant, 0, ',', ' ') }} MRU
                </p>
            @else
                <p class="text-gray-500 dark:text-gray-500 italic">Aucune donnée</p>
            @endif
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">📊 Répartition de l'Activité</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Par Jour de la Semaine</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Jour</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Factures</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($activiteParJour as $jour => $count)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $jour }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-semibold text-blue-600 dark:text-blue-400">
                                        {{ number_format($count) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400 italic">
                                        Aucune donnée
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Par Heure de la Journée</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Heure</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Factures</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($activiteParHeure as $heure => $count)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $heure }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-semibold text-green-600 dark:text-green-400">
                                        {{ number_format($count) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-3 text-center text-sm text-gray-500 dark:text-gray-400 italic">
                                        Aucune donnée
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">💳 Types de Paiement</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Montant Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($typesPaiement as $type)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ ucfirst($type->type) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                {{ number_format($type->count) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-semibold">
                                {{ number_format($type->total, 0, ',', ' ') }} MRU
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 italic">
                                Aucune donnée de paiement
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">💸 Dernières Dépenses (20 dernières)</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">N°</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Source</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Montant</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($dernieresDepenses as $depense)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                #{{ $depense->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                {{ $depense->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                {{ $depense->nom }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                    {{ $depense->source === 'manuelle' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ ucfirst($depense->source ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600 dark:text-red-400">
                                {{ number_format($depense->montant, 0, ',', ' ') }} MRU
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 italic">
                                Aucune dépense pour cette période
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">📈 Performance sur 12 Mois</h2>
        <canvas id="chartPerformance" height="80"></canvas>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">📄 Dernières Factures (20 dernières)</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">N° Facture</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Examen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Montant</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($dernieresFactures as $facture)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                #{{ $facture->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                {{ $facture->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $facture->patient->nom ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                {{ $facture->examen->nom ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                {{ number_format($facture->total, 0, ',', ' ') }} MRU
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400 italic">
                                Aucune facture
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#E5E7EB' : '#374151';
    const gridColor = isDark ? '#374151' : '#E5E7EB';

    const ctxPerformance = document.getElementById('chartPerformance').getContext('2d');
    new Chart(ctxPerformance, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($performanceMensuelle, 'mois')) !!},
            datasets: [{
                label: 'Recettes (MRU)',
                data: {!! json_encode(array_column($performanceMensuelle, 'recettes')) !!},
                backgroundColor: 'rgba(34, 197, 94, 0.2)',
                borderColor: 'rgba(34, 197, 94, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }, {
                label: 'Dépenses (MRU)',
                data: {!! json_encode(array_column($performanceMensuelle, 'depenses')) !!},
                backgroundColor: 'rgba(239, 68, 68, 0.2)',
                borderColor: 'rgba(239, 68, 68, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }, {
                label: 'Montant Net (MRU)',
                data: {!! json_encode(array_column($performanceMensuelle, 'montant')) !!},
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    labels: { 
                        color: textColor,
                        font: { size: 12 }
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
                    grid: { color: gridColor }
                },
                x: {
                    ticks: { color: textColor },
                    grid: { color: gridColor }
                }
            }
        }
    });
});
</script>
@endsection
