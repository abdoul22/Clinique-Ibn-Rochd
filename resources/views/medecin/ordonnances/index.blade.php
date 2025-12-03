@extends('layouts.app')
@section('title', 'Mes Ordonnances')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-4 sm:p-8">
    <div class="max-w-7xl mx-auto">
        <!-- En-tête -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">💊 Mes Ordonnances</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Liste de toutes vos ordonnances</p>
            </div>
            <a href="{{ route('medecin.ordonnances.create') }}" 
               class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition transform hover:scale-105 font-semibold">
                + Nouvelle Ordonnance
            </a>
        </div>

        <!-- Filtres -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher (réf., patient)..."
                       class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <input type="date" name="date" value="{{ request('date') }}"
                       class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                    Filtrer
                </button>
            </form>
        </div>

        <!-- Liste des ordonnances -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            @if($ordonnances->count() > 0)
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Référence</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Médicaments</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($ordonnances as $ordonnance)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $ordonnance->reference }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                                {{ $ordonnance->date_ordonnance->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $ordonnance->patient->first_name }} {{ $ordonnance->patient->last_name }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $ordonnance->patient->phone }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $ordonnance->medicaments->count() }} médicament(s)
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('medecin.ordonnances.show', $ordonnance->id) }}" 
                                   class="text-blue-600 hover:text-blue-900 dark:text-blue-400">Voir</a>
                                <a href="{{ route('medecin.ordonnances.print-page', $ordonnance->id) }}" 
                                   target="_blank"
                                   class="text-blue-600 hover:text-blue-900 dark:text-blue-400">🖨️</a>
                                <a href="{{ route('medecin.ordonnances.print', $ordonnance->id) }}" 
                                   target="_blank"
                                   class="text-green-600 hover:text-green-900 dark:text-green-400">PDF</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="px-6 py-4">
                    {{ $ordonnances->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">Aucune ordonnance trouvée</p>
                    <a href="{{ route('medecin.ordonnances.create') }}" 
                       class="mt-4 inline-block px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        Créer votre première ordonnance
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

