@extends('layouts.app')
@section('title', 'Gestion Pharmacie')

@section('content')
<div class="w-full px-0 sm:px-2 lg:px-4 py-4 sm:py-8">
    <!-- En-tête -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 dark:text-gray-200">Gestion de la Pharmacie</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Gérez votre inventaire de médicaments</p>
        </div>
        <a href="{{ route('pharmacie.create') }}"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg flex items-center text-base w-full sm:w-auto justify-center">
            <i class="fas fa-plus mr-2"></i>Nouveau Médicament
        </a>
    </div>

    <!-- Filtres -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
        <form method="GET" action="{{ route('pharmacie.index') }}"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-6">
            <div>
                <label class="block text-base font-medium text-gray-700 dark:text-gray-300 mb-2">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Nom, catégorie, fournisseur..."
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-4 py-3 text-base">
            </div>

            <div>
                <label class="block text-base font-medium text-gray-700 dark:text-gray-300 mb-2">Statut</label>
                <select name="statut"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-4 py-3 text-base">
                    <option value="">Tous les statuts</option>
                    <option value="actif" {{ request('statut')=='actif' ? 'selected' : '' }}>Actif</option>
                    <option value="inactif" {{ request('statut')=='inactif' ? 'selected' : '' }}>Inactif</option>
                    <option value="rupture" {{ request('statut')=='rupture' ? 'selected' : '' }}>Rupture</option>
                </select>
            </div>

            <div>
                <label class="block text-base font-medium text-gray-700 dark:text-gray-300 mb-2">Stock</label>
                <select name="stock"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-4 py-3 text-base">
                    <option value="">Tous</option>
                    <option value="en_stock" {{ request('stock')=='en_stock' ? 'selected' : '' }}>En stock</option>
                    <option value="rupture" {{ request('stock')=='rupture' ? 'selected' : '' }}>En rupture</option>
                    <option value="faible" {{ request('stock')=='faible' ? 'selected' : '' }}>Stock faible</option>
                </select>
            </div>

            <div>
                <label class="block text-base font-medium text-gray-700 dark:text-gray-300 mb-2">Catégorie</label>
                <input type="text" name="categorie" value="{{ request('categorie') }}" placeholder="Catégorie..."
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md px-4 py-3 text-base">
            </div>

            <div class="flex flex-col sm:flex-row items-end gap-3">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-base w-full sm:w-auto">
                    <i class="fas fa-search mr-2"></i>Filtrer
                </button>
                <a href="{{ route('pharmacie.index') }}"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-6 rounded-lg text-base text-center w-full sm:w-auto">
                    <i class="fas fa-times mr-2"></i>Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Résumé des totaux -->
    @if($resume)
    <div
        class="alert alert-info my-4 sm:my-6 rounded-xl shadow-lg border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/10 p-4 sm:p-6">
        <h2 class="text-blue-700 dark:text-blue-300 font-semibold mb-4 text-lg flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
            </svg>
            Résumé des données filtrées
        </h2>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 my-4">
            <div class="card text-sm flex flex-col items-center p-3 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <span class="font-bold text-gray-700 dark:text-gray-300 mb-1 text-center">Total Médicaments</span>
                <span class="text-blue-700 dark:text-blue-400 text-xl font-bold">{{
                    number_format($resume['total_medicaments'], 0, ',', ' ') }}</span>
            </div>
            <div class="card text-sm flex flex-col items-center p-3 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <span class="font-bold text-gray-700 dark:text-gray-300 mb-1 text-center">Stock Total</span>
                <span class="text-green-700 dark:text-green-400 text-xl font-bold">{{
                    number_format($resume['total_stock'], 0, ',', ' ') }} unités</span>
            </div>
            <div class="card text-sm flex flex-col items-center p-3 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <span class="font-bold text-gray-700 dark:text-gray-300 mb-1 text-center">Valeur Stock</span>
                <span class="text-purple-700 dark:text-purple-400 text-xl font-bold">{{
                    number_format($resume['valeur_stock_vente'], 0, ',', ' ') }} MRU</span>
            </div>
            <div class="card text-sm flex flex-col items-center p-3 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <span class="font-bold text-gray-700 dark:text-gray-300 mb-1 text-center">Marge Moyenne</span>
                <span class="text-indigo-700 dark:text-indigo-400 text-xl font-bold">{{
                    number_format($resume['marge_moyenne_absolue'], 0, ',', ' ') }} MRU</span>
            </div>
            <div class="card text-sm flex flex-col items-center p-3 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <span class="font-bold text-gray-700 dark:text-gray-300 mb-1 text-center">Marge %</span>
                <span class="text-orange-700 dark:text-orange-400 text-xl font-bold">{{
                    number_format($resume['marge_moyenne_pourcentage'], 1, ',', ' ') }}%</span>
            </div>
            <div class="card text-sm flex flex-col items-center p-3 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <span class="font-bold text-gray-700 dark:text-gray-300 mb-1 text-center">Bénéfice Potentiel</span>
                <span class="text-green-700 dark:text-green-400 text-xl font-bold">{{
                    number_format($resume['benefice_potentiel_total'], 0, ',', ' ') }} MRU</span>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4 mt-4">
            <div class="card text-sm flex flex-col items-center p-3 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <span class="font-bold text-gray-700 dark:text-gray-300 mb-1 text-center">En Rupture</span>
                <span class="text-red-700 dark:text-red-400 text-xl font-bold">{{
                    number_format($resume['medicaments_rupture'], 0, ',', ' ') }}</span>
            </div>
            <div class="card text-sm flex flex-col items-center p-3 bg-white dark:bg-gray-800 rounded-lg shadow-md">
                <span class="font-bold text-gray-700 dark:text-gray-300 mb-1 text-center">Stock Faible</span>
                <span class="text-yellow-700 dark:text-yellow-400 text-xl font-bold">{{
                    number_format($resume['medicaments_faible_stock'], 0, ',', ' ') }}</span>
            </div>
        </div>
    </div>
    @endif

    <!-- Liste des médicaments -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Liste des Médicaments</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th
                            class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Médicament
                        </th>
                        <th
                            class="hidden lg:table-cell px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Prix
                        </th>
                        <th
                            class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Stock
                        </th>
                        <th
                            class="hidden md:table-cell px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Catégorie
                        </th>
                        <th
                            class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Statut
                        </th>
                        <th
                            class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($pharmacies as $pharmacie)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 sm:h-10 sm:w-10">
                                    <div
                                        class="h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                        <i class="fas fa-pills text-blue-600 dark:text-blue-400 text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-3 sm:ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                        {{ $pharmacie->nom_medicament }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $pharmacie->fournisseur ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="hidden lg:table-cell px-3 sm:px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-200">
                                <div>Achat: {{ number_format($pharmacie->prix_achat, 0, ',', ' ') }} MRU</div>
                                <div>Vente: {{ number_format($pharmacie->prix_vente, 0, ',', ' ') }} MRU</div>
                                <div class="text-xs text-green-600">+{{ number_format($pharmacie->marge_beneficiaire, 0,
                                    ',', ' ') }} MRU</div>
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-200">
                                {{ $pharmacie->stock }}
                            </div>
                            @if($pharmacie->en_rupture)
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                Rupture
                            </span>
                            @elseif($pharmacie->stock <= 10) <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                Faible
                                </span>
                                @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                    OK
                                </span>
                                @endif
                        </td>
                        <td
                            class="hidden md:table-cell px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                            {{ $pharmacie->categorie ?? 'N/A' }}
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                            @switch($pharmacie->statut)
                            @case('actif')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                Actif
                            </span>
                            @break
                            @case('inactif')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
                                Inactif
                            </span>
                            @break
                            @case('rupture')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                Rupture
                            </span>
                            @break
                            @endswitch
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('pharmacie.show', $pharmacie->id) }}"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('pharmacie.edit', $pharmacie->id) }}"
                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 p-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('pharmacie.destroy', $pharmacie->id) }}" method="POST"
                                    class="inline"
                                    onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit pharmaceutique ?')">
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
                        <td colspan="6" class="px-4 sm:px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                            Aucun médicament trouvé.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 sm:px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $pharmacies->links() }}
        </div>
    </div>
</div>
@endsection
