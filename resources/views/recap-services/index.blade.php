{{-- resources/views/recap-services/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 space-y-4 lg:space-y-0">
    <h1 class="text-xl md:text-2xl font-bold">Récapitulatif journalier des services</h1>

    <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0 w-full lg:w-auto">
        <a href="{{ route('recap-services.exportPdf', request()->query()) }}"
            class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded transition">PDF</a>
        <a href="{{ route('recap-services.print', request()->query()) }}" target="_blank"
            class="bg-green-500 hover:bg-green-600 text-white text-sm px-4 py-2 rounded transition">Imprimer</a>
    </div>
</div>

<!-- Filtres -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
    <form method="GET" action="{{ route('recap-services.index') }}" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Filtre par période -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Période</label>
                <select name="period" id="period"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm">
                    <option value="all" {{ request('period')=='all' ? 'selected' : '' }}>Toutes les périodes</option>
                    <option value="day" {{ request('period')=='day' ? 'selected' : '' }}>Jour</option>
                    <option value="week" {{ request('period')=='week' ? 'selected' : '' }}>Semaine</option>
                    <option value="month" {{ request('period')=='month' ? 'selected' : '' }}>Mois</option>
                    <option value="year" {{ request('period')=='year' ? 'selected' : '' }}>Année</option>
                    <option value="range" {{ request('period')=='range' ? 'selected' : '' }}>Plage de dates</option>
                </select>
            </div>

            <!-- Date spécifique -->
            <div id="dateField" class="hidden">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date</label>
                <input type="date" name="date" value="{{ request('date') }}"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm">
            </div>

            <!-- Semaine -->
            <div id="weekField" class="hidden">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Semaine</label>
                <input type="week" name="week" value="{{ request('week') }}"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm">
            </div>

            <!-- Mois -->
            <div id="monthField" class="hidden">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mois</label>
                <input type="month" name="month" value="{{ request('month') }}"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm">
            </div>

            <!-- Année -->
            <div id="yearField" class="hidden">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Année</label>
                <input type="number" name="year" value="{{ request('year') }}" min="2020" max="2030"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm">
            </div>

            <!-- Plage de dates -->
            <div id="rangeFields" class="hidden lg:col-span-2">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date
                            début</label>
                        <input type="date" name="date_start" value="{{ request('date_start') }}"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date fin</label>
                        <input type="date" name="date_end" value="{{ request('date_end') }}"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm">
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Filtre par service -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Service</label>
                <select name="service_id"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm">
                    <option value="">Tous les services</option>
                    @foreach($allServices as $service)
                    <option value="{{ $service->id }}" {{ request('service_id')==$service->id ? 'selected' : '' }}>
                        {{ $service->nom }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Recherche par nom -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Recherche</label>
                <input type="text" name="search" placeholder="Rechercher par nom de service..."
                    value="{{ request('search') }}"
                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm">
            </div>
        </div>

        <div class="flex justify-between items-center">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition">
                Filtrer
            </button>
            <a href="{{ route('recap-services.index') }}"
                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                Réinitialiser
            </a>
        </div>
    </form>
</div>

<!-- Résumé -->
@if(isset($resume))
<div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-6 mb-6">
    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Résumé</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
            <div class="flex items-center">
                <div class="bg-blue-100 dark:bg-blue-900/30 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total des actes</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($resume['total_actes'],
                        0, ',', ' ') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm">
            <div class="flex items-center">
                <div class="bg-green-100 dark:bg-green-900/30 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                        </path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total des recettes</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{
                        number_format($resume['total_recettes'], 0, ',', ' ') }} MRU</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
    <table class="min-w-full text-sm text-left">
        <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
            <tr>
                <th class="py-3 px-4 font-semibold">Service</th>
                <th class="py-3 px-4 font-semibold">Nombre d'actes</th>
                <th class="py-3 px-4 font-semibold">Total</th>
                <th class="py-3 px-4 font-semibold">Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recaps as $recap)
            <tr class="border-t border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="py-3 px-4">
                    @php
                    $key = $recap->service_key ?? $recap->service_id;
                    $badgeColors = [
                    'LABORATOIRE' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                    'PHARMACIE' => 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200',
                    'MEDECINE DENTAIRE' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                    'IMAGERIE MEDICALE' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
                    'CONSULTATIONS EXTERNES' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                    'HOSPITALISATION' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                    'BLOC OPERATOIRE' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                    'INFIRMERIE' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                    'EXPLORATIONS FONCTIONNELLES' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                    ];

                    if ($key === 'PHARMACIE') {
                    $displayName = 'PHARMACIE';
                    $typeKey = 'PHARMACIE';
                    $badgeClass = $badgeColors[$typeKey];
                    } else {
                    $serviceModel = \App\Models\Service::find($key);
                    $typeKey = $serviceModel?->type_service;
                    $badgeClass = $typeKey ? ($badgeColors[$typeKey] ?? 'bg-gray-200 text-gray-800 dark:bg-gray-700
                    dark:text-gray-200') : 'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
                    $displayName = $services[$key] ?? ($serviceModel->nom ?? 'Service non assigné');
                    }
                    @endphp
                    <span class="font-medium text-gray-900 dark:text-white">{{ $displayName }}</span>
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }} ml-2">
                        {{ $typeKey ?? '—' }}
                    </span>
                </td>
                <td class="py-3 px-4">
                    <span
                        class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 px-2 py-1 rounded-full text-xs font-medium">
                        {{ $recap->nombre }}
                    </span>
                </td>
                <td class="py-3 px-4 font-semibold text-green-600 dark:text-green-400">
                    {{ number_format($recap->total, 0, ',', ' ') }} MRU
                </td>
                <td class="py-3 px-4 text-gray-600 dark:text-gray-400">
                    {{ \Carbon\Carbon::parse($recap->jour)->format('d/m/Y') }}
                </td>
            </tr>
            @empty
            <tr class="border-t border-gray-200 dark:border-gray-600">
                <td colspan="4" class="py-8 px-4 text-center text-gray-500 dark:text-gray-400">
                    <div class="flex flex-col items-center">
                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                        <p class="text-lg font-medium">Aucun enregistrement trouvé</p>
                        <p class="text-sm">Essayez de modifier vos filtres</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="py-4">
    {{ $recaps->links() }}
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const periodSelect = document.getElementById('period');
    const dateField = document.getElementById('dateField');
    const weekField = document.getElementById('weekField');
    const monthField = document.getElementById('monthField');
    const yearField = document.getElementById('yearField');
    const rangeFields = document.getElementById('rangeFields');

    function toggleFields() {
        // Cacher tous les champs
        dateField.classList.add('hidden');
        weekField.classList.add('hidden');
        monthField.classList.add('hidden');
        yearField.classList.add('hidden');
        rangeFields.classList.add('hidden');

        // Afficher le champ approprié
        switch(periodSelect.value) {
            case 'day':
                dateField.classList.remove('hidden');
                break;
            case 'week':
                weekField.classList.remove('hidden');
                break;
            case 'month':
                monthField.classList.remove('hidden');
                break;
            case 'year':
                yearField.classList.remove('hidden');
                break;
            case 'range':
                rangeFields.classList.remove('hidden');
                break;
        }
    }

    periodSelect.addEventListener('change', toggleFields);
    toggleFields(); // Initialiser l'état
});
</script>
@endsection