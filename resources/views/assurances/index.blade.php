@extends('layouts.app')

@section('content')
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 space-y-4 lg:space-y-0">
    <h1 class="text-xl md:text-2xl font-bold dark:text-gray-100">Liste des assurances</h1>

    <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0 w-full lg:w-auto">
        <!-- Bouton Ajouter -->
        <a href="{{ route('assurances.create') }}"
            class="bg-cyan-600 hover:bg-cyan-700 text-white text-sm px-4 py-2 rounded transition dark:bg-cyan-700 dark:hover:bg-cyan-800 dark:text-gray-100">
            + Ajouter une assurance
        </a>

        <!-- Bouton PDF -->
        <a href="{{ route('assurances.exportPdf') }}"
            class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded flex items-center dark:bg-red-700 dark:hover:bg-red-800 dark:text-gray-100">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Télécharger PDF
        </a>

        <!-- Bouton Impression -->
        <a href="{{ route('assurances.print') }}" target="_blank"
            class="bg-gray-600 hover:bg-gray-700 text-white text-sm px-4 py-2 rounded flex items-center dark:bg-gray-800 dark:hover:bg-gray-900 dark:text-gray-100">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2m-4 0h-4v4h4v-4z" />
            </svg>
            Imprimer
        </a>
    </div>
</div>

<!-- Tableau -->
<div class="overflow-x-auto bg-white rounded shadow dark:bg-gray-900 dark:shadow-lg dark:border dark:border-gray-700">
    <table class="min-w-full text-sm text-left dark:text-gray-100">
        <thead class="bg-gray-200 text-gray-700 dark:bg-gray-800 dark:text-gray-200">
            <tr>
                <th class="py-2 px-4">ID</th>
                <th class="py-2 px-4">Nom</th>
                <th class="py-2 px-4">Credit</th>
                <th class="py-2 px-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assurances as $assurance)
            <tr class="border-t dark:border-gray-700">
                <td class="py-2 px-4">{{ $assurance->id }}</td>
                <td class="py-2 px-4">{{ $assurance->nom }}</td>
                <td class="py-2 px-4">{{ $assurance->credit_format }}</td>
                <td class="py-2 px-4">
                    <div class="flex space-x-2">
                        <!-- Voir -->
                        <a href="{{ route(auth()->user()->role->name . '.assurances.show', $assurance->id) }}"
                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1">
                            <i class="fas fa-eye"></i>
                        </a>

                        <!-- Modifier -->
                        <a href="{{ route(auth()->user()->role->name . '.assurances.edit', $assurance->id) }}"
                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 p-1">
                            <i class="fas fa-edit"></i>
                        </a>

                        <!-- Supprimer -->
                        <form action="{{ route(auth()->user()->role->name . '.assurances.destroy', $assurance->id) }}"
                            method="POST" onsubmit="return confirm('Êtes-vous sûr ?')" class="inline">
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
            @endforeach
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="py-4 dark:text-gray-100">
    {{ $assurances->links() }}
</div>
@endsection
