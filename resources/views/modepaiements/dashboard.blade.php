@extends('layouts.app')

@section('content')
<div class="bg-white rounded-2xl mb-5 shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100">
        <h2 class="text-lg font-semibold text-gray-900">Mode des Paiements</h2>
    </div>
    <div class="p-6">
        <div class="space-y-5">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($data as $item)
                <div class="rounded-xl p-4 bg-gradient-to-br from-gray-100 to-white shadow">
                    <p class="text-sm font-medium text-gray-600">{{ $item['mode'] }}</p>
                    <p class="text-sm text-green-600">Entrée : {{ number_format($item['entree'], 0, ',', ' ') }} MRU</p>
                    <p class="text-sm text-red-600">Sortie : {{ number_format($item['sortie'], 0, ',', ' ') }} MRU</p>
                    <p class="text-xl font-semibold text-gray-900 mt-1">Solde : {{ number_format($item['solde'], 0, ',',
                        ' ') }} MRU</p>
                </div>
                @endforeach
                <div class="bg-lime-200 rounded-xl p-4 col-span-2 md:col-span-1">
                    <p class="text-sm font-medium text-gray-700">Total Trésorerie</p>
                    <p class="text-xl font-semibold text-gray-900 mt-1">{{ number_format($totalGlobal, 0, ',', ' ') }}
                        MRU</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
