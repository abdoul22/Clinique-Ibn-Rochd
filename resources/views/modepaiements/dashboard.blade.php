@extends('layouts.app')

@section('content')
<div class="card mb-5">
    <div class="card-header">
        <div class="flex justify-between items-center">
            <h2 class="card-title">Mode des Paiements</h2>
            <a href="{{ route('modepaiements.historique') }}" class="form-button text-sm">
                Voir l'Historique
            </a>
        </div>
    </div>
    <div class="card-body">
        <!-- Filtre par date -->
        <form method="GET" action="" class="mb-6 flex flex-wrap gap-2 items-center">
            <label for="date" class="text-sm font-medium text-gray-700 dark:text-gray-300">Afficher pour la date
                :</label>
            <input type="date" name="date" id="date" value="{{ $date ?? '' }}" class="form-input text-sm">
            <button type="submit" class="form-button text-sm">Filtrer</button>
            <a href="{{ route('modepaiements.dashboard') }}"
                class="ml-2 text-sm text-gray-600 dark:text-gray-400 underline">Afficher le
                total global</a>
        </form>
        <div class="space-y-5">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($data as $item)
                <div class="card p-4 bg-gradient-to-br from-gray-100 to-white dark:from-gray-800 dark:to-gray-700">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $item['mode'] }}</p>
                    <p class="text-sm text-green-600 dark:text-green-400">Entrée : {{ number_format($item['entree'], 0,
                        ',', ' ') }} MRU</p>
                    <p class="text-sm text-red-600 dark:text-red-400">Sortie : {{ number_format($item['sortie'], 0, ',',
                        ' ') }} MRU</p>
                    <p class="text-xl font-semibold text-gray-900 dark:text-gray-100 mt-1">Solde : {{
                        number_format($item['solde'], 0, ',',
                        ' ') }} MRU</p>
                </div>
                @endforeach
                <div class="bg-lime-200 dark:bg-lime-800 rounded-xl p-4 col-span-2 md:col-span-1">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Trésorerie</p>
                    <p class="text-xl font-semibold text-gray-900 dark:text-gray-100 mt-1">{{
                        number_format($totalGlobal, 0, ',', ' ') }}
                        MRU</p>
                </div>
            </div>
        </div>
        <!-- Chart.js -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">Statistiques graphiques</h3>
            <canvas id="paiementChart" height="120"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('paiementChart').getContext('2d');
        const paiementChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [
                    {
                        label: 'Entrée',
                        data: @json($chartEntrees),
                        backgroundColor: 'rgba(16, 185, 129, 0.6)', // vert
                    },
                    {
                        label: 'Sortie',
                        data: @json($chartSorties),
                        backgroundColor: 'rgba(239, 68, 68, 0.6)', // rouge
                    },
                    {
                        label: 'Solde',
                        data: @json($chartSoldes),
                        backgroundColor: 'rgba(59, 130, 246, 0.6)', // bleu
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#374151'
                        }
                    },
                    title: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#374151',
                            callback: function(value) {
                                return value.toLocaleString() + ' MRU';
                            }
                        },
                        grid: {
                            color: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb'
                        }
                    },
                    x: {
                        ticks: {
                            color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#374151'
                        },
                        grid: {
                            color: document.documentElement.classList.contains('dark') ? '#374151' : '#e5e7eb'
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
