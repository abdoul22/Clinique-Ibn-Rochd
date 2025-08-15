@extends('layouts.app')
@section('title', 'Liste des Crédits')

@section('content')
<!-- Main Container - Mobile optimized spacing -->
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 px-2 sm:px-4 lg:px-6 py-2 sm:py-4 lg:py-6">
    <div class="max-w-7xl mx-auto">

        <!-- Header Section -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6 mb-4 sm:mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <!-- Title with Icon -->
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                            Gestion des Crédits
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Gérez les crédits du personnel et des assurances
                        </p>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="flex-shrink-0">
                    <a href="{{ route('credits.create') }}"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-medium py-2.5 px-4 sm:px-6 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        <span class="hidden sm:inline">Donner Un Crédit</span>
                        <span class="sm:hidden">Nouveau</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-4 sm:mb-6 overflow-hidden">
            <!-- Filter Header -->
            <div
                class="px-4 sm:px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b border-gray-200 dark:border-gray-600">
                <div class="flex items-center gap-3">
                    <div class="p-1.5 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Filtres et Recherche</h3>
                </div>

                <!-- Active Filter Summary -->
                @php $summary = $summary ?? ''; @endphp

                @if($summary || request('type') || request('status'))
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Filtres actifs:</span>

                    @if($summary)
                    <span
                        class="inline-flex items-center gap-1 bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200 px-2 py-1 rounded-md text-xs font-medium">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        {{ $summary }}
                    </span>
                    @endif

                    @if(request('type'))
                    <span
                        class="inline-flex items-center gap-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 px-2 py-1 rounded-md text-xs font-medium">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        {{ ucfirst(request('type')) }}
                    </span>
                    @endif

                    @if(request('status'))
                    <span
                        class="inline-flex items-center gap-1 bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-200 px-2 py-1 rounded-md text-xs font-medium">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ ucfirst(request('status')) }}
                    </span>
                    @endif

                    <a href="{{ route('credits.index') }}"
                        class="inline-flex items-center gap-1 text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 text-xs font-medium transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Réinitialiser
                    </a>
                </div>
                @endif
            </div>

            <!-- Filter Forms -->
            <div class="p-4 sm:p-6 space-y-6">
                <!-- Unified Filter Form -->
                <form method="GET" action="{{ route('credits.index') }}" class="space-y-6" id="unified-filter-form"
                    autocomplete="off">

                    <!-- Period Filters -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            Filtrage par période
                        </h4>

                        <!-- Period Type Selection -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
                            <div class="lg:col-span-1">
                                <label for="period"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Période</label>
                                <select name="period" id="period"
                                    class="w-full px-4 py-3 text-sm border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 transition-colors">
                                    <option value="day" {{ request('period', 'day' )=='day' ? 'selected' : '' }}>Jour
                                    </option>
                                    <option value="week" {{ request('period')=='week' ? 'selected' : '' }}>Semaine
                                    </option>
                                    <option value="month" {{ request('period')=='month' ? 'selected' : '' }}>Mois
                                    </option>
                                    <option value="year" {{ request('period')=='year' ? 'selected' : '' }}>Année
                                    </option>
                                    <option value="range" {{ request('period')=='range' ? 'selected' : '' }}>
                                        Personnalisée</option>
                                </select>
                            </div>

                            <!-- Dynamic Date Inputs -->
                            <div id="input-day"
                                class="period-input lg:col-span-2 transition-all duration-300 transform">
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date</label>
                                <input type="date" name="date" value="{{ request('date') }}"
                                    class="w-full px-4 py-3 text-sm border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 transition-colors">
                            </div>

                            <div id="input-week"
                                class="period-input lg:col-span-2 hidden transition-all duration-300 transform">
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Semaine</label>
                                <input type="week" name="week" value="{{ request('week') }}"
                                    class="w-full px-4 py-3 text-sm border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 transition-colors">
                            </div>

                            <div id="input-month"
                                class="period-input lg:col-span-2 hidden transition-all duration-300 transform">
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mois</label>
                                <input type="month" name="month" value="{{ request('month') }}"
                                    class="w-full px-4 py-3 text-sm border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 transition-colors">
                            </div>

                            <div id="input-year"
                                class="period-input lg:col-span-2 hidden transition-all duration-300 transform">
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Année</label>
                                <input type="number" name="year" min="1900" max="2100" step="1"
                                    value="{{ request('year', date('Y')) }}"
                                    class="w-full px-4 py-3 text-sm border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 transition-colors">
                            </div>

                            <div id="input-range"
                                class="period-input lg:col-span-4 hidden transition-all duration-300 transform">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Période
                                    personnalisée</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <input type="date" name="date_start" value="{{ request('date_start') }}"
                                        placeholder="Date de début"
                                        class="w-full px-4 py-3 text-sm border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 transition-colors">
                                    <input type="date" name="date_end" value="{{ request('date_end') }}"
                                        placeholder="Date de fin"
                                        class="w-full px-4 py-3 text-sm border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 transition-colors">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Filters -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z">
                                </path>
                            </svg>
                            Filtres supplémentaires
                        </h4>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="type"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type de
                                    crédit</label>
                                <select name="type" id="type"
                                    class="w-full px-4 py-3 text-sm border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white dark:bg-gray-700 transition-colors">
                                    <option value="">Tous les types</option>
                                    <option value="personnel" {{ request('type')=='personnel' ? 'selected' : '' }}>
                                        Personnel</option>
                                    <option value="assurance" {{ request('type')=='assurance' ? 'selected' : '' }}>
                                        Assurance</option>
                                </select>
                            </div>

                            <div>
                                <label for="status"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Statut de
                                    paiement</label>
                                <select name="status" id="status"
                                    class="w-full px-4 py-3 text-sm border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-gray-700 transition-colors">
                                    <option value="">Tous les statuts</option>
                                    <option value="non payé" {{ request('status')=='non payé' ? 'selected' : '' }}>Non
                                        payé</option>
                                    <option value="partiellement payé" {{ request('status')=='partiellement payé'
                                        ? 'selected' : '' }}>Partiellement payé</option>
                                    <option value="payé" {{ request('status')=='payé' ? 'selected' : '' }}>Payé</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                        <button type="submit"
                            class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white font-medium py-2.5 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-sm hover:shadow-md text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Appliquer les filtres
                        </button>

                        <a href="{{ route('credits.index') }}"
                            class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 font-medium py-2.5 px-6 rounded-lg transition-all duration-200 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            Réinitialiser
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Credits Display -->
        <div class="space-y-4 sm:space-y-6">
            <!-- Personnel Credits Section -->
            @if(!request('type') || request('type') !== 'assurance')
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div
                    class="px-4 sm:px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Crédits du Personnel</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $creditsPersonnel->total() }} crédit(s) trouvé(s)
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-0">
                    @if($creditsPersonnel->count() > 0)
                    <x-credit-table :credits="$creditsPersonnel" />

                    <!-- Pagination Personnel -->
                    @if($creditsPersonnel->hasPages())
                    <div
                        class="px-4 sm:px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                Affichage de {{ $creditsPersonnel->firstItem() ?? 1 }} à {{
                                $creditsPersonnel->lastItem() ?? $creditsPersonnel->count() }}
                                sur {{ $creditsPersonnel->total() ?? $creditsPersonnel->count() }} résultats
                            </div>
                            <div class="flex justify-center">
                                {{ $creditsPersonnel->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                    @endif
                    @else
                    <div class="p-8 sm:p-12 text-center">
                        <div
                            class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Aucun crédit personnel</h3>
                        <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                            Aucun crédit personnel n'a été trouvé pour les critères sélectionnés.
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Insurance Credits Section -->
            @if(!request('type') || request('type') !== 'personnel')
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div
                    class="px-4 sm:px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-b border-gray-200 dark:border-gray-600">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Crédits des Assurances</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $creditsAssurance->total() }} crédit(s) trouvé(s)
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-0">
                    @if($creditsAssurance->count() > 0)
                    <x-credit-table :credits="$creditsAssurance" />

                    <!-- Pagination Assurance -->
                    @if($creditsAssurance->hasPages())
                    <div
                        class="px-4 sm:px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                Affichage de {{ $creditsAssurance->firstItem() ?? 1 }} à {{
                                $creditsAssurance->lastItem() ?? $creditsAssurance->count() }}
                                sur {{ $creditsAssurance->total() ?? $creditsAssurance->count() }} résultats
                            </div>
                            <div class="flex justify-center">
                                {{ $creditsAssurance->appends(request()->query())->links() }}
                            </div>
                        </div>
                    </div>
                    @endif
                    @else
                    <div class="p-8 sm:p-12 text-center">
                        <div
                            class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Aucun crédit assurance</h3>
                        <p class="text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                            Aucun crédit d'assurance n'a été trouvé pour les critères sélectionnés.
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const periodSelect = document.getElementById('period');
    const inputs = {
        day: document.getElementById('input-day'),
        week: document.getElementById('input-week'),
        month: document.getElementById('input-month'),
        year: document.getElementById('input-year'),
        range: document.getElementById('input-range')
    };

    function showInput(period) {
        Object.values(inputs).forEach(input => {
            if (input) {
                input.classList.add('hidden');
                input.classList.remove('opacity-100', 'translate-y-0');
                input.classList.add('opacity-0', '-translate-y-2');
            }
        });

        if (inputs[period]) {
            inputs[period].classList.remove('hidden', 'opacity-0', '-translate-y-2');
            inputs[period].classList.add('opacity-100', 'translate-y-0');
        }
    }

    if (periodSelect) {
        periodSelect.addEventListener('change', function() {
            showInput(this.value);
        });

        // Initialiser l'affichage
        showInput(periodSelect.value);
    }

    // Améliorer l'état de chargement du bouton
    const submitButton = document.querySelector('button[type="submit"]');
    if (submitButton) {
        submitButton.addEventListener('click', function(e) {
            const form = this.closest('form');

            // Validation stricte pour la période personnalisée
            const periodIsRange = form.querySelector('[name="period"]')?.value === 'range';
            const start = form.querySelector('[name="date_start"]')?.value;
            const end = form.querySelector('[name="date_end"]')?.value;
            if (periodIsRange && (!start || !end)) {
                e.preventDefault();
                const alert = document.createElement('div');
                alert.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 text-sm';
                alert.textContent = 'Veuillez sélectionner une date de début et une date de fin.';
                document.body.appendChild(alert);
                setTimeout(() => { alert.remove(); }, 3000);
                return;
            }

            // Afficher l'état de chargement
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = `
                <svg class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Recherche en cours...</span>
            `;

            // Si pour une raison quelconque le formulaire ne se soumet pas, restaurer le bouton après 3 secondes
            setTimeout(() => {
                if (this.disabled) {
                    this.disabled = false;
                    this.innerHTML = originalText;
                }
            }, 3000);
        });
    }

    // Effet de focus sur les inputs
    const allInputs = document.querySelectorAll('input, select');
    allInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('ring-2', 'ring-purple-200', 'dark:ring-purple-800');
        });

        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('ring-2', 'ring-purple-200', 'dark:ring-purple-800');
        });
    });
});
</script>
@endsection
