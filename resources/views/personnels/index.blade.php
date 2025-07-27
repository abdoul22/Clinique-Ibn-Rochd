@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Liste du personnel</h1>
        <a href="{{ route('personnels.create') }}" class="form-button">
            + Ajouter
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="table-container">
        <table class="table-main">
            <thead class="table-header">
                <tr>
                    <th class="table-header">Nom</th>
                    <th class="table-header">Fonction</th>
                    <th class="table-header">Salaire</th>
                    <th class="table-header">Crédit</th>
                    <th class="table-header">Téléphone</th>
                    <th class="table-header">Adresse</th>
                    <th class="table-header">Actions</th>
                </tr>
            </thead>
            <tbody class="table-body">
                @forelse($personnels as $personnel)
                <tr class="table-row">
                    <td class="table-cell">{{ $personnel->nom }}</td>
                    <td class="table-cell">{{ $personnel->fonction }}</td>
                    <td class="table-cell">{{ $personnel->salaire }}</td>
                    <td class="table-cell">
                        @if ($personnel->statut_credit)
                        <span class="{{ $personnel->statut_color }}">
                            {{ ucfirst($personnel->statut_credit) }}
                        </span>
                        @else
                        <span class="text-gray-400 dark:text-gray-500">Aucun crédit</span>
                        @endif
                    </td>
                    <td class="table-cell">{{ $personnel->telephone }}</td>
                    <td class="table-cell">{{ $personnel->adresse }}</td>
                    <td class="table-cell table-actions">
                        <div class="flex space-x-2">
                            <a href="{{ route('personnels.show', $personnel) }}"
                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('personnels.edit', $personnel) }}"
                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 p-1">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('personnels.destroy', $personnel) }}" method="POST" class="inline"
                                onsubmit="return confirm('Supprimer ce personnel ?')">
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
                @empty
                <tr>
                    <td colspan="7" class="table-cell text-center text-gray-500 dark:text-gray-400 py-4">Aucun personnel
                        trouvé.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
