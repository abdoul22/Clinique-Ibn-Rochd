@extends('layouts.app')

@section('title', 'Modifier le crédit #' . $credit->id)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- En-tête -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <i class="fas fa-edit text-indigo-600 dark:text-indigo-400"></i>
                Modifier le Crédit #{{ $credit->id }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                Modification des détails du crédit
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('credits.show', $credit->id) }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-eye"></i>
                Voir le détail
            </a>
            <a href="{{ route('credits.index') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Formulaire de modification -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <i class="fas fa-form text-indigo-600 dark:text-indigo-400"></i>
                Informations du Crédit
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">
                Modifiez les informations du crédit ci-dessous
            </p>
        </div>

        <form action="{{ route('credits.update', $credit->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Informations de base -->
                <div class="space-y-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informations de Base</h3>

                    <!-- Bénéficiaire (lecture seule) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Bénéficiaire
                        </label>
                        <div
                            class="bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($credit->source_type === 'App\\Models\\Personnel')
                                <span
                                    class="bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 px-2 py-1 rounded text-xs font-medium">
                                    <i class="fas fa-user mr-1"></i>Personnel
                                </span>
                                @else
                                <span
                                    class="bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400 px-2 py-1 rounded text-xs font-medium">
                                    <i class="fas fa-shield-alt mr-1"></i>Assurance
                                </span>
                                @endif
                                <span class="text-gray-900 dark:text-white font-medium">{{ $credit->source->nom ?? 'N/A'
                                    }}</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Le bénéficiaire ne peut pas être modifié
                        </p>
                    </div>

                    <!-- Montant total -->
                    <div>
                        <label for="montant" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Montant Total <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" id="montant" name="montant"
                                value="{{ old('montant', $credit->montant) }}" min="1" step="1" required
                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <span class="text-gray-500 dark:text-gray-400 text-sm">MRU</span>
                            </div>
                        </div>
                        @error('montant')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Statut -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Statut <span class="text-red-500">*</span>
                        </label>



                        <select id="status" name="status" required
                            class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                            <option value="non payé" {{ old('status', $credit->status) === 'non payé' ? 'selected' : ''
                                }}>
                                Non payé
                            </option>
                            <option value="partiellement payé" {{ old('status', $credit->status) === 'partiellement
                                payé' ? 'selected' : '' }}>
                                Partiellement payé
                            </option>
                            <option value="payé" {{ old('status', $credit->status) === 'payé' ? 'selected' : '' }}>
                                Payé
                            </option>
                        </select>
                        @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror


                    </div>
                </div>

                <!-- Informations de paiement -->
                <div class="space-y-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informations de Paiement</h3>

                    <!-- Montant payé -->
                    <div>
                        <label for="montant_paye"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Montant Payé <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" id="montant_paye" name="montant_paye"
                                value="{{ old('montant_paye', $credit->montant_paye) }}" min="0" step="1" required
                                class="block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <span class="text-gray-500 dark:text-gray-400 text-sm">MRU</span>
                            </div>
                        </div>
                        @error('montant_paye')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Le montant payé ne peut pas dépasser le montant total
                        </p>
                    </div>

                    <!-- Calcul automatique du reste -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Calcul Automatique</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Montant Total:</span>
                                <span id="display-montant" class="font-medium text-gray-900 dark:text-white">{{
                                    number_format($credit->montant, 0, ',', ' ') }} MRU</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Montant Payé:</span>
                                <span id="display-montant-paye"
                                    class="font-medium text-green-600 dark:text-green-400">{{
                                    number_format($credit->montant_paye, 0, ',', ' ') }} MRU</span>
                            </div>
                            <div class="flex justify-between border-t border-gray-200 dark:border-gray-600 pt-2">
                                <span class="text-gray-600 dark:text-gray-400">Reste à Payer:</span>
                                <span id="display-reste" class="font-medium text-red-600 dark:text-red-400">{{
                                    number_format($credit->montant - $credit->montant_paye, 0, ',', ' ') }} MRU</span>
                            </div>
                        </div>
                    </div>

                    <!-- Informations supplémentaires -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-blue-700 dark:text-blue-300 mb-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Informations
                        </h4>
                        <div class="space-y-1 text-sm text-blue-600 dark:text-blue-400">
                            <p><strong>Date de création:</strong> {{ $credit->created_at ?
                                $credit->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
                            @if($credit->date_echeance)
                            <p><strong>Date d'échéance:</strong> {{
                                \Carbon\Carbon::parse($credit->date_echeance)->format('d/m/Y') }}</p>
                            @endif
                            <p><strong>ID du crédit:</strong> #{{ $credit->id }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex flex-col sm:flex-row gap-4 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit"
                    class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-6 rounded-lg transition-colors flex items-center justify-center gap-2 font-medium">
                    <i class="fas fa-save"></i>
                    Enregistrer les modifications
                </button>

                <a href="{{ route('credits.show', $credit->id) }}"
                    class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-3 px-6 rounded-lg transition-colors flex items-center justify-center gap-2 font-medium">
                    <i class="fas fa-times"></i>
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const montantInput = document.getElementById('montant');
    const montantPayeInput = document.getElementById('montant_paye');
    const displayMontant = document.getElementById('display-montant');
    const displayMontantPaye = document.getElementById('display-montant-paye');
    const displayReste = document.getElementById('display-reste');

    function formatNumber(num) {
        return new Intl.NumberFormat('fr-FR').format(num);
    }

    function updateDisplays() {
        const montant = parseFloat(montantInput.value) || 0;
        const montantPaye = parseFloat(montantPayeInput.value) || 0;
        const reste = Math.max(montant - montantPaye, 0);

        displayMontant.textContent = formatNumber(montant) + ' MRU';
        displayMontantPaye.textContent = formatNumber(montantPaye) + ' MRU';
        displayReste.textContent = formatNumber(reste) + ' MRU';

        // Validation en temps réel
        if (montantPaye > montant) {
            montantPayeInput.setCustomValidity('Le montant payé ne peut pas dépasser le montant total');
            montantPayeInput.classList.add('border-red-500');
        } else {
            montantPayeInput.setCustomValidity('');
            montantPayeInput.classList.remove('border-red-500');
        }
    }

    montantInput.addEventListener('input', updateDisplays);
    montantPayeInput.addEventListener('input', updateDisplays);

    // Mise à jour initiale
    updateDisplays();
});
</script>
@endsection