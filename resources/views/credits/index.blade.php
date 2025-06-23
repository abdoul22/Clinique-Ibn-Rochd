@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">Liste des Crédits</h1>

    <table class="min-w-full bg-white border">
        <thead>
            <tr>
                <th class="px-4 py-2 border">#</th>
                <th class="px-4 py-2 border">Type</th>
                <th class="px-4 py-2 border">Nom</th>
                <th class="px-4 py-2 border">Montant</th>
                <th class="px-4 py-2 border">Statut</th>
                <th class="px-4 py-2 border">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($credits as $credit)
            <tr class="border-t">
                <td class="px-4 py-2 border">{{ $credit->id }}</td>
                <td class="px-4 py-2 border">
                    {{ class_basename($credit->source_type) === 'Personnel' ? 'Personnel' : 'Assurance' }}
                </td>
                <td class="px-4 py-2 border">
                    {{ $credit->source?->nom ?? '---' }}
                </td>
                <td class="px-4 py-2 border">
                    {{ number_format($credit->montant, 0, ',', ' ') }} MRU
                </td>
                <td class="px-4 py-2 border">
                    @if($credit->status === 'non payé')
                    <span class="text-red-500 font-semibold">Non payé</span>
                    @elseif($credit->status === 'partiellement payé')
                    <span class="text-yellow-500 font-semibold">Partiellement payé</span>
                    @else
                    <span class="text-green-600 font-semibold">Payé</span>
                    @endif
                </td>
                <td class="px-4 py-2 border">
                    @if($credit->status !== 'payé')
                    <a href="{{ route('credits.payer', $credit->id) }}"
                        class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                        Payer</a>
                    @else
                    ---
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
