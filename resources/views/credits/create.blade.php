@extends('layouts.app')

@section('content')
<div class="p-4">
    <h1 class="text-xl font-bold mb-4">Liste des crédits</h1>

    <table class="min-w-full table-auto border-collapse border">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-4 py-2">Type</th>
                <th class="border px-4 py-2">Nom</th>
                <th class="border px-4 py-2">Montant</th>
                <th class="border px-4 py-2">Statut</th>
                <th class="border px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($credits as $credit)
            <tr class="border-t">
                <td class="px-4 py-2">{{ ucfirst($credit->type) }}</td>
                <td class="px-4 py-2">{{ $credit->nom_source }}</td>
                <td class="px-4 py-2">{{ number_format($credit->montant, 0, ',', ' ') }} MRU</td>
                <td class="px-4 py-2">
                    <span class="font-bold
                        @if($credit->statut === 'payé') text-green-600
                        @elseif($credit->statut === 'partiellement payé') text-yellow-600
                        @else text-red-600
                        @endif">
                        {{ ucfirst($credit->statut) }}
                    </span>
                </td>
                <td class="px-4 py-2 flex space-x-2">
                    @if($credit->statut !== 'payé')
                    <form method="POST"
                        action="{{ route('credits.marquer', ['id' => $credit->id, 'statut' => 'payé']) }}">
                        @csrf
                        <button class="text-green-600 font-bold hover:underline" type="submit">Marquer comme
                            payé</button>
                    </form>
                    <form method="POST"
                        action="{{ route('credits.marquer', ['id' => $credit->id, 'statut' => 'partiellement payé']) }}">
                        @csrf
                        <button class="text-yellow-600 font-bold hover:underline" type="submit">Partiellement
                            payé</button>
                    </form>
                    @else
                    <span class="text-gray-400">—</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
