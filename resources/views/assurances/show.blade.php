@extends('layouts.app')

@section('title', 'Détails de l\'assurance ' . $assurance->nom)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- En-tête avec boutons -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <i class="fas fa-shield-alt text-blue-600 dark:text-blue-400"></i>
                {{ $assurance->nom }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                Détails des crédits et statistiques de l'assurance
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route(auth()->user()->role->name . '.assurances.edit', $assurance->id) }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-edit"></i>
                Modifier
            </a>
            <a href="{{ route(auth()->user()->role->name . '.assurances.index') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Crédits -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Crédits</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        {{ number_format($totalCredits, 0, ',', ' ') }} MRU
                    </p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-full">
                    <i class="fas fa-credit-card text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Payé -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Payé</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                        {{ number_format($totalPaye, 0, ',', ' ') }} MRU
                    </p>
                </div>
                <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-full">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Crédit Restant -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Crédit Restant</p>
                    <p
                        class="text-2xl font-bold {{ $creditRestant > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}">
                        {{ number_format($creditRestant, 0, ',', ' ') }} MRU
                    </p>
                </div>
                <div
                    class="bg-{{ $creditRestant > 0 ? 'red' : 'gray' }}-100 dark:bg-{{ $creditRestant > 0 ? 'red' : 'gray' }}-900/30 p-3 rounded-full">
                    <i
                        class="fas fa-exclamation-triangle text-{{ $creditRestant > 0 ? 'red' : 'gray' }}-600 dark:text-{{ $creditRestant > 0 ? 'red' : 'gray' }}-400 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Nombre de Crédits -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nombre de Crédits</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                        {{ $nombreCredits }}
                    </p>
                </div>
                <div class="bg-purple-100 dark:bg-purple-900/30 p-3 rounded-full">
                    <i class="fas fa-list text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des Crédits -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-list-ul text-indigo-600 dark:text-indigo-400"></i>
                Liste des Crédits
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">
                Historique complet des crédits de cette assurance
            </p>
        </div>

        @if($assurance->credits->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            ID
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Date de Création
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Montant
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Montant Payé
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Reste à Payer
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Statut
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Date d'Échéance
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($assurance->credits as $credit)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            #{{ $credit->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            {{ $credit->created_at ? $credit->created_at->format('d/m/Y H:i') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            <span class="font-semibold text-blue-600 dark:text-blue-400">
                                {{ number_format($credit->montant, 0, ',', ' ') }} MRU
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            <span class="font-semibold text-green-600 dark:text-green-400">
                                {{ number_format($credit->montant_paye, 0, ',', ' ') }} MRU
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            @php
                            $resteAPayer = $credit->montant - $credit->montant_paye;
                            @endphp
                            <span
                                class="font-semibold {{ $resteAPayer > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}">
                                {{ number_format($resteAPayer, 0, ',', ' ') }} MRU
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                            $status = $credit->status ?? 'non défini';
                            $statusColor = match($status) {
                            'payé' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                            'partiellement payé' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30
                            dark:text-yellow-400',
                            'non payé' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400'
                            };
                            @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            {{ $credit->date_echeance ? \Carbon\Carbon::parse($credit->date_echeance)->format('d/m/Y') :
                            'Non définie' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('credits.show', $credit->id) }}"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1"
                                    title="Voir le détail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('credits.edit', $credit->id) }}"
                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 p-1"
                                    title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-12 text-center">
            <div class="max-w-md mx-auto">
                <div
                    class="bg-gray-100 dark:bg-gray-700 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-credit-card text-gray-400 dark:text-gray-500 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    Aucun crédit trouvé
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Cette assurance n'a pas encore de crédits associés.
                </p>
                <a href="{{ route('credits.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Ajouter un crédit
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('title', 'Détails de l\'assurance ' . $assurance->nom)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- En-tête avec boutons -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <i class="fas fa-shield-alt text-blue-600 dark:text-blue-400"></i>
                {{ $assurance->nom }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                Détails des crédits et statistiques de l'assurance
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route(auth()->user()->role->name . '.assurances.edit', $assurance->id) }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-edit"></i>
                Modifier
            </a>
            <a href="{{ route(auth()->user()->role->name . '.assurances.index') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Crédits -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Crédits</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                        {{ number_format($totalCredits, 0, ',', ' ') }} MRU
                    </p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-full">
                    <i class="fas fa-credit-card text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Payé -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Payé</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                        {{ number_format($totalPaye, 0, ',', ' ') }} MRU
                    </p>
                </div>
                <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-full">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Crédit Restant -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Crédit Restant</p>
                    <p
                        class="text-2xl font-bold {{ $creditRestant > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}">
                        {{ number_format($creditRestant, 0, ',', ' ') }} MRU
                    </p>
                </div>
                <div
                    class="bg-{{ $creditRestant > 0 ? 'red' : 'gray' }}-100 dark:bg-{{ $creditRestant > 0 ? 'red' : 'gray' }}-900/30 p-3 rounded-full">
                    <i
                        class="fas fa-exclamation-triangle text-{{ $creditRestant > 0 ? 'red' : 'gray' }}-600 dark:text-{{ $creditRestant > 0 ? 'red' : 'gray' }}-400 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Nombre de Crédits -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Nombre de Crédits</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                        {{ $nombreCredits }}
                    </p>
                </div>
                <div class="bg-purple-100 dark:bg-purple-900/30 p-3 rounded-full">
                    <i class="fas fa-list text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des Crédits -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-list-ul text-indigo-600 dark:text-indigo-400"></i>
                Liste des Crédits
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">
                Historique complet des crédits de cette assurance
            </p>
        </div>

        @if($assurance->credits->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            ID
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Date de Création
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Montant
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Montant Payé
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Reste à Payer
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Statut
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Date d'Échéance
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($assurance->credits as $credit)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            #{{ $credit->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            {{ $credit->created_at ? $credit->created_at->format('d/m/Y H:i') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            <span class="font-semibold text-blue-600 dark:text-blue-400">
                                {{ number_format($credit->montant, 0, ',', ' ') }} MRU
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            <span class="font-semibold text-green-600 dark:text-green-400">
                                {{ number_format($credit->montant_paye, 0, ',', ' ') }} MRU
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            @php
                            $resteAPayer = $credit->montant - $credit->montant_paye;
                            @endphp
                            <span
                                class="font-semibold {{ $resteAPayer > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400' }}">
                                {{ number_format($resteAPayer, 0, ',', ' ') }} MRU
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                            $status = $credit->status ?? 'non défini';
                            $statusColor = match($status) {
                            'payé' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                            'partiellement payé' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30
                            dark:text-yellow-400',
                            'non payé' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400'
                            };
                            @endphp
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                                {{ ucfirst($status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            {{ $credit->date_echeance ? \Carbon\Carbon::parse($credit->date_echeance)->format('d/m/Y') :
                            'Non définie' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('credits.show', $credit->id) }}"
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1"
                                    title="Voir le détail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('credits.edit', $credit->id) }}"
                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 p-1"
                                    title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-12 text-center">
            <div class="max-w-md mx-auto">
                <div
                    class="bg-gray-100 dark:bg-gray-700 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-credit-card text-gray-400 dark:text-gray-500 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    Aucun crédit trouvé
                </h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Cette assurance n'a pas encore de crédits associés.
                </p>
                <a href="{{ route('credits.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Ajouter un crédit
                </a>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
