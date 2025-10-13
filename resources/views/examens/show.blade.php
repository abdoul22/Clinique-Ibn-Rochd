@extends('layouts.app')

@section('content')
<div
    class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 dark:from-gray-900 dark:to-gray-800 p-4 sm:p-6 lg:p-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    @php
                    $role = auth()->user()->role?->name;
                    $routePrefix = ($role === 'superadmin' || $role === 'admin') ? $role . '.' : '';
                    @endphp
                    <a href="{{ route($routePrefix . 'examens.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200 shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Retour
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                            Détail de l'examen
                        </h1>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">
                            {{ $examen->nom }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card principale -->
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- En-tête avec gradient -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                <h2 class="text-2xl font-bold text-white flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Informations de l'examen
                </h2>
            </div>

            <!-- Contenu -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Nom de l'examen
                        </label>
                        <p
                            class="text-lg font-semibold text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 rounded-lg px-4 py-3">
                            {{ $examen->nom }}
                        </p>
                    </div>

                    <!-- Service -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                            Service associé
                        </label>
                        <p
                            class="text-lg font-semibold text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 rounded-lg px-4 py-3">
                            {{ $examen->service->nom ?? 'Non défini' }}
                        </p>
                    </div>

                    <!-- Tarif -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                </path>
                            </svg>
                            Tarif
                        </label>
                        <p
                            class="text-lg font-semibold text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 rounded-lg px-4 py-3">
                            {{ number_format($examen->tarif, 0, ',', ' ') }} MRU
                        </p>
                    </div>

                    <!-- Part Cabinet -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                            Part Cabinet
                        </label>
                        <p
                            class="text-lg font-semibold text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 rounded-lg px-4 py-3">
                            {{ number_format($examen->part_cabinet, 0, ',', ' ') }} MRU
                        </p>
                    </div>

                    <!-- Part Médecin -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                </path>
                            </svg>
                            Part Médecin
                        </label>
                        <p
                            class="text-lg font-semibold text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 rounded-lg px-4 py-3">
                            {{ number_format($examen->part_medecin, 0, ',', ' ') }} MRU
                        </p>
                    </div>

                    <!-- Médecin associé -->
                    @if($examen->medecin)
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            Médecin associé
                        </label>
                        <p
                            class="text-lg font-semibold text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 rounded-lg px-4 py-3">
                            Dr. {{ $examen->medecin->nom }}
                        </p>
                    </div>
                    @endif
                </div>

                <!-- Total -->
                <div
                    class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-6 border border-blue-200 dark:border-blue-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                </path>
                            </svg>
                            <div>
                                <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Total Recette</p>
                                <p class="text-2xl font-bold text-blue-800 dark:text-blue-200">
                                    {{ number_format($examen->tarif, 0, ',', ' ') }} MRU
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Répartition</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Cabinet: {{ number_format(($examen->part_cabinet / max($examen->tarif, 1)) * 100, 0) }}%
                                |
                                Médecin: {{ number_format(($examen->part_medecin / max($examen->tarif, 1)) * 100, 0) }}%
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex justify-end space-x-3">
                <a href="{{ route($routePrefix . 'examens.index') }}"
                    class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-500 transition-all duration-200 shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Retour à la liste
                </a>
                <a href="{{ route($routePrefix . 'examens.edit', $examen->id) }}"
                    class="inline-flex items-center px-6 py-3 bg-blue-600 dark:bg-blue-700 text-white rounded-lg font-medium hover:bg-blue-700 dark:hover:bg-blue-800 transition-all duration-200 shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    Modifier
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
