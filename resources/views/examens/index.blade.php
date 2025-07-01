@extends('layouts.app')

@section('content')
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 space-y-4 lg:space-y-0">
    <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">Liste des examens</h1>
    <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0 w-full lg:w-auto">
        <!-- Bouton Ajouter -->
        <a href="{{ route('examens.create') }}"
            class="bg-cyan-600 dark:bg-cyan-800 hover:bg-cyan-700 dark:hover:bg-cyan-900 text-white text-sm px-4 py-2 rounded transition">
            + Ajouter un examen
        </a>
        <!-- Bouton PDF -->
        <a href="{{ route('examens.exportPdf') }}"
            class="bg-red-500 dark:bg-red-700 hover:bg-red-600 dark:hover:bg-red-900 text-white text-sm px-4 py-2 rounded flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Télécharger PDF
        </a>
        <!-- Bouton Impression -->
        <a href="{{ route('examens.print') }}" target="_blank"
            class="bg-gray-600 dark:bg-gray-800 hover:bg-gray-700 dark:hover:bg-gray-900 text-white text-sm px-4 py-2 rounded flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2h-2m-4 0h-4v4h4v-4z" />
            </svg>
            Imprimer
        </a>
    </div>
</div>
<!-- Tableau -->
<div class="overflow-x-auto bg-white dark:bg-gray-800 rounded shadow dark:shadow-lg">
    <table class="min-w-full text-sm text-left">
        <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
            <tr>
                <th class="py-2 px-4">ID</th>
                <th class="py-2 px-4">Nom</th>
                <th class="py-2 px-4">Service</th>
                <th class="py-2 px-4">Tarif</th>
                <th class="py-2 px-4">Part Medecins</th>
                <th class="py-2 px-4">Part Cabinet</th>
                <th class="py-2 px-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($examens as $examen)
            <tr class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900">
                <td class="py-2 px-4 text-gray-900 dark:text-gray-100">{{ $examen->id }}</td>
                <td class="py-2 px-4 text-gray-900 dark:text-gray-100">{{ $examen->nom }}</td>
                <td class="py-2 px-4 text-gray-900 dark:text-gray-100">{{ $examen->service->nom ?? '-' }}</td>
                <td class="py-2 px-4 text-gray-900 dark:text-gray-100">{{ number_format($examen->tarif, 0, ',', ' ') }}
                    MRU</td>
                <td class="py-2 px-4 text-gray-900 dark:text-gray-100">{{ number_format($examen->part_medecin, 0, ',', '
                    ') }} MRU</td>
                <td class="py-2 px-4 text-gray-900 dark:text-gray-100">{{ number_format($examen->part_cabinet, 0, ',', '
                    ') }} MRU</td>
                <td class="py-2 px-4">
                    <div class="flex space-x-2">
                        <!-- Modifier -->
                        <a href="{{ route('examens.edit', $examen->id) }}"
                            class="text-indigo-500 dark:text-indigo-300 hover:text-indigo-700 dark:hover:text-indigo-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536M16.768 4.768a2 2 0 112.828 2.828l-9.192 9.192H7v-3.192l9.768-9.828z" />
                            </svg>
                        </a>
                        <!-- Supprimer -->
                        <form action="{{ route('examens.destroy', $examen->id) }}" method="POST"
                            onsubmit="return confirm('Êtes-vous sûr ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
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
<div class="py-4">
    {{ $examens->links() }}
</div>
@endsection
