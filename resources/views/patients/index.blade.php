@extends('layouts.app')

@section('content')

<!-- Titre + Bouton Ajouter + Formulaire -->
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 space-y-4 lg:space-y-0">
    <h1 class="page-title">Liste des Patients</h1>

    <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0 w-full lg:w-auto">
        <!-- Bouton Ajouter -->
        <a href="{{ route(auth()->user()->role->name . '.patients.create') }}" class="form-button text-sm">
            + Ajouter un Patient
        </a>

        <!-- Formulaire de recherche -->
        <form method="GET" action="{{ route('patients.index') }}" class="flex flex-wrap gap-2 items-center">
            <!-- Recherche -->
            <input type="text" name="search" placeholder="Rechercher..." value="{{ request('search') }}"
                class="form-input text-sm w-full md:w-auto">

            <!-- Sexe -->
            <select name="sexe" class="form-select text-sm w-full md:w-auto">
                <option value="">Tous les sexes</option>
                <option value="Homme" {{ request('sexe')=='male' ? 'selected' : '' }}>Homme</option>
                <option value="Femme" {{ request('sexe')=='female' ? 'selected' : '' }}>Femme</option>
            </select>

            <!-- Type -->
            <select name="type_patient" class="form-select text-sm w-full md:w-auto">
                <option value="">Tous les types</option>
                <option value="Interne" {{ request('type_patient')=='Interne' ? 'selected' : '' }}>Interne</option>
                <option value="Externe" {{ request('type_patient')=='Externe' ? 'selected' : '' }}>Externe</option>
            </select>

            <!-- Bouton Filtrer -->
            <button type="submit" class="form-button text-sm">
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
                <th>Sexe</th>
                <th>Date de naissance</th>
                <th>Téléphone</th>
                <th>Adresse</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody class="table-body">
            @foreach($patients as $patient)
            <tr class="table-row">
                <td class="table-cell">{{ $patient->id }}</td>
                <td class="table-cell-medium">{{ $patient->first_name }} {{ $patient->last_name }}</td>
                <td class="table-cell">{{ $patient->gender }}</td>
                <td class="table-cell">{{ $patient->date_of_birth }}</td>
                <td class="table-cell">{{ $patient->phone }}</td>
                <td class="table-cell">{{ $patient->address }}</td>
                <td class="table-cell">
                    <div class="table-actions">
                        <!-- Voir -->
                        <a href="{{ route(auth()->user()->role->name . '.patients.show', $patient->id) }}"
                            class="action-btn action-btn-view">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.522 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S3.732 16.057 2.458 12z" />
                            </svg>
                        </a>

                        <!-- Modifier -->
                        <a href="{{ route(auth()->user()->role->name . '.patients.edit', $patient->id) }}"
                            class="action-btn action-btn-edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536M16.768 4.768a2 2 0 112.828 2.828l-9.192 9.192H7v-3.192l9.768-9.828z" />
                            </svg>
                        </a>

                        <!-- Supprimer -->
                        <form action="{{ route(auth()->user()->role->name . '.patients.destroy', $patient->id) }}"
                            method="POST" onsubmit="return confirm('Êtes-vous sûr ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn action-btn-delete">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
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
<div class="pagination-container">
    {{ $patients->links() }}
</div>

@endsection
