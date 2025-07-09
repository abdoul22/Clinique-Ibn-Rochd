@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Hospitalisations</h1>
        <a href="{{ route('hospitalisations.create') }}"
            class="bg-blue-600 dark:bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-700 dark:hover:bg-blue-800">Nouvelle
            hospitalisation</a>
    </div>
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 dark:bg-gray-900">
                <tr>
                    <th class="px-4 py-2 text-gray-700 dark:text-gray-200">#</th>
                    <th class="px-4 py-2 text-gray-700 dark:text-gray-200">Patient</th>
                    <th class="px-4 py-2 text-gray-700 dark:text-gray-200">Médecin</th>
                    <th class="px-4 py-2 text-gray-700 dark:text-gray-200">Service</th>
                    <th class="px-4 py-2 text-gray-700 dark:text-gray-200">Entrée</th>
                    <th class="px-4 py-2 text-gray-700 dark:text-gray-200">Sortie</th>
                    <th class="px-4 py-2 text-gray-700 dark:text-gray-200">Statut</th>
                    <th class="px-4 py-2 text-gray-700 dark:text-gray-200">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hospitalisations as $hosp)
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $hosp->id }}</td>
                    <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $hosp->patient->nom ?? '-' }} {{
                        $hosp->patient->prenom ?? '' }}</td>
                    <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $hosp->medecin->nom ?? '-' }}</td>
                    <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $hosp->service->nom ?? '-' }}</td>
                    <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $hosp->date_entree }}</td>
                    <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $hosp->date_sortie ?? '-' }}</td>
                    <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ ucfirst($hosp->statut) }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('hospitalisations.show', $hosp->id) }}"
                            class="text-blue-600 dark:text-blue-400 hover:underline">Voir</a>
                        <a href="{{ route('hospitalisations.edit', $hosp->id) }}"
                            class="text-yellow-600 dark:text-yellow-400 hover:underline ml-2">Modifier</a>
                        <form action="{{ route('hospitalisations.destroy', $hosp->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 dark:text-red-400 hover:underline ml-2"
                                onclick="return confirm('Supprimer cette hospitalisation ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-gray-900 dark:text-gray-100">Aucune hospitalisation
                        trouvée.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $hospitalisations->links() }}</div>
</div>
@endsection
