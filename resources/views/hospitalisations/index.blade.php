@extends('layouts.app')

@section('content')
<div class="w-full max-w-full mx-auto p-3 sm:p-4 lg:p-6 overflow-x-hidden">
    <!-- Header responsive -->
    <div class="mb-4 sm:mb-6 lg:mb-8">
        <!-- Titre et description -->
        <div class="mb-4 sm:mb-6">
            <h1 class="page-title flex items-center flex-wrap">
                <svg class="w-6 h-6 sm:w-8 sm:h-8 mr-2 sm:mr-3 text-indigo-600 flex-shrink-0" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                    </path>
                </svg>
                Gestion des Hospitalisations
            </h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base">Gérez les hospitalisations des patients</p>
        </div>

        <!-- Boutons d'action responsive -->
        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 mb-4 sm:mb-6 w-full overflow-x-hidden">
            <a href="{{ route('hospitalisations.create') }}" class="form-button flex-1 sm:flex-none">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span class="hidden sm:inline">Nouvelle Hospitalisation</span>
                <span class="sm:hidden text-sm">Nouvelle</span>
            </a>

            @if(Auth::user() && Auth::user()->role?->name === 'superadmin')
            <a href="{{ route('chambres.create') }}" class="form-button flex-1 sm:flex-none"
                style="background: linear-gradient(to right, rgb(34 197 94), rgb(22 163 74));">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                    </path>
                </svg>
                <span class="hidden sm:inline">Créer une Chambre</span>
                <span class="sm:hidden text-sm">Chambre</span>
            </a>
            @endif
        </div>

        <!-- Statistiques responsive -->
        <div
            class="grid grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-3 lg:gap-4 mb-4 sm:mb-6 lg:mb-8 w-full overflow-x-hidden">
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-2 sm:p-3 lg:p-4 border-l-4 border-indigo-500 min-w-0">
                <div class="flex items-center">
                    <div class="p-1.5 sm:p-2 rounded-full bg-indigo-100 dark:bg-indigo-900 flex-shrink-0">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 lg:w-5 lg:h-5 text-indigo-600 dark:text-indigo-400"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-2 sm:ml-3 min-w-0 flex-1">
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 truncate">Total</p>
                        <p class="text-sm sm:text-lg lg:text-xl font-semibold text-gray-900 dark:text-white">{{
                            $hospitalisations->total() }}</p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-2 sm:p-3 lg:p-4 border-l-4 border-green-500 min-w-0">
                <div class="flex items-center">
                    <div class="p-1.5 sm:p-2 rounded-full bg-green-100 dark:bg-green-900 flex-shrink-0">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 lg:w-5 lg:h-5 text-green-600 dark:text-green-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-2 sm:ml-3 min-w-0 flex-1">
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 truncate">En Cours</p>
                        <p class="text-sm sm:text-lg lg:text-xl font-semibold text-gray-900 dark:text-white">{{
                            $hospitalisations->where('statut', 'en cours')->count() }}</p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-2 sm:p-3 lg:p-4 border-l-4 border-blue-500 min-w-0">
                <div class="flex items-center">
                    <div class="p-1.5 sm:p-2 rounded-full bg-blue-100 dark:bg-blue-900 flex-shrink-0">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 lg:w-5 lg:h-5 text-blue-600 dark:text-blue-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <div class="ml-2 sm:ml-3 min-w-0 flex-1">
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 truncate">Terminées</p>
                        <p class="text-sm sm:text-lg lg:text-xl font-semibold text-gray-900 dark:text-white">{{
                            $hospitalisations->where('statut', 'terminé')->count() }}</p>
                    </div>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-2 sm:p-3 lg:p-4 border-l-4 border-red-500 min-w-0">
                <div class="flex items-center">
                    <div class="p-1.5 sm:p-2 rounded-full bg-red-100 dark:bg-red-900 flex-shrink-0">
                        <svg class="w-3 h-3 sm:w-4 sm:h-4 lg:w-5 lg:h-5 text-red-600 dark:text-red-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div class="ml-2 sm:ml-3 min-w-0 flex-1">
                        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 truncate">Annulées</p>
                        <p class="text-sm sm:text-lg lg:text-xl font-semibold text-gray-900 dark:text-white">{{
                            $hospitalisations->where('statut', 'annulé')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des hospitalisations responsive -->
    <div class="table-container">
        <table class="table-main">
            <thead class="table-header">
                <tr>
                    <th>ID</th>
                    <th>Patient</th>
                    <th>Médecin</th>
                    <th>Service</th>
                    <th>Période</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="table-body">
                @forelse($hospitalisations as $hosp)
                <tr>
                    <td class="table-cell">
                        <span
                            class="text-sm font-mono text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                            #{{ $hosp->id }}
                        </span>
                    </td>
                    <td class="table-cell-medium">
                        <div>
                            <div class="font-medium">{{ $hosp->patient->nom ?? '-' }} {{ $hosp->patient->prenom ?? '' }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                @if($hosp->lit && $hosp->lit->chambre)
                                Chambre: {{ $hosp->lit->chambre->nom }} - Lit {{ $hosp->lit->numero }}
                                @else
                                Chambre: Non assignée
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="table-cell">Dr. {{ $hosp->medecin->nom ?? '-' }}</td>
                    <td class="table-cell">{{ $hosp->service->nom ?? '-' }}</td>
                    <td class="table-cell">
                        <div>
                            <div class="font-medium">{{ \Carbon\Carbon::parse($hosp->date_entree)->format('d/m/Y') }}
                            </div>
                            @if($hosp->date_sortie)
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                Sortie: {{ \Carbon\Carbon::parse($hosp->date_sortie)->format('d/m/Y') }}
                            </div>
                            @endif
                        </div>
                    </td>
                    <td class="table-cell">
                        @if($hosp->statut === 'en cours')
                        <span
                            class="inline-flex items-center gap-1 text-xs bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 px-2 py-1 rounded-full font-medium">
                            En Cours
                        </span>
                        @elseif($hosp->statut === 'terminé')
                        <span
                            class="inline-flex items-center gap-1 text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-2 py-1 rounded-full font-medium">
                            Terminé
                        </span>
                        @else
                        <span
                            class="inline-flex items-center gap-1 text-xs bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 px-2 py-1 rounded-full font-medium">
                            Annulé
                        </span>
                        @endif
                    </td>
                    <td class="table-cell">
                        <div class="flex space-x-2">
                            <a href="{{ route('hospitalisations.show', $hosp->id) }}"
                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('hospitalisations.edit', $hosp->id) }}"
                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 p-1">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('hospitalisations.destroy', $hosp->id) }}" method="POST"
                                class="inline"
                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette hospitalisation ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="table-cell text-center py-8">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-gray-400 dark:text-gray-600 mb-3" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                            <h3 class="text-base font-medium text-gray-900 dark:text-white mb-2">Aucune hospitalisation
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Commencez par créer une nouvelle
                                hospitalisation</p>
                            <a href="{{ route('hospitalisations.create') }}" class="form-button">
                                Créer une hospitalisation
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination responsive -->
    @if($hospitalisations->hasPages())
    <div class="mt-4 sm:mt-6 flex justify-center">
        {{ $hospitalisations->links() }}
    </div>
    @endif
</div>
@endsection
