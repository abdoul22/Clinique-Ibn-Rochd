@extends('layouts.app')

@section('content')

<!-- Titre + Bouton Ajouter + Formulaire -->
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 space-y-4 lg:space-y-0">
    <h1 class="text-xl md:text-2xl font-bold">Liste des Parts du Cabinet</h1>

    <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0 w-full lg:w-auto">
        <!-- Bouton Ajouter -->
        <a href="{{ route('partcabinets.create') }}"
            class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-4 py-2 rounded transition">
            + Ajouter une Part
        </a>

        <!-- Formulaire de recherche -->
        <form method="GET" action="{{ route('partcabinets.index') }}" class="flex gap-2 items-center">
            <input type="text" name="search" placeholder="Prestation ou tarif" value="{{ request('search') }}"
                class="w-full md:w-auto border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

            <button type="submit" class="bg-blue-500 text-white px-4 py-1 text-sm rounded hover:bg-blue-600 transition">
                Filtrer
            </button>
        </form>
    </div>
</div>

<!-- Tableau -->
<div class="overflow-x-auto bg-white rounded shadow">
    <table class="min-w-full text-sm text-left">
        <thead class="bg-gray-200 text-gray-700">
            <tr>
                <th class="py-2 px-4">ID</th>
                <th class="py-2 px-4">Prestation</th>
                <th class="py-2 px-4">Prix</th>
                <th class="py-2 px-4">Part Cabinet</th>
                <th class="py-2 px-4">Part Médecin</th>
                <th class="py-2 px-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($partcabinets as $part)
            <tr class="border-t">
                <td class="py-2 px-4">{{ $part->id }}</td>
                <td class="py-2 px-4">{{ $part->prestation }}</td>
                <td class="py-2 px-4">{{ number_format($part->prix, 2) }} MRU</td>
                <td class="py-2 px-4">{{ number_format($part->part_cabinet, 2) }} MRU</td>
                <td class="py-2 px-4">{{ number_format($part->part_medecin, 2) }} MRU</td>
                <td class="py-2 px-4">
                    <div class="flex space-x-2">
                        <!-- Voir -->
                        <a href="{{ route('partcabinets.show', $part->id) }}" class="text-gray-500 hover:text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.522 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S3.732 16.057 2.458 12z" />
                            </svg>
                        </a>
                        <!-- Modifier -->
                        <a href="{{ route('partcabinets.edit', $part->id) }}"
                            class="text-indigo-500 hover:text-indigo-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536M16.768 4.768a2 2 0 112.828 2.828l-9.192 9.192H7v-3.192l9.768-9.828z" />
                            </svg>
                        </a>
                        <!-- Supprimer -->
                        <form action="{{ route('partcabinets.destroy', $part->id) }}" method="POST"
                            onsubmit="return confirm('Êtes-vous sûr ?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22" />
                            </svg></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-4 px-4 text-center text-gray-500">Aucune part enregistrée.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if ($partcabinets->hasPages())
    <div class="mt-6 flex justify-center">
        {{ $partcabinets->links() }}
    </div>
@endif

@endsection
