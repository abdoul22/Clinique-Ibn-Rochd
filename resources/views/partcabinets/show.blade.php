@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Détail de la Part du Cabinet</h1>

    <div class="space-y-4">
        <div>
            <strong class="mr-6">Prestation :</strong> {{ $partcabinet->prestation }}
        </div>

        <div>
            <strong class="mr-6">Prix :</strong> {{ number_format($partcabinet->prix, 2) }} MRU
        </div>

        <div>
            <strong class="mr-6">Part Cabinet :</strong> {{ number_format($partcabinet->part_cabinet, 2) }} MRU
        </div>

        <div>
            <strong class="mr-6">Part Médecin :</strong> {{ number_format($partcabinet->part_medecin, 2) }} MRU
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('partcabinets.index') }}"
            class="inline-block bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
            Retour à la liste
        </a>
    </div>
</div>
@endsection
