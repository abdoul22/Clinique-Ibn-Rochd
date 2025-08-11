@extends('layouts.app')
@section('title', 'Liste des Paiements')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="page-title">Liste des Paiements</h2>
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
                    <th class="table-header">Date</th>

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
                        @if($paiement->source === 'credit_assurance')
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            Paiement crédit assurance
                        </span>
                        @else
                        <span class="text-gray-400 dark:text-gray-500 italic">Aucune</span>
                        @endif
                        @endif
                    </td>
                    <td class="table-cell">
                        {{ optional($paiement->created_at)->format('d/m/Y - H:i') }}
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