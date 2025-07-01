@extends('layouts.app')

@section('content')

<!-- Titre + Boutons -->
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 space-y-4 lg:space-y-0">
    <h1 class="page-title">Liste des Dépenses</h1>

    <div class="flex flex-col md:flex-row md:items-center md:space-x-4 space-y-2 md:space-y-0 w-full lg:w-auto">
        <!-- Bouton Ajouter -->
        <a href="{{ route('depenses.create') }}"
            class="bg-blue-500 hover:bg-blue-600 text-white text-sm px-4 py-2 rounded transition">
            + Ajouter une Dépense
        </a>

        <!-- Boutons Export/Print -->
        <a href="{{ route('depenses.exportPdf') }}"
            class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded transition">PDF</a>
        <a href="{{ route('depenses.print') }}" target="_blank"
            class="bg-green-500 hover:bg-green-600 text-white text-sm px-4 py-2 rounded transition">Imprimer</a>
    </div>
</div>

<!-- Filtres -->
<div class="card mb-6">
    <form method="GET" action="{{ route('depenses.index') }}" class="flex flex-wrap gap-4 items-end">
        <div class="flex-1 min-w-48">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rechercher</label>
            <input type="text" name="search" placeholder="Nom de la dépense..." value="{{ request('search') }}"
                class="form-input text-sm">
        </div>

        <div class="min-w-32">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Source</label>
            <select name="source" class="form-select text-sm">
                <option value="">Toutes les sources</option>
                <option value="manuelle" {{ request('source')==='manuelle' ? 'selected' : '' }}>Manuelle</option>
                <option value="automatique" {{ request('source')==='automatique' ? 'selected' : '' }}>Automatique (Part
                    médecin)</option>
            </select>
        </div>

        <div class="min-w-32">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mode de paiement</label>
            <select name="mode_paiement" class="form-select text-sm">
                <option value="">Tous les modes</option>
                <option value="espèces" {{ request('mode_paiement')==='espèces' ? 'selected' : '' }}>Espèces</option>
                <option value="bankily" {{ request('mode_paiement')==='bankily' ? 'selected' : '' }}>Bankily</option>
                <option value="masrivi" {{ request('mode_paiement')==='masrivi' ? 'selected' : '' }}>Masrivi</option>
                <option value="sedad" {{ request('mode_paiement')==='sedad' ? 'selected' : '' }}>Sedad</option>
            </select>
        </div>

        <button type="submit" class="form-button text-sm">
            Filtrer
        </button>

        <a href="{{ route('depenses.index') }}"
            class="bg-gray-500 text-white px-4 py-2 text-sm rounded hover:bg-gray-600 transition">
            Réinitialiser
        </a>
    </form>
</div>

<!-- Tableau -->
<div class="table-container">
    <table class="table-main">
        <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
            <tr>
                <th class="table-header">ID</th>
                <th class="table-header">Nom</th>
                <th class="table-header">Montant</th>
                <th class="table-header">Mode de paiement</th>
                <th class="table-header">Source</th>
                <th class="table-header">Date</th>
                <th class="table-header">Actions</th>
            </tr>
        </thead>
        <tbody class="table-body">
            @foreach($depenses as $depense)
            <tr class="table-row hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="table-cell font-medium">{{ $depense->id }}</td>
                <td class="table-cell">
                    <div class="font-medium">{{ $depense->nom }}</div>
                    @if($depense->etat_caisse_id)
                    <div class="text-xs text-gray-500 dark:text-gray-400">État caisse #{{ $depense->etat_caisse_id }}
                    </div>
                    @endif
                </td>
                <td class="table-cell">
                    <span class="font-bold text-red-600 dark:text-red-400">{{ number_format($depense->montant, 0, ',', '
                        ') }} MRU</span>
                </td>
                <td class="table-cell">
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                        {{ ucfirst($depense->mode_paiement_id ?? 'Non défini') }}
                    </span>
                </td>
                <td class="table-cell">
                    @if($depense->source === 'automatique')
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                        Part médecin
                    </span>
                    @else
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                        {{ ucfirst($depense->source ?? 'Manuelle') }}
                    </span>
                    @endif
                </td>
                <td class="table-cell text-gray-600 dark:text-gray-400">
                    {{ $depense->created_at->format('d/m/Y H:i') }}
                </td>
                <td class="table-cell">
                    <div class="table-actions">
                        <!-- Voir -->
                        <a href="{{ route('depenses.show', $depense->id) }}" class="action-btn action-btn-view"
                            title="Voir">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.522 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S3.732 16.057 2.458 12z" />
                            </svg>
                        </a>
                        <!-- Modifier -->
                        <a href="{{ route('depenses.edit', $depense->id) }}" class="action-btn action-btn-edit"
                            title="Modifier">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536M16.768 4.768a2 2 0 112.828 2.828l-9.192 9.192H7v-3.192l9.768-9.828z" />
                            </svg>
                        </a>
                        <!-- Supprimer -->
                        <form action="{{ route('depenses.destroy', $depense->id) }}" method="POST"
                            onsubmit="return confirm('Supprimer cette dépense ?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn action-btn-delete" title="Supprimer">
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
    {{ $depenses->links() }}
</div>

@endsection
