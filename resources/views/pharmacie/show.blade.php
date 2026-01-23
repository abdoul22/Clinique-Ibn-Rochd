@extends('layouts.app')
@section('title', 'Détails du Médicament')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200">Détails du Médicament</h1>
            <div class="flex space-x-2">
                <a href="{{ route('pharmacie.edit', $pharmacie->id) }}"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-2"></i>Modifier
                </a>
                <a href="{{ route('pharmacie.index') }}"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-2"></i>Retour
                </a>
            </div>
        </div>

        <!-- Informations principales -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Informations Générales</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-200 mb-4">{{
                            $pharmacie->nom_medicament }}</h3>

                        <div class="space-y-3">
                            <div>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Catégorie:</span>
                                <span class="ml-2 text-gray-900 dark:text-gray-200">{{ $pharmacie->categorie ?? 'Non
                                    spécifiée' }}</span>
                            </div>

                            <div>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Fournisseur:</span>
                                <span class="ml-2 text-gray-900 dark:text-gray-200">{{ $pharmacie->fournisseur ?? 'Non
                                    spécifié' }}</span>
                            </div>

                            <div>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Statut:</span>
                                <span class="ml-2">
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
                                </span>
                            </div>

                            @if($pharmacie->date_expiration)
                            <div>
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Date
                                    d'Expiration:</span>
                                <span class="ml-2 text-gray-900 dark:text-gray-200">
                                    {{ $pharmacie->date_expiration->format('d/m/Y') }}
                                    @php
                                        // Vérifier manuellement si expire bientôt (dans moins de 180 jours)
                                        $dateExpiration = \Carbon\Carbon::parse($pharmacie->date_expiration);
                                        $now = \Carbon\Carbon::now();
                                        // Calculer le nombre de jours entre maintenant et la date d'expiration
                                        // Si la date est dans le futur, diffInDays() retourne un nombre positif
                                        $joursRestants = $now->diffInDays($dateExpiration, false);
                                        // Expire bientôt si la date est dans le futur ET dans moins de 180 jours
                                        $expireBientot = $dateExpiration->isFuture() && $joursRestants > 0 && $joursRestants <= 180;
                                    @endphp
                                    @if($expireBientot)
                                    <span class="text-red-600 dark:text-red-400 ml-2">(Expire bientôt!)</span>
                                    @endif
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <h4 class="text-md font-medium text-gray-900 dark:text-gray-200 mb-4">Prix et Stock</h4>

                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Prix d'Achat:</span>
                                <span class="text-gray-900 dark:text-gray-200">{{ number_format($pharmacie->prix_achat,
                                    0, ',', ' ') }} MRU</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Prix de Vente:</span>
                                <span class="text-gray-900 dark:text-gray-200">{{ number_format($pharmacie->prix_vente,
                                    0, ',', ' ') }} MRU</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Prix Unitaire:</span>
                                <span class="text-gray-900 dark:text-gray-200">{{
                                    number_format($pharmacie->prix_unitaire, 0, ',', ' ') }} MRU</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Quantité par
                                    Unité:</span>
                                <span class="text-gray-900 dark:text-gray-200">{{ $pharmacie->quantite }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Stock Actuel:</span>
                                <span class="text-gray-900 dark:text-gray-200 font-medium">{{ $pharmacie->stock
                                    }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Marge
                                    Bénéficiaire:</span>
                                <span class="text-green-600 dark:text-green-400 font-medium">+{{
                                    number_format($pharmacie->marge_beneficiaire, 0, ',', ' ') }} MRU</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($pharmacie->description)
                <div class="mt-6">
                    <h4 class="text-md font-medium text-gray-900 dark:text-gray-200 mb-2">Description</h4>
                    <p class="text-gray-700 dark:text-gray-300">{{ $pharmacie->description }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Statistiques du médicament -->
        <div
            class="alert alert-info my-6 rounded-xl shadow-lg border border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/10">
            <h2 class="text-blue-700 dark:text-blue-300 font-semibold mb-4 text-lg flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
                </svg>
                Statistiques du Médicament
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 my-4">
                <div class="card text-sm flex flex-col items-center">
                    <span class="font-bold text-gray-700 dark:text-gray-300 mb-1">Services Liés</span>
                    <span class="text-blue-700 dark:text-blue-400 text-xl font-bold">{{
                        number_format($stats['services_lies'], 0, ',', ' ') }}</span>
                </div>
                <div class="card text-sm flex flex-col items-center">
                    <span class="font-bold text-gray-700 dark:text-gray-300 mb-1">Valeur Stock</span>
                    <span class="text-green-700 dark:text-green-400 text-xl font-bold">{{
                        number_format($stats['valeur_stock_vente'], 0, ',', ' ') }} MRU</span>
                </div>
                <div class="card text-sm flex flex-col items-center">
                    <span class="font-bold text-gray-700 dark:text-gray-300 mb-1">Marge Bénéficiaire</span>
                    <span class="text-purple-700 dark:text-purple-400 text-xl font-bold">{{
                        number_format($stats['marge_beneficiaire'], 0, ',', ' ') }} MRU</span>
                </div>
                <div class="card text-sm flex flex-col items-center">
                    <span class="font-bold text-gray-700 dark:text-gray-300 mb-1">% Marge</span>
                    <span class="text-indigo-700 dark:text-indigo-400 text-xl font-bold">{{
                        number_format($stats['pourcentage_marge'], 1, ',', ' ') }}%</span>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div class="card text-sm flex flex-col items-center">
                    <span class="font-bold text-gray-700 dark:text-gray-300 mb-1">Ventes Potentielles</span>
                    <span class="text-emerald-700 dark:text-emerald-400 text-xl font-bold">{{
                        number_format($stats['ventes_potentielles'], 0, ',', ' ') }} MRU</span>
                </div>
                <div class="card text-sm flex flex-col items-center">
                    <span class="font-bold text-gray-700 dark:text-gray-300 mb-1">Bénéfice Potentiel</span>
                    <span class="text-amber-700 dark:text-amber-400 text-xl font-bold">{{
                        number_format($stats['benefice_potentiel'], 0, ',', ' ') }} MRU</span>
                </div>
            </div>
        </div>

        <!-- Services associés -->
        @if($pharmacie->services->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Services Associés</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Service
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Type
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Prix
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Quantité Défaut
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($pharmacie->services as $service)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">
                                {{ $service->nom }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                    {{ ucfirst($service->type_service) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                {{ number_format($service->prix_service, 0, ',', ' ') }} MRU
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                                {{ $service->quantite_defaut }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
