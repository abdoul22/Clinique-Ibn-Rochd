@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Statistiques du médecin</h1>
            <p class="text-xl font-extrabold text-indigo-600 dark:text-indigo-300 mt-2">{{ $medecin->prenom }} {{
                $medecin->nom }}</p>
        </div>

        <!-- Filtre Date -->
        <form method="GET"
            class="w-full md:w-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 border border-gray-100 dark:border-gray-700">
            <div class="flex flex-col md:flex-row md:items-end gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Du
                        :</label>
                    <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                        class="w-full md:w-48 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 border bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Au
                        :</label>
                    <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                        class="w-full md:w-48 rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 px-3 border bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
                </div>
                <button type="submit"
                    class="h-10 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 dark:bg-indigo-700 hover:bg-indigo-700 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Appliquer
                </button>
            </div>
        </form>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Statistiques -->
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Examens par période</h2>
            </div>
            <div class="p-6">
                <div class="space-y-5">
                    <div class="flex justify-between items-center pb-3 border-b border-gray-100 dark:border-gray-700">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-300">Aujourd'hui</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white mt-1">{{ $examensAujourdhui
                                }}</p>
                        </div>
                        <div class="bg-indigo-50 dark:bg-indigo-900 rounded-lg p-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-300"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-300">Hier</p>
                            <p class="text-xl font-semibold text-gray-900 dark:text-white mt-1">{{ $examensHier }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-300">Cette semaine</p>
                            <p class="text-xl font-semibold text-gray-900 dark:text-white mt-1">{{
                                $examensHebdo->sum('total') }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-300">Ce mois</p>
                            <p class="text-xl font-semibold text-gray-900 dark:text-white mt-1">{{
                                $examensMensuels->sum('total') }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-300">Cette année</p>
                            <p class="text-xl font-semibold text-gray-900 dark:text-white mt-1">{{
                                $examensAnnuels->sum('total') }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-300">Total</p>
                            <p class="text-xl font-semibold text-gray-900 dark:text-white mt-1">{{ $totalExamens }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique -->
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Évolution des examens</h2>
                <div class="flex space-x-2">
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-300">Examens</span>
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900 text-emerald-800 dark:text-emerald-300">Parts</span>
                </div>
            </div>
            <div class="p-4 h-64 sm:h-80">
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

    // Adaptation dark mode Chart.js
    function getChartColors() {
        const isDark = document.documentElement.classList.contains('dark');
        return {
            grid: isDark ? '#374151' : '#e5e7eb',
            text: isDark ? '#d1d5db' : '#374151',
            examens: isDark ? 'rgb(129, 140, 248)' : 'rgb(59, 130, 246)',
            examensBg: isDark ? 'rgba(129, 140, 248, 0.1)' : 'rgba(59, 130, 246, 0.1)',
            parts: isDark ? 'rgb(52, 211, 153)' : 'rgb(16, 185, 129)',
            partsBg: isDark ? 'rgba(52, 211, 153, 0.1)' : 'rgba(16, 185, 129, 0.1)',
        };
    }
    
    function renderChart() {
        const colors = getChartColors();
        new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Examens par jour',
                        data: dataExamens,
                        borderColor: colors.examens,
                        backgroundColor: colors.examensBg,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    },
                    {
                        label: 'Parts médecin validées',
                        data: dataParts,
                        borderColor: colors.parts,
                        backgroundColor: colors.partsBg,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        labels: { 
                            font: { size: 12 }, 
                            color: colors.text,
                            usePointStyle: true,
                            padding: 20
                        },
                        position: 'top',
                        align: 'start'
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
                            font: { size: 12 },
                            color: colors.text
                        },
                        grid: { color: colors.grid },
                        ticks: { 
                            color: colors.text,
                            maxRotation: 45,
                            minRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Nombre / Montant',
                            font: { size: 12 },
                            color: colors.text
                        },
                        grid: { color: colors.grid },
                        ticks: { color: colors.text }
                    }
                }
            }
        });
    }
    
    renderChart();
    
    // Re-render on dark mode toggle
    window.addEventListener('darkmode:toggle', () => {
        ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
        renderChart();
    });
    
    // Re-render on window resize for better mobile responsiveness
    window.addEventListener('resize', () => {
        ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
        renderChart();
    });
</script>
@endpush