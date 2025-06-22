@extends('layouts.app')

@section('content')
<div class="mr-6 ml-6">
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 space-y-4 lg:space-y-0">
        <h1 class="text-xl md:text-2xl font-bold">Prescripteurs</h1>

        <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0 w-full lg:w-auto">
            <!-- Bouton Ajouter -->
            <a href="{{ route('prescripteurs.create') }}"
                class="bg-cyan-600 hover:bg-cyan-700 text-white text-sm px-4 py-2 rounded transition">
                + Ajouter un prescripteur
            </a>

            <!-- Bouton PDF -->
            <a href="{{ route('prescripteurs.exportPdf') }} "
                class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Télécharger PDF
            </a>

            <!-- Bouton Impression -->
            <a href="{{ route('prescripteurs.print') }}" target="_blank"
                class="bg-gray-600 hover:bg-gray-700 text-white text-sm px-4 py-2 rounded flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2m-4 0h-4v4h4v-4z" />
                </svg>
                Imprimer
            </a>
        </div>
        </div>
        <!-- Tableau -->
        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-200 text-gray-700">
                    <tr>
                        <th class="py-2 px-4">ID</th>
                        <th class="py-2 px-4">Nom</th>
                        <th class="py-2 px-4">Spécialité</th>
                        <th class="py-2 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($prescripteurs as $prescripteur)
                    <tr class="border-t">
                        <td class="py-2 px-4">{{ $prescripteur->id }}</td>
                        <td class="py-2 px-4">{{ $prescripteur->nom }}</td>
                        <td class="py-2 px-4">{{ $prescripteur->specialite }}</td>
                        <td class="py-2 px-4">
                            <div class="flex space-x-2">
                                <!-- Voir -->
                                <a href="{{ route('prescripteurs.create') }}" class="text-green-600 hover:text-green-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>

                                <!-- Modifier -->


                                <a href="{{ route('prescripteurs.edit', $prescripteur->id) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536M16.768 4.768a2 2 0 112.828 2.828l-9.192 9.192H7v-3.192l9.768-9.828z" />
                                    </svg>
                                </a>

                                <!-- Supprimer -->
                                <form action="{{ route('prescripteurs.destroy', $prescripteur->id) }}" method="POST"
                                    onsubmit="return confirm('Êtes-vous sûr ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="py-4">
            {{ $prescripteurs->links() }}
        </div>
</div>



@endsection
