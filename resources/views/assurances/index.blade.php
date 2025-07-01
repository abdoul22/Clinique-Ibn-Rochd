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
                        <!-- Modifier -->
                        <a href="{{ route('assurances.edit', $assurance->id) }}"
                            class="text-indigo-500 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536M16.768 4.768a2 2 0 112.828 2.828l-9.192 9.192H7v-3.192l9.768-9.828z" />
                            </svg>
                        </a>

                        <!-- Supprimer -->
                        <form action="{{ route('assurances.destroy', $assurance->id) }}" method="POST"
                            onsubmit="return confirm('Êtes-vous sûr ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22" />
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
<div class="py-4 dark:text-gray-100">
    {{ $assurances->links() }}
</div>
@endsection
