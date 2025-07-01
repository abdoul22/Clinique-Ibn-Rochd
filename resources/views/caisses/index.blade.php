@extends('layouts.app')

@section('content')
<!-- Titre + Boutons -->
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 space-y-4 lg:space-y-0">
    <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">Gestion des Factures</h1>

    <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0 w-full lg:w-auto">
        <!-- Bouton Ajouter -->
        <a href="{{ route('caisses.create') }}"
            class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-4 py-2 rounded transition flex items-center dark:bg-blue-700 dark:hover:bg-blue-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nouvel facture
        </a>

        <!-- Formulaire de recherche -->
        <form method="GET" action="{{ route('caisses.index') }}" class="flex flex-wrap gap-2 items-center">
            <input type="text" name="search" placeholder="Rechercher..." value="{{ request('search') }}"
                class="w-full md:w-auto border border-gray-300 dark:border-gray-600 rounded px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-900 text-gray-900 dark:text-white">

            <button type="submit"
                class="bg-blue-500 text-white px-4 py-1 text-sm rounded hover:bg-blue-600 transition dark:bg-blue-700 dark:hover:bg-blue-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>
        </form>
    </div>
</div>

<!-- Tableau -->
<div class="overflow-x-auto bg-white dark:bg-gray-800 rounded shadow dark:shadow-lg">
    <table class="min-w-full text-sm text-left">
        <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
            <tr>
                <th class="py-3 px-4">N° Entrée</th>
                <th class="py-3 px-4">Patient</th>
                <th class="py-3 px-4">Médecin</th>
                <th class="py-3 px-4">Date</th>
                <th class="py-3 px-4">Total</th>
                <th class="py-3 px-4">Caissier</th>
                <th class="py-3 px-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($caisses as $caisse)
            <tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900">
                <td class="py-3 px-4 font-medium">{{ $caisse->numero_entre }}</td>
                <td class="py-3 px-4">{{ $caisse->patient->first_name ?? 'N/A' }} {{ $caisse->patient->last_name ?? ''
                    }}</td>
                <td class="py-3 px-4">{{ $caisse->medecin->nom ?? 'N/A' }}</td>
                <td class="py-3 px-4">{{ $caisse->date_examen->format('d/m/Y') }}</td>
                <td class="py-3 px-4">{{ number_format($caisse->total, 2) }} MRU</td>
                <td class="py-3 px-4">{{ $caisse->nom_caissier }}</td>
                <td class="py-3 px-4">
                    <div class="flex space-x-2">
                        <!-- Voir -->
                        <a href="{{ route(auth()->user()->role->name . '.caisses.show', $caisse->id) }}"
                            class="text-blue-500 hover:text-blue-700 p-1 rounded-full hover:bg-blue-50 dark:hover:bg-blue-900/40"
                            title="Voir détails">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>

                        <!-- Modifier -->
                        <a href="{{ route(auth()->user()->role->name . '.caisses.edit', $caisse->id) }}"
                            class="text-yellow-500 hover:text-yellow-700 p-1 rounded-full hover:bg-yellow-50 dark:hover:bg-yellow-900/40"
                            title="Modifier">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>

                        <!-- Supprimer -->
                        <form action="{{ route(auth()->user()->role->name . '.caisses.destroy', $caisse->id) }}"
                            method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet examen ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-50 dark:hover:bg-red-900/40"
                                title="Supprimer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="py-4">
    {{ $caisses->links() }}
</div>
@endsection
