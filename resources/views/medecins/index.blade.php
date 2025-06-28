@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-6">
    <!-- Header avec titre et boutons -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Liste des Médecins</h1>
            <p class="text-gray-600 mt-1">Gérez les informations des médecins</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <!-- Bouton Ajouter -->
            <a href="{{ route(auth()->user()->role->name . '.medecins.create') }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-sm transition flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Ajouter un Médecin
            </a>

            <!-- Formulaire de recherche -->
            <form method="GET" action="{{ route(auth()->user()->role->name . '.medecins.index') }}"
                class="flex flex-1 gap-2">
                <div class="relative flex-1">
                    <input type="text" name="search" placeholder="Rechercher..." value="{{ request('search') }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-2.5 text-gray-400"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <button type="submit"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg shadow-sm transition">
                    Filtrer
                </button>
            </form>
        </div>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-700">
                <tr>
                    <th class="py-3 px-4">ID</th>
                    <th class="py-3 px-4">Nom</th>
                    <th class="py-3 px-4">Prénom</th>
                    <th class="py-3 px-4">Spécialité</th>
                    <th class="py-3 px-4">Téléphone</th>
                    <th class="py-3 px-4 text-center">Status</th>
                    <th class="py-3 px-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($medecins as $medecin)
                <tr class="hover:bg-gray-50 transition">
                    <td class="py-4 px-4">{{ $medecin->id }}</td>
                    <td class="py-4 px-4 font-medium">{{ $medecin->nom }}</td>
                    <td class="py-4 px-4">{{ $medecin->prenom }}</td>
                    <td class="py-4 px-4">{{ $medecin->specialite }}</td>
                    <td class="py-4 px-4">{{ $medecin->telephone }}</td>
                    <td class="py-4 px-4 text-center">
                        <a href="{{ route('medecins.stats', $medecin->id) }}"
                            class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white text-xs px-3 py-1 rounded-full transition">
                            Status
                        </a>
                    </td>
                    <td class="py-4 px-4">
                        <div class="flex justify-center space-x-3">
                            <!-- Voir -->
                            <a href="{{ route(auth()->user()->role->name . '.medecins.show', $medecin->id) }}"
                                class="text-gray-500 hover:text-gray-700 p-1.5 rounded-full hover:bg-gray-100 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.522 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S3.732 16.057 2.458 12z" />
                                </svg>
                            </a>
                            <!-- Modifier -->
                            <a href="{{ route(auth()->user()->role->name . '.medecins.edit', $medecin->id) }}"
                                class="text-indigo-500 hover:text-indigo-700 p-1.5 rounded-full hover:bg-indigo-50 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536M16.768 4.768a2 2 0 112.828 2.828l-9.192 9.192H7v-3.192l9.768-9.828z" />
                                </svg>
                            </a>
                            <!-- Supprimer -->
                            <form action="{{ route(auth()->user()->role->name . '.medecins.destroy', $medecin->id) }}"
                                method="POST" onsubmit="return confirm('Êtes-vous sûr ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-red-500 hover:text-red-700 p-1.5 rounded-full hover:bg-red-50 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
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
    <div class="mt-6">
        {{ $medecins->links() }}
    </div>
</div>

@endsection
