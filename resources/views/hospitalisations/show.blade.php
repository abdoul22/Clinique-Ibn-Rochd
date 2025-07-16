@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('hospitalisations.index') }}"
                    class="mr-4 text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-8 h-8 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                        Hospitalisation #{{ $hospitalisation->id }}
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Détails de l'hospitalisation</p>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('hospitalisations.edit', $hospitalisation->id) }}"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg font-medium flex items-center transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    Modifier
                </a>
                <form action="{{ route('hospitalisations.destroy', $hospitalisation->id) }}" method="POST"
                    class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium flex items-center transition-colors duration-200"
                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette hospitalisation ?')">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Statut Badge -->
    <div class="mb-6">
        @if($hospitalisation->statut === 'en cours')
        <span
            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                    clip-rule="evenodd"></path>
            </svg>
            En Cours
        </span>
        @elseif($hospitalisation->statut === 'terminé')
        <span
            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"></path>
            </svg>
            Terminé
        </span>
        @else
        <span
            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd"></path>
            </svg>
            Annulé
        </span>
        @endif
    </div>

    <!-- Informations principales -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Informations du patient -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-indigo-500">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-indigo-100 dark:bg-indigo-900 mr-4">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Informations Patient</h2>
            </div>
            <div class="space-y-3">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-indigo-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span class="text-gray-600 dark:text-gray-400">Nom:</span>
                    <span class="ml-2 font-medium text-gray-900 dark:text-white">{{ $hospitalisation->patient->nom ??
                        '-' }} {{ $hospitalisation->patient->prenom ?? '' }}</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-indigo-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                    <span class="text-gray-600 dark:text-gray-400">Médecin:</span>
                    <span class="ml-2 font-medium text-gray-900 dark:text-white">Dr. {{ $hospitalisation->medecin->nom
                        ?? '-' }}</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-indigo-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                    <span class="text-gray-600 dark:text-gray-400">Service:</span>
                    <span class="ml-2 font-medium text-gray-900 dark:text-white">{{ $hospitalisation->service->nom ??
                        '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Informations d'hospitalisation -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 mr-4">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Période d'Hospitalisation</h2>
            </div>
            <div class="space-y-3">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-gray-600 dark:text-gray-400">Date d'entrée:</span>
                    <span class="ml-2 font-medium text-gray-900 dark:text-white">{{
                        \Carbon\Carbon::parse($hospitalisation->date_entree)->format('d/m/Y') }}</span>
                </div>
                @if($hospitalisation->date_sortie)
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-gray-600 dark:text-gray-400">Date de sortie:</span>
                    <span class="ml-2 font-medium text-gray-900 dark:text-white">{{
                        \Carbon\Carbon::parse($hospitalisation->date_sortie)->format('d/m/Y') }}</span>
                </div>
                @endif
                @if($hospitalisation->motif)
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <span class="text-gray-600 dark:text-gray-400">Motif:</span>
                    <span class="ml-2 font-medium text-gray-900 dark:text-white">{{ $hospitalisation->motif }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Informations détaillées -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Logement -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 mr-4">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Logement</h2>
            </div>
            <div class="space-y-3">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                    <span class="text-gray-600 dark:text-gray-400">Chambre:</span>
                    <span class="ml-2 font-medium text-gray-900 dark:text-white">
                        @if($hospitalisation->lit && $hospitalisation->lit->chambre)
                        {{ $hospitalisation->lit->chambre->nom_complet }}
                        @else
                        Non assignée
                        @endif
                    </span>
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                    </svg>
                    <span class="text-gray-600 dark:text-gray-400">Lit:</span>
                    <span class="ml-2 font-medium text-gray-900 dark:text-white">
                        @if($hospitalisation->lit)
                        Lit {{ $hospitalisation->lit->numero }}
                        @else
                        Non assigné
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Informations financières -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-yellow-500">
            <div class="flex items-center mb-4">
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900 mr-4">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                        </path>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Informations Financières</h2>
            </div>
            <div class="space-y-3">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                        </path>
                    </svg>
                    <span class="text-gray-600 dark:text-gray-400">Montant total:</span>
                    <span class="ml-2 font-medium text-gray-900 dark:text-white">
                        @if($hospitalisation->montant_total)
                        {{ number_format($hospitalisation->montant_total, 2) }} MRU
                        @else
                        Non défini
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Observations -->
    @if($hospitalisation->observation)
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-l-4 border-purple-500 mb-8">
        <div class="flex items-center mb-4">
            <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900 mr-4">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
            </div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Observations</h2>
        </div>
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $hospitalisation->observation }}</p>
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="flex justify-center space-x-4">
        <a href="{{ route('hospitalisations.index') }}"
            class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium flex items-center transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Retour à la liste
        </a>
    </div>
</div>
@endsection
