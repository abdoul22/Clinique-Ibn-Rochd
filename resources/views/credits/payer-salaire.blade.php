@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 to-pink-100 dark:from-gray-900 dark:to-gray-800 py-8">
    <div class="container mx-auto px-4">
        <!-- En-tête avec navigation -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-800 dark:text-gray-100 mb-2">
                    💰 Déduction Salariale
                </h1>
                <p class="text-gray-600 dark:text-gray-400 text-lg">
                    Paiement par déduction du salaire du personnel
                </p>
            </div>
            <a href="{{ route('credits.index') }}"
                class="mt-4 sm:mt-0 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 px-6 py-3 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-200 dark:border-gray-700 flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                <span>Retour aux crédits</span>
            </a>
        </div>

        <!-- Carte principale -->
        <div class="max-w-2xl mx-auto">
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <!-- En-tête de la carte -->
                <div
                    class="bg-gradient-to-r from-purple-600 to-pink-600 dark:from-purple-700 dark:to-pink-700 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-white mb-2">
                                {{ $credit->source?->nom ?? 'Personnel' }}
                            </h2>
                            <p class="text-purple-100 text-lg">
                                👤 Crédit Personnel
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-white text-sm opacity-90">Salaire Actuel</div>
                            <div class="text-3xl font-bold text-white">
                                {{ number_format($credit->source?->salaire ?? 0, 0, ',', ' ') }} MRU
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations du crédit -->
                <div class="p-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div
                                    class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user-tie text-purple-600 dark:text-purple-400"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Informations
                                    Personnel</h3>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Nom :</span>
                                    <span class="font-medium text-gray-800 dark:text-gray-200">
                                        {{ $credit->source?->nom ?? 'N/A' }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Salaire :</span>
                                    <span class="font-medium text-green-600 dark:text-green-400">
                                        {{ number_format($credit->source?->salaire ?? 0, 0, ',', ' ') }} MRU
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Type :</span>
                                    <span class="font-medium text-purple-600 dark:text-purple-400">
                                        Personnel
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div
                                    class="w-10 h-10 bg-pink-100 dark:bg-pink-900 rounded-full flex items-center justify-center">
                                    <i class="fas fa-chart-line text-pink-600 dark:text-pink-400"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Détails du Crédit
                                </h3>
                            </div>
                            @php
                            $pourcentage = $credit->montant > 0 ? ($credit->montant_paye / $credit->montant) * 100 : 0;
                            @endphp
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Crédit total :</span>
                                    <span class="font-medium text-red-600 dark:text-red-400">
                                        {{ number_format($credit->montant, 0, ',', ' ') }} MRU
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Montant payé :</span>
                                    <span class="font-medium text-blue-600 dark:text-blue-400">
                                        {{ number_format($credit->montant_paye, 0, ',', ' ') }} MRU
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Reste à payer :</span>
                                    <span class="font-medium text-orange-600 dark:text-orange-400">
                                        {{ number_format($credit->montant - $credit->montant_paye, 0, ',', ' ') }} MRU
                                    </span>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-1">
                                    <span>Progression</span>
                                    <span>{{ number_format($pourcentage, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-pink-500 to-purple-600 h-3 rounded-full transition-all duration-300"
                                        style="width: {{ $pourcentage }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulaire de déduction -->
                    <form method="POST" action="{{ route('credits.payer.salaire', ['credit' => $credit->id]) }}"
                        class="space-y-6">
                        @csrf

                        <div
                            class="bg-purple-50 dark:bg-gray-700 rounded-xl p-6 border border-purple-200 dark:border-gray-600">
                            <div class="flex items-center gap-3 mb-4">
                                <div
                                    class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center">
                                    <i class="fas fa-money-bill-wave text-purple-600 dark:text-purple-400"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Déduction Salariale
                                </h3>
                            </div>

                            <div>
                                <label for="montant"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Montant à déduire du salaire *
                                </label>
                                <div class="relative">
                                    <input type="number" id="montant" name="montant" step="0.01" min="1"
                                        max="{{ $credit->montant - $credit->montant_paye }}"
                                        class="w-full border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200"
                                        placeholder="0.00" required>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 dark:text-gray-400 text-sm">MRU</span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Maximum: {{ number_format($credit->montant - $credit->montant_paye, 0, ',', ' ') }}
                                    MRU
                                </p>
                            </div>
                        </div>

                        <!-- Avertissement -->
                        <div
                            class="bg-yellow-50 dark:bg-gray-700 border border-yellow-200 dark:border-gray-600 rounded-xl p-6">
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-exclamation-triangle text-yellow-600 dark:text-yellow-400"></i>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                                        ⚠️ Attention
                                    </h4>
                                    <p class="text-yellow-700 dark:text-yellow-300 text-sm leading-relaxed">
                                        Ce montant sera automatiquement déduit du salaire du personnel et enregistré
                                        comme une dépense.
                                        Cette action est irréversible et affectera directement le salaire net du
                                        personnel.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Bouton de soumission -->
                        <div class="flex justify-end">
                            <button type="submit"
                                class="bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white font-bold py-4 px-8 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex items-center gap-3">
                                <i class="fas fa-check-circle"></i>
                                <span>Enregistrer la Déduction</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const montantInput = document.getElementById('montant');
    const maxMontant = {{ $credit->montant - $credit->montant_paye }};

    // Formater l'input montant
    montantInput.addEventListener('input', function() {
        const value = parseFloat(this.value);
        if (value > maxMontant) {
            this.value = maxMontant;
        }
    });

    // Validation en temps réel
    montantInput.addEventListener('blur', function() {
        const value = parseFloat(this.value);
        if (value <= 0) {
            this.classList.add('border-red-500');
            this.classList.remove('border-gray-300', 'dark:border-gray-600');
        } else {
            this.classList.remove('border-red-500');
            this.classList.add('border-gray-300', 'dark:border-gray-600');
        }
    });
});
</script>
@endpush

@endsection