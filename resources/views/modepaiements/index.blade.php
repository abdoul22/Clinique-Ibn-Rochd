@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="page-title">Liste des Paiements</h2>
        <a href="{{ route('modepaiements.create') }}"
            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            + Nouveau Paiement
        </a>
    </div>

    @if (session('success'))
    <div class="alert alert-success mb-4">
        {{ session('success') }}
    </div>
    @endif
    <div class="table-container">
        <table class="table-main">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="table-header">#</th>
                    <th class="table-header">Type</th>
                    <th class="table-header">Montant</th>
                    <th class="table-header">Facture Caisse</th>
                    <th class="table-header">Actions</th>
                </tr>
            </thead>
            <tbody class="table-body">
                @forelse($paiements as $paiement)
                <tr class="table-row">
                    <td class="table-cell">{{ $paiement->id }}</td>
                    <td class="table-cell capitalize">{{ $paiement->type }}</td>
                    <td class="table-cell">{{ number_format($paiement->montant, 2, ',', ' ') }} MRU
                    </td>
                    <td class="table-cell">
                        @if ($paiement->caisse)
                        <a href="{{ route('caisses.show', $paiement->caisse_id) }}"
                            class="text-blue-600 dark:text-blue-400 hover:underline">
                            Facture n°{{ $paiement->caisse->id }}
                        </a>
                        @else
                        <span class="text-gray-400 dark:text-gray-500 italic">Aucune</span>
                        @endif
                    </td>
                    <td class="table-cell space-x-2">
                        <a href="{{ route('modepaiements.show', $paiement->id) }}"
                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">Voir</a>
                        <a href="{{ route('modepaiements.edit', $paiement->id) }}"
                            class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-300">Modifier</a>
                        <form action="{{ route('modepaiements.destroy', $paiement->id) }}" method="POST"
                            class="inline-block" onsubmit="return confirm('Supprimer ce paiement ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">Supprimer</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="table-cell text-center text-gray-500 dark:text-gray-400">Aucun paiement
                        trouvé.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-container">
        {{ $paiements->links() }}
    </div>
</div>
@endsection
