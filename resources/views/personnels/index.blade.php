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
                    <th class="table-header">Statut</th>
                    <th class="table-header">Actions</th>
                </tr>
            </thead>
            <tbody class="table-body">
                @forelse($personnels as $personnel)
                <tr class="table-row {{ $personnel['type'] === 'user' ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                    <td class="table-cell">
                        <div class="flex items-center">
                            @if($personnel['type'] === 'user')
                            <i class="fas fa-user-shield text-blue-600 mr-2" title="Utilisateur système"></i>
                            @else
                            <i class="fas fa-user text-gray-600 mr-2" title="Personnel"></i>
                            @endif
                            {{ $personnel['nom'] }}
                        </div>
                    </td>
                    <td class="table-cell">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $personnel['type'] === 'user' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                            {{ $personnel['fonction'] }}
                        </span>
                    </td>
                    <td class="table-cell">
                        @if($personnel['salaire'] > 0)
                        {{ number_format($personnel['salaire'], 2) }} MRU
                        @else
                        <span class="text-gray-400 italic">Non défini</span>
                        @endif
                    </td>
                    <td class="table-cell">
                        @if($personnel['credit'] > 0)
                        <span class="text-red-600 dark:text-red-400 font-semibold">
                            {{ number_format($personnel['credit'], 2) }} MRU
                        </span>
                        @else
                        <span class="text-gray-400 dark:text-gray-500">Aucun crédit</span>
                        @endif
                    </td>
                    <td class="table-cell">
                        @if($personnel['telephone'])
                        {{ $personnel['telephone'] }}
                        @else
                        <span class="text-gray-400 italic">Non renseigné</span>
                        @endif
                    </td>
                    <td class="table-cell">
                        @if($personnel['adresse'])
                        {{ $personnel['adresse'] }}
                        @else
                        <span class="text-gray-400 italic">Non renseignée</span>
                        @endif
                    </td>
                    <td class="table-cell">
                        @if($personnel['is_approved'])
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                            <i class="fas fa-check mr-1"></i>Approuvé
                        </span>
                        @else
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                            <i class="fas fa-clock mr-1"></i>En attente
                        </span>
                        @endif
                    </td>
                    <td class="table-cell table-actions">
                        <div class="flex space-x-2">
                            @if($personnel['type'] === 'personnel' || ($personnel['type'] === 'user' &&
                            is_numeric($personnel['id'])))
                            {{-- Personnel normal OU personnel lié à un utilisateur (avec ID numérique) --}}
                            <script>
                                document.addEventListener('DOMContentLoaded', function(){
                                  const row = document.currentScript.closest('tr');
                                  const approved = {{ $personnel['is_approved'] ? 'true' : 'false' }};
                                  // Désactiver tout bouton de crédit s'il en existe (par convention data-action="credit")
                                  const creditBtns = row.querySelectorAll('[data-action="credit"]');
                                  creditBtns.forEach(btn => {
                                    if(!approved){
                                      btn.classList.add('opacity-50','cursor-not-allowed');
                                      btn.addEventListener('click', function(e){ e.preventDefault(); alert('Ce personnel n\'est pas approuvé. Impossible de lui attribuer un crédit.'); });
                                    }
                                  });
                                });
                            </script>
                            <a href="{{ route('personnels.show', $personnel['id']) }}"
                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1"
                                title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('personnels.edit', $personnel['id']) }}"
                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 p-1"
                                title="@if($personnel['type'] === 'user' && is_numeric($personnel['id']))Modifier (édition limitée)@else Modifier @endif">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($personnel['type'] === 'personnel')
                            {{-- Bouton de suppression uniquement pour le personnel normal --}}
                            <form action="{{ route('personnels.destroy', $personnel['id']) }}" method="POST"
                                class="inline" onsubmit="return confirm('Supprimer ce personnel ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1"
                                    title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @else
                            {{-- Pour personnel lié aux utilisateurs, lien vers suppression utilisateur --}}
                            <a href="{{ route('superadmin.admins.index') }}"
                                class="text-orange-600 hover:text-orange-900 dark:text-orange-400 dark:hover:text-orange-300 p-1"
                                title="Supprimer via module utilisateurs">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                            @endif
                            @else
                            {{-- Entrées virtuelles user_X (non créées) --}}
                            <span class="text-gray-400 text-xs italic">Géré depuis les utilisateurs</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="table-cell text-center text-gray-500 dark:text-gray-400 py-4">
                        Aucun personnel trouvé.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        <div class="flex justify-center gap-2">
            <div class="sm:hidden">
                {{ $personnels->appends(request()->query())->links('pagination::simple-tailwind') }}
            </div>
            <div class="hidden sm:block">
                {{ $personnels->onEachSide(1)->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
