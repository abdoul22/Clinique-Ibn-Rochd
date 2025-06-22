@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Liste du personnel</h1>
        <a href="{{ route('personnels.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            + Ajouter
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 text-green-700 p-2 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white shadow rounded-lg">
        <table class="w-full table-auto">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">Nom</th>
                    <th class="px-4 py-2">Fonction</th>
                    <th class="px-4 py-2">Salaire</th>
                    <th class="px-4 py-2">Crédit</th>
                    <th class="px-4 py-2">Téléphone</th>
                    <th class="px-4 py-2">Adresse</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($personnels as $personnel)
                <tr class="border-t">
                    <td class="px-4 py-2">{{ $personnel->nom }}</td>
                    <td class="px-4 py-2">{{ $personnel->fonction }}</td>
                    <td class="px-4 py-2">{{ $personnel->salaire }}</td>
                    <td class="px-4 py-2">{{ $personnel->credit }}</td>
                    <td class="px-4 py-2">{{ $personnel->telephone }}</td>
                    <td class="px-4 py-2">{{ $personnel->adresse }}</td>
                    <td class="px-4 py-2 space-x-2">
                        <a href="{{ route('personnels.show', $personnel) }}" class="text-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.522 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S3.732 16.057 2.458 12z" />
                            </svg>
                        </a>
                        <a href="{{ route('personnels.edit', $personnel) }}" class="text-yellow-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536M16.768 4.768a2 2 0 112.828 2.828l-9.192 9.192H7v-3.192l9.768-9.828z" />
                            </svg></a>
                        <form action="{{ route('personnels.destroy', $personnel) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:underline"
                                onclick="return confirm('Supprimer ce personnel ?')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22" />
                                    </svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-4 text-center text-gray-500">Aucun personnel trouvé.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
