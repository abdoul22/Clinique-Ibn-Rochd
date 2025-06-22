@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold text-gray-700">Liste des Paiements</h2>
        <a href="{{ route('modepaiements.create') }}"
            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            + Nouveau Paiement
        </a>
    </div>

    @if (session('success'))
    <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-500 uppercase">#</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-500 uppercase">Montant</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-500 uppercase">Facture Caisse</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($paiements as $paiement)
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $paiement->id }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700 capitalize">{{ $paiement->type }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ number_format($paiement->montant, 2, ',', ' ') }} MRU
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        @if ($paiement->caisse)
                        <a href="{{ route(auth()->user()->role->name . '.caisses.show', $paiement->caisse_id) }}"
                            class="text-blue-600 hover:underline">
                            Facture n°{{ $paiement->caisse->id }}
                        </a>
                        @else
                        <span class="text-gray-400 italic">Aucune</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700 space-x-2">
                        <a href="{{ route('modepaiements.show', $paiement->id) }}"
                            class="text-indigo-600 hover:text-indigo-900">Voir</a>
                        <a href="{{ route('modepaiements.edit', $paiement->id) }}"
                            class="text-yellow-600 hover:text-yellow-800">Modifier</a>
                        <form action="{{ route('modepaiements.destroy', $paiement->id) }}" method="POST"
                            class="inline-block" onsubmit="return confirm('Supprimer ce paiement ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">Supprimer</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucun paiement trouvé.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $paiements->links() }}
    </div>
</div>
@endsection
