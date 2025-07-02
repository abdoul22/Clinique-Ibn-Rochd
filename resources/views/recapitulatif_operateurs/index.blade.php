@extends('layouts.app')

@section('content')

<!-- Titre + Boutons -->
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 space-y-4 lg:space-y-0">
    <h1 class="text-xl md:text-2xl font-bold dark:text-gray-100">Récapitulatif des opérateurs</h1>

    <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0 w-full lg:w-auto">
        <!-- Boutons Export/Print -->
        <a href="{{ route('recap-operateurs.exportPdf') }}"
            class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded transition dark:bg-red-600 dark:hover:bg-red-700">PDF</a>
        <a href="{{ route('recap-operateurs.print') }}" target="_blank"
            class="bg-green-500 hover:bg-green-600 text-white text-sm px-4 py-2 rounded transition dark:bg-green-600 dark:hover:bg-green-700">Imprimer</a>

        <!-- Formulaire de recherche -->
        <form method="GET" action="{{ route('recap-operateurs.index') }}" class="flex flex-wrap gap-2 items-center">
            <input type="text" name="search" placeholder="Rechercher..." value="{{ request('search') }}"
                class="w-full md:w-auto border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-100 dark:border-gray-700 dark:placeholder-gray-400">
            <button type="submit"
                class="bg-blue-500 text-white px-4 py-1 text-sm rounded hover:bg-blue-600 transition dark:bg-blue-600 dark:hover:bg-blue-700">
                Filtrer
            </button>
        </form>
    </div>
</div>

<!-- Tableau -->
<div class="overflow-x-auto bg-white rounded shadow dark:bg-gray-900 dark:shadow-lg dark:border dark:border-gray-700">
    <table class="min-w-full text-sm text-left">
        <thead class="bg-gray-200 text-gray-700 dark:bg-gray-800 dark:text-gray-200">
            <tr>
                <th class="py-2 px-4">ID</th>
                <th class="py-2 px-4">Médecin</th>
                <th class="py-2 px-4">Service</th>
                <th class="py-2 px-4">Nombre</th>
                <th class="py-2 px-4">Tarif</th>
                <th class="py-2 px-4">Recettes</th>
                <th class="py-2 px-4">Part Médecin</th>
                <th class="py-2 px-4">Part Clinique</th>
                <th class="py-2 px-4">Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recapOperateurs as $recap)
            <tr class="border-t dark:border-gray-700 dark:text-gray-100">
                <td class="py-2 px-4">{{ $loop->iteration }}</td>
                <td class="py-2 px-4">{{ $recap->medecin?->nom ?? '—' }}</td>
                <td class="py-2 px-4">{{ $recap->service?->nom ?? '—' }}</td>
                <td class="py-2 px-4">{{ $recap->nombre }}</td>
                <td class="py-2 px-4">{{ number_format($recap->tarif, 0, ',', ' ') }} MRU</td>
                <td class="py-2 px-4">{{ number_format($recap->recettes, 0, ',', ' ') }} MRU</td>
                <td class="py-2 px-4">{{ number_format($recap->part_medecin, 0, ',', ' ') }} MRU</td>
                <td class="py-2 px-4">{{ number_format($recap->part_clinique, 0, ',', ' ') }} MRU</td>
                <td class="py-2 px-4">{{ \Carbon\Carbon::parse($recap->jour)->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr class="border-t dark:border-gray-700">
                <td colspan="9" class="py-2 px-4 text-center text-gray-500 dark:text-gray-400">Aucun enregistrement
                    trouvé.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="py-4">
    {{ $recapOperateurs->links() }}
</div>

@endsection
