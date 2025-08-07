@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 py-8">
    <div class="container mx-auto px-4">
        <!-- En-tête avec navigation -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <div>
                <h1 class="text-3xl sm:text-4xl font-bold text-gray-800 dark:text-gray-100 mb-2">
                    💳 Paiement de Crédit
                </h1>
                <p class="text-gray-600 dark:text-gray-400 text-lg">
                    {{ class_basename($credit->source_type) === 'Personnel' ? 'Paiement Personnel' : 'Paiement
                    Assurance' }}
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
                    class="bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-700 dark:to-indigo-700 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-white mb-2">
                                {{ $credit->source?->nom ?? 'Crédit' }}
                            </h2>
                            <p class="text-blue-100 text-lg">
                                {{ class_basename($credit->source_type) === 'Personnel' ? '👤 Personnel' : '🏥
                                Assurance' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <div class="text-white text-sm opacity-90">Montant Total</div>
                            <div class="text-3xl font-bold text-white">
                                {{ number_format($credit->montant, 0, ',', ' ') }} MRU
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
                                    class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                    <i class="fas fa-info-circle text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Informations</h3>
                            </div>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Type :</span>
                                    <span class="font-medium text-gray-800 dark:text-gray-200">
                                        {{ class_basename($credit->source_type) === 'Personnel' ? 'Personnel' :
                                        'Assurance' }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Montant payé :</span>
                                    <span class="font-medium text-green-600 dark:text-green-400">
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
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div
                                    class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                    <i class="fas fa-chart-pie text-green-600 dark:text-green-400"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Progression</h3>
                            </div>
                            @php
                            $pourcentage = $credit->montant > 0 ? ($credit->montant_paye / $credit->montant) * 100 : 0;
                            @endphp
                            <div class="mb-3">
                                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-1">
                                    <span>Progression</span>
                                    <span>{{ number_format($pourcentage, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-green-500 to-green-600 h-3 rounded-full transition-all duration-300"
                                        style="width: {{ $pourcentage }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulaire de paiement -->
                    <form method="POST" action="{{ route('credits.payer.store', ['credit' => $credit->id]) }}"
                        class="space-y-6">
                        @csrf

                        <div
                            class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-6 border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center gap-3 mb-4">
                                <div
                                    class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                    <i class="fas fa-credit-card text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Détails du Paiement
                                </h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="montant"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Montant à payer *
                                    </label>
                                    <div class="relative">
                                        <input type="number" id="montant" name="montant" step="0.01" min="1"
                                            max="{{ $credit->montant - $credit->montant_paye }}"
                                            class="w-full border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-3 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                            placeholder="0.00" required>
                                        <div
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 dark:text-gray-400 text-sm">MRU</span>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Maximum: {{ number_format($credit->montant - $credit->montant_paye, 0, ',', ' ')
                                        }} MRU
                                    </p>
                                </div>

                                <div>
                                    <label for="mode_paiement_id"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Mode de paiement *
                                    </label>
                                    <select name="mode_paiement_id" id="mode_paiement_id" required
                                        class="w-full border border-gray-300 dark:border-gray-600 rounded-xl px-4 py-3 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                        <option value="">-- Sélectionner --</option>
                                        @foreach($modes as $mode)
                                        <option value="{{ $mode }}">{{ ucfirst($mode) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Bouton de soumission -->
                        <div class="flex justify-end">
                            <button type="submit"
                                class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-4 px-8 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 flex items-center gap-3">
                                <i class="fas fa-check-circle"></i>
                                <span>Enregistrer le Paiement</span>
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