@extends('layouts.app')

@section('content')
<div class="card mb-5">
    <div class="card-header">
        <div class="flex justify-between items-center">
            <h2 class="card-title">Historique des Opérations</h2>
            <a href="{{ route('modepaiements.dashboard') }}"
                class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                ← Retour au Dashboard
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-container">
            <table class="table-main">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="table-header">
                            Date
                        </th>
                        <th class="table-header">
                            Type d'Opération
                        </th>
                        <th class="table-header">
                            Description
                        </th>
                        <th class="table-header">
                            Montant
                        </th>
                        <th class="table-header">
                            Mode de Paiement
                        </th>
                        <th class="table-header">
                            Source
                        </th>
                        <th class="table-header">
                            Type
                        </th>
                    </tr>
                </thead>
                <tbody class="table-body">
                    @forelse($historique as $operation)
                    <tr class="table-row hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="table-cell">
                       {{ $operation['date']->diffForHumans() }} </td>
                        <td class="table-cell">
                            {{ $operation['type_operation'] }}
                        </td>
                        <td class="table-cell">
                            {{ $operation['description'] }}
                        </td>
                        <td class="table-cell font-medium">
                            <span
                                class="{{ $operation['operation'] === 'entree' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $operation['operation'] === 'entree' ? '+' : '-' }}
                                {{ number_format($operation['montant'], 0, ',', ' ') }} MRU
                            </span>
                        </td>
                        <td class="table-cell">
                            {{ ucfirst($operation['mode_paiement']) }}
                        </td>
                        <td class="table-cell">
                            {{ ucfirst($operation['source']) }}
                        </td>
                        <td class="table-cell">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $operation['operation'] === 'entree' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' }}">
                                {{ $operation['operation'] === 'entree' ? 'Entrée' : 'Sortie' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="table-cell text-center text-gray-500 dark:text-gray-400">
                            Aucune opération trouvée.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection