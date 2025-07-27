@extends('layouts.app')
@section('title', 'Liste des Médecins')

@section('content')

<div class="max-w-7xl mx-auto px-4 py-6">
    <!-- Header avec titre et boutons -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="page-title">Liste des Médecins</h1>
            <p class="page-subtitle">Gérez les informations des médecins</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <!-- Bouton Ajouter -->
            <a href="{{ route(auth()->user()->role->name . '.medecins.create') }}"
                class="form-button flex items-center justify-center gap-2">
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
                        class="form-input pl-10">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 absolute left-3 top-2.5 text-gray-400 dark:text-gray-500" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <button type="submit" class="form-button-secondary">
                    Filtrer
                </button>
            </form>
        </div>
    </div>

    <!-- Tableau -->
    <div class="table-container">
        <table class="table-main">
            <thead class="table-header">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Spécialité</th>
                    <th>Téléphone</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="table-body">
                @foreach($medecins as $medecin)
                <tr class="table-row">
                    <td class="table-cell">{{ $medecin->id }}</td>
                    <td class="table-cell-medium">{{ $medecin->nom }}</td>
                    <td class="table-cell">{{ $medecin->prenom }}</td>
                    <td class="table-cell">{{ $medecin->specialite }}</td>
                    <td class="table-cell">{{ $medecin->telephone }}</td>
                    <td class="table-cell text-center">
                        <a href="{{ route('medecins.stats', $medecin->id) }}" class="status-badge status-badge-primary">
                            Status
                        </a>
                    </td>
                    <td class="table-cell">
                        <div class="flex space-x-2">
                            <!-- Voir -->
                            <a href="{{ route(auth()->user()->role->name . '.medecins.show', $medecin->id) }}"
                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1">
                                <i class="fas fa-eye"></i>
                            </a>
                            <!-- Modifier -->
                            <a href="{{ route(auth()->user()->role->name . '.medecins.edit', $medecin->id) }}"
                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 p-1">
                                <i class="fas fa-edit"></i>
                            </a>
                            <!-- Supprimer -->
                            <form action="{{ route(auth()->user()->role->name . '.medecins.destroy', $medecin->id) }}"
                                method="POST" onsubmit="return confirm('Êtes-vous sûr ?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1">
                                    <i class="fas fa-trash"></i>
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
    <div class="pagination-container">
        {{ $medecins->links() }}
    </div>
</div>

@endsection
