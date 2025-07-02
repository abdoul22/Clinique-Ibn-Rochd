{{-- resources/views/recap-services/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 space-y-4 lg:space-y-0">
    <h1 class="text-xl md:text-2xl font-bold">Récapitulatif journalier des services</h1>

    <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0 w-full lg:w-auto">
        <a href="{{ route('recap-services.exportPdf') }}"
            class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded transition">PDF</a>
        <a href="{{ route('recap-services.print') }}" target="_blank"
            class="bg-green-500 hover:bg-green-600 text-white text-sm px-4 py-2 rounded transition">Imprimer</a>

        <form method="GET" action="{{ route('recap-services.index') }}" class="flex flex-wrap gap-2 items-center">
            <input type="text" name="search" placeholder="Rechercher par service..." value="{{ request('search') }}"
                class="w-full md:w-auto border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-blue-500 text-white px-4 py-1 text-sm rounded hover:bg-blue-600 transition">
                Filtrer
            </button>
        </form>
    </div>
</div>

<div class="overflow-x-auto bg-white rounded shadow">
    <table class="min-w-full text-sm text-left">
        <thead class="bg-gray-200 text-gray-700">
            <tr>
                <th class="py-2 px-4">Service</th>
                <th class="py-2 px-4">Nombre d'actes</th>
                <th class="py-2 px-4">Total</th>
                <th class="py-2 px-4">Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recaps as $recap)
            <tr class="border-t">
                <td class="py-2 px-4">{{ $recap->service->nom ?? '—' }}</td>
                <td class="py-2 px-4">{{ $recap->nombre }}</td>
                <td class="py-2 px-4">{{ number_format($recap->total, 0, ',', ' ') }} MRU</td>
                <td class="py-2 px-4">{{ \Carbon\Carbon::parse($recap->jour)->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr class="border-t">
                <td colspan="4" class="py-2 px-4 text-center text-gray-500">Aucun enregistrement trouvé.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="py-4">
    {{ $recaps->links() }}
</div>
@endsection