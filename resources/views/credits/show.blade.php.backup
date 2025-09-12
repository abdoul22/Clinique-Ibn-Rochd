@extends('layouts.app')

@section('title', 'Détails du crédit #' . $credit->id)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- En-tête avec boutons -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <i class="fas fa-credit-card text-blue-600 dark:text-blue-400"></i>
                Crédit #{{ $credit->id }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                Détails complets du crédit et historique des paiements
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            @if($credit->status !== 'payé')
            <a href="{{ route('credits.payer', $credit->id) }}"
                class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-money-bill-wave"></i>
                Effectuer un paiement
            </a>
            @endif
            <a href="{{ route('credits.edit', $credit->id) }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-edit"></i>
                Modifier
            </a>
            <a href="{{ route('credits.index') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Informations principales -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Informations du crédit -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-600 dark:text-blue-400"></i>
                Informations du Crédit
            </h2>

            <div class="space-y-4">
                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                    <span class="font-medium text-gray-600 dark:text-gray-400">ID du crédit:</span>
                    <span class="text-gray-900 dark:text-white font-semibold">#{{ $credit->id }}</span>
                </div>

                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                    <span class="font-medium text-gray-600 dark:text-gray-400">Type:</span>
                    <span class="text-gray-900 dark:text-white">
                        @if($credit->source_type === 'App\\Models\\Personnel')
                        <span
                            class="bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 px-3 py-1 rounded-full text-sm font-medium">
                            <i class="fas fa-user mr-1"></i>Personnel
                        </span>
                        @else
                        <span
                            class="bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400 px-3 py-1 rounded-full text-sm font-medium">
                            <i class="fas fa-shield-alt mr-1"></i>Assurance
                        </span>
                        @endif
                    </span>
                </div>

                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                    <span class="font-medium text-gray-600 dark:text-gray-400">Bénéficiaire:</span>
                    <span class="text-gray-900 dark:text-white font-semibold">{{ $credit->source->nom ?? 'N/A' }}</span>
                </div>

                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                    <span class="font-medium text-gray-600 dark:text-gray-400">Date de création:</span>
                    <span class="text-gray-900 dark:text-white">{{ $credit->created_at ?
                        $credit->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                </div>

                @if($credit->date_echeance)
                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                    <span class="font-medium text-gray-600 dark:text-gray-400">Date d'échéance:</span>
                    <span class="text-gray-900 dark:text-white">{{
                        \Carbon\Carbon::parse($credit->date_echeance)->format('d/m/Y') }}</span>
                </div>
                @endif

                <div class="flex justify-between items-center py-3">
                    <span class="font-medium text-gray-600 dark:text-gray-400">Statut:</span>
                    <span class="text-gray-900 dark:text-white">
                        @php
                        $statusColor = match($credit->status) {
                        'payé' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                        'partiellement payé' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30
                        dark:text-yellow-400',
                        'non payé' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400'
                        };
                        @endphp
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $statusColor }}">
                            {{ ucfirst($credit->status ?? 'Non défini') }}
                        </span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Résumé financier -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <i class="fas fa-chart-pie text-green-600 dark:text-green-400"></i>
                Résumé Financier
            </h2>

            <div class="space-y-6">
                <!-- Montant total -->
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-700 dark:text-blue-300">Montant Total</p>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                {{ number_format($credit->montant, 0, ',', ' ') }} MRU
                            </p>
                        </div>
                        <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-full">
                            <i class="fas fa-credit-card text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Montant payé -->
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-700 dark:text-green-300">Montant Payé</p>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                {{ number_format($credit->montant_paye, 0, ',', ' ') }} MRU
                            </p>
                        </div>
                        <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-full">
                            <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Montant restant -->
                <div
                    class="bg-{{ $montantRestant > 0 ? 'red' : 'gray' }}-50 dark:bg-{{ $montantRestant > 0 ? 'red' : 'gray' }}-900/20 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm font-medium text-{{ $montantRestant > 0 ? 'red' : 'gray' }}-700 dark:text-{{ $montantRestant > 0 ? 'red' : 'gray' }}-300">
                                Montant Restant</p>
                            <p
                                class="text-2xl font-bold text-{{ $montantRestant > 0 ? 'red' : 'gray' }}-600 dark:text-{{ $montantRestant > 0 ? 'red' : 'gray' }}-400">
                                {{ number_format($montantRestant, 0, ',', ' ') }} MRU
                            </p>
                        </div>
                        <div
                            class="bg-{{ $montantRestant > 0 ? 'red' : 'gray' }}-100 dark:bg-{{ $montantRestant > 0 ? 'red' : 'gray' }}-900/30 p-3 rounded-full">
                            <i
                                class="fas fa-{{ $montantRestant > 0 ? 'exclamation-triangle' : 'check' }} text-{{ $montantRestant > 0 ? 'red' : 'gray' }}-600 dark:text-{{ $montantRestant > 0 ? 'red' : 'gray' }}-400 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Barre de progression -->
                <div class="mt-6">
                    <div class="flex justify-between text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                        <span>Progression du paiement</span>
                        <span>{{ number_format($pourcentagePaye, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-400 to-emerald-600 h-3 rounded-full transition-all duration-700 ease-out"
                            style="width: {{ $pourcentagePaye }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions recommandées -->
    @if($credit->status !== 'payé')
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            <i class="fas fa-hand-holding-usd text-green-600 dark:text-green-400"></i>
            Actions de Paiement
        </h2>



        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('credits.payer', $credit->id) }}"
                class="w-full bg-green-600 hover:bg-green-700 text-white py-4 px-6 rounded-lg transition-colors flex items-center justify-center gap-3 font-medium">
                <i class="fas fa-money-bill-wave text-xl"></i>
                <div class="text-left">
                    <div>Effectuer un Paiement</div>
                    <div class="text-xs opacity-90">Enregistrement en caisse</div>
                </div>
            </a>

            <a href="{{ route('credits.edit', $credit->id) }}"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 px-6 rounded-lg transition-colors flex items-center justify-center gap-3 font-medium">
                <i class="fas fa-edit text-xl"></i>
                <div class="text-left">
                    <div>Modifier le Crédit</div>
                    <div class="text-xs opacity-90">Ajuster les montants</div>
                </div>
            </a>
        </div>


    </div>
    @endif

    <!-- Historique des paiements -->
    @if($historiquePaiements->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-history text-indigo-600 dark:text-indigo-400"></i>
                Historique des Paiements Récents
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">
                Les 10 derniers paiements de crédits dans le système
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Date
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Type
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Montant
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Source
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($historiquePaiements as $paiement)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            {{ $paiement->created_at ? $paiement->created_at->format('d/m/Y H:i') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            <span
                                class="bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 px-2 py-1 rounded text-xs font-medium">
                                {{ ucfirst($paiement->type ?? 'N/A') }}
                            </span>
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600 dark:text-green-400">
                            {{ number_format($paiement->montant, 0, ',', ' ') }} MRU
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            @if($paiement->source === 'credit_personnel')
                            <span class="text-blue-600 dark:text-blue-400">Crédit Personnel</span>
                            @elseif($paiement->source === 'credit_assurance')
                            <span class="text-purple-600 dark:text-purple-400">Crédit Assurance</span>
                            @else
                            <span class="text-gray-600 dark:text-gray-400">{{ $paiement->source ?? 'N/A' }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection

@section('title', 'Détails du crédit #' . $credit->id)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- En-tête avec boutons -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <i class="fas fa-credit-card text-blue-600 dark:text-blue-400"></i>
                Crédit #{{ $credit->id }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                Détails complets du crédit et historique des paiements
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            @if($credit->status !== 'payé')
            <a href="{{ route('credits.payer', $credit->id) }}"
                class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-money-bill-wave"></i>
                Effectuer un paiement
            </a>
            @endif
            <a href="{{ route('credits.edit', $credit->id) }}"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-edit"></i>
                Modifier
            </a>
            <a href="{{ route('credits.index') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Informations principales -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Informations du crédit -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <i class="fas fa-info-circle text-blue-600 dark:text-blue-400"></i>
                Informations du Crédit
            </h2>

            <div class="space-y-4">
                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                    <span class="font-medium text-gray-600 dark:text-gray-400">ID du crédit:</span>
                    <span class="text-gray-900 dark:text-white font-semibold">#{{ $credit->id }}</span>
                </div>

                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                    <span class="font-medium text-gray-600 dark:text-gray-400">Type:</span>
                    <span class="text-gray-900 dark:text-white">
                        @if($credit->source_type === 'App\\Models\\Personnel')
                        <span
                            class="bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 px-3 py-1 rounded-full text-sm font-medium">
                            <i class="fas fa-user mr-1"></i>Personnel
                        </span>
                        @else
                        <span
                            class="bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400 px-3 py-1 rounded-full text-sm font-medium">
                            <i class="fas fa-shield-alt mr-1"></i>Assurance
                        </span>
                        @endif
                    </span>
                </div>

                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                    <span class="font-medium text-gray-600 dark:text-gray-400">Bénéficiaire:</span>
                    <span class="text-gray-900 dark:text-white font-semibold">{{ $credit->source->nom ?? 'N/A' }}</span>
                </div>

                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                    <span class="font-medium text-gray-600 dark:text-gray-400">Date de création:</span>
                    <span class="text-gray-900 dark:text-white">{{ $credit->created_at ?
                        $credit->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                </div>

                @if($credit->date_echeance)
                <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-gray-700">
                    <span class="font-medium text-gray-600 dark:text-gray-400">Date d'échéance:</span>
                    <span class="text-gray-900 dark:text-white">{{
                        \Carbon\Carbon::parse($credit->date_echeance)->format('d/m/Y') }}</span>
                </div>
                @endif

                <div class="flex justify-between items-center py-3">
                    <span class="font-medium text-gray-600 dark:text-gray-400">Statut:</span>
                    <span class="text-gray-900 dark:text-white">
                        @php
                        $statusColor = match($credit->status) {
                        'payé' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                        'partiellement payé' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30
                        dark:text-yellow-400',
                        'non payé' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                        default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400'
                        };
                        @endphp
                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $statusColor }}">
                            {{ ucfirst($credit->status ?? 'Non défini') }}
                        </span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Résumé financier -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <i class="fas fa-chart-pie text-green-600 dark:text-green-400"></i>
                Résumé Financier
            </h2>

            <div class="space-y-6">
                <!-- Montant total -->
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-blue-700 dark:text-blue-300">Montant Total</p>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                {{ number_format($credit->montant, 0, ',', ' ') }} MRU
                            </p>
                        </div>
                        <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-full">
                            <i class="fas fa-credit-card text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Montant payé -->
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-green-700 dark:text-green-300">Montant Payé</p>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                                {{ number_format($credit->montant_paye, 0, ',', ' ') }} MRU
                            </p>
                        </div>
                        <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-full">
                            <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Montant restant -->
                <div
                    class="bg-{{ $montantRestant > 0 ? 'red' : 'gray' }}-50 dark:bg-{{ $montantRestant > 0 ? 'red' : 'gray' }}-900/20 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p
                                class="text-sm font-medium text-{{ $montantRestant > 0 ? 'red' : 'gray' }}-700 dark:text-{{ $montantRestant > 0 ? 'red' : 'gray' }}-300">
                                Montant Restant</p>
                            <p
                                class="text-2xl font-bold text-{{ $montantRestant > 0 ? 'red' : 'gray' }}-600 dark:text-{{ $montantRestant > 0 ? 'red' : 'gray' }}-400">
                                {{ number_format($montantRestant, 0, ',', ' ') }} MRU
                            </p>
                        </div>
                        <div
                            class="bg-{{ $montantRestant > 0 ? 'red' : 'gray' }}-100 dark:bg-{{ $montantRestant > 0 ? 'red' : 'gray' }}-900/30 p-3 rounded-full">
                            <i
                                class="fas fa-{{ $montantRestant > 0 ? 'exclamation-triangle' : 'check' }} text-{{ $montantRestant > 0 ? 'red' : 'gray' }}-600 dark:text-{{ $montantRestant > 0 ? 'red' : 'gray' }}-400 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Barre de progression -->
                <div class="mt-6">
                    <div class="flex justify-between text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">
                        <span>Progression du paiement</span>
                        <span>{{ number_format($pourcentagePaye, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-400 to-emerald-600 h-3 rounded-full transition-all duration-700 ease-out"
                            style="width: {{ $pourcentagePaye }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions recommandées -->
    @if($credit->status !== 'payé')
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
            <i class="fas fa-hand-holding-usd text-green-600 dark:text-green-400"></i>
            Actions de Paiement
        </h2>



        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('credits.payer', $credit->id) }}"
                class="w-full bg-green-600 hover:bg-green-700 text-white py-4 px-6 rounded-lg transition-colors flex items-center justify-center gap-3 font-medium">
                <i class="fas fa-money-bill-wave text-xl"></i>
                <div class="text-left">
                    <div>Effectuer un Paiement</div>
                    <div class="text-xs opacity-90">Enregistrement en caisse</div>
                </div>
            </a>

            <a href="{{ route('credits.edit', $credit->id) }}"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-4 px-6 rounded-lg transition-colors flex items-center justify-center gap-3 font-medium">
                <i class="fas fa-edit text-xl"></i>
                <div class="text-left">
                    <div>Modifier le Crédit</div>
                    <div class="text-xs opacity-90">Ajuster les montants</div>
                </div>
            </a>
        </div>


    </div>
    @endif

    <!-- Historique des paiements -->
    @if($historiquePaiements->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-history text-indigo-600 dark:text-indigo-400"></i>
                Historique des Paiements Récents
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">
                Les 10 derniers paiements de crédits dans le système
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Date
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Type
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Montant
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Source
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($historiquePaiements as $paiement)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            {{ $paiement->created_at ? $paiement->created_at->format('d/m/Y H:i') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            <span
                                class="bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 px-2 py-1 rounded text-xs font-medium">
                                {{ ucfirst($paiement->type ?? 'N/A') }}
                            </span>
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600 dark:text-green-400">
                            {{ number_format($paiement->montant, 0, ',', ' ') }} MRU
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-300">
                            @if($paiement->source === 'credit_personnel')
                            <span class="text-blue-600 dark:text-blue-400">Crédit Personnel</span>
                            @elseif($paiement->source === 'credit_assurance')
                            <span class="text-purple-600 dark:text-purple-400">Crédit Assurance</span>
                            @else
                            <span class="text-gray-600 dark:text-gray-400">{{ $paiement->source ?? 'N/A' }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
