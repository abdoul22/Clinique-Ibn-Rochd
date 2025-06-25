@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Statistiques du médecin</h1>
            <p class="text-xl text-indigo-600 font-medium mt-1">{{ $medecin->prenom }} {{ $medecin->nom }}</p>
        </div>

        <!-- Filtre Date -->
        <form method="GET" class="w-full md:w-auto bg-white rounded-xl shadow-sm p-4 border border-gray-100">
            <div class="flex flex-col md:flex-row md:items-end gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Du :</label>
                    <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                        class="w-full md:w-48 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 border">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Au :</label>
                    <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                        class="w-full md:w-48 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 border">
                </div>
                <button type="submit"
                    class="h-10 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Appliquer
                </button>
            </div>
        </form>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Statistiques -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Examens par période</h2>
            </div>
            <div class="p-6">
                <div class="space-y-5">
                    <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Aujourd'hui</p>
                            <p class="text-2xl font-semibold text-gray-900 mt-1">{{ $examensParJour->last() ?? 0 }}</p>
                        </div>
                        <div class="bg-indigo-50 rounded-lg p-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-sm font-medium text-gray-500">Cette semaine</p>
                            <p class="text-xl font-semibold text-gray-900 mt-1">{{ $examensHebdo->sum('total') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-sm font-medium text-gray-500">Ce mois</p>
                            <p class="text-xl font-semibold text-gray-900 mt-1">{{ $examensMensuels->sum('total') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-sm font-medium text-gray-500">Cette année</p>
                            <p class="text-xl font-semibold text-gray-900 mt-1">{{ $examensAnnuels->sum('total') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-sm font-medium text-gray-500">Total</p>
                            <p class="text-xl font-semibold text-gray-900 mt-1">
                                {{ $examensParJour->last() + $examensHebdo->sum('total') +
                                $examensMensuels->sum('total') + $examensAnnuels->sum('total') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-900">Évolution des examens</h2>
                <div class="flex space-x-2">
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        Examens
                    </span>
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                        Parts
                    </span>
                </div>
            </div>
            <div class="p-4 h-80">
                <canvas id="chartExamens" class="w-full h-full"></canvas>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('chartExamens').getContext('2d');

    const labels = {!! json_encode($examensParJour->keys()) !!};
    const dataExamens = {!! json_encode($examensParJour->values()) !!};
    const dataParts = {!! json_encode($partsParJour->values()) !!};

    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Examens par jour',
                    data: dataExamens,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 5
                },
                {
                    label: 'Parts médecin validées',
                    data: dataParts,
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    labels: { font: { size: 14 } }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Date',
                        font: { size: 14 }
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Nombre / Montant',
                        font: { size: 14 }
                    }
                }
            }
        }
    });
</script>
@endpush
