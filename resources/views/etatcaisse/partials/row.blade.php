<tr class="table-row">
    <td class="table-cell py-2 px-2">{{ $etat->id }}</td>

    <td class="table-cell py-2 px-2">
        @if($etat->caisse)
        <a href="{{ route('caisses.show', $etat->caisse_id) }}"
            class="text-blue-600 dark:text-blue-400 hover:underline">
            Facture n°{{ $etat->caisse->numero_facture }}
        </a>
        @else
        {{ $etat->designation }}
        @endif
    </td>

    <td class="py-2 px-2 group">
        <div
            class="flex items-center justify-between bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg p-2 transition-all duration-300 hover:shadow-md hover:scale-[1.01]">
            <div class="flex items-center space-x-1">
                <!-- Icône -->
                <div class="bg-emerald-500/10 p-1 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-emerald-600 dark:text-emerald-400"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Recette</div>
                    <div class="font-mono font-bold text-emerald-700 dark:text-emerald-400 tracking-tight text-sm">
                        {{ number_format($etat->recette, 0, ',', ' ') }}
                    </div>
                </div>
            </div>
            <span
                class="bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 text-xs font-semibold px-2 py-0.5 rounded-full">
                MRU
            </span>
        </div>
    </td>

    <td class="py-2 px-2 group">
        <div
            class="flex items-center justify-between bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-lg p-2 transition-all duration-300 hover:shadow-md hover:scale-[1.01]">
            <div class="flex items-center space-x-1">
                <!-- Icône -->
                <div class="bg-blue-500/10 p-1 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600 dark:text-blue-400"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Part Médecin</div>
                    <div class="font-mono font-bold text-blue-700 dark:text-blue-400 tracking-tight text-sm">
                        {{ number_format($etat->part_medecin, 0, ',', ' ') }}
                    </div>
                </div>
            </div>
            <span
                class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-xs font-semibold px-2 py-0.5 rounded-full">
                MRU
            </span>
        </div>
    </td>

    <td class="py-2 px-2 group">
        <div
            class="flex items-center justify-between bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-lg p-2 transition-all duration-300 hover:shadow-md hover:scale-[1.01]">
            <div class="flex items-center space-x-1">
                <!-- Icône -->
                <div class="bg-indigo-500/10 p-1 rounded-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-600 dark:text-indigo-400"
                        viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                    </svg>
                </div>
                <div>
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Part Clinique</div>
                    <div class="font-mono font-bold text-indigo-700 dark:text-indigo-400 tracking-tight text-sm">
                        {{ number_format($etat->part_clinique, 0, ',', ' ') }}
                    </div>
                </div>
            </div>
            <span
                class="bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300 text-xs font-semibold px-2 py-0.5 rounded-full">
                MRU
            </span>
        </div>
    </td>

    <td class="table-cell py-2 px-2">
        <div class="flex flex-col items-center space-y-2">
            @php $paiement = optional($etat->caisse)->paiements; @endphp
            @if($paiement)
            <p class="text-blue-800 dark:text-blue-300 font-bold text-sm">{{ $paiement->type }}</p>
            <small
                class="font-bold inline-block px-3 py-1.5 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm border border-indigo-200 dark:border-indigo-600 rounded-lg text-indigo-700 dark:text-indigo-300 shadow-md shadow-indigo-200/30 dark:shadow-indigo-600/30 text-xs">(
                {{ number_format($paiement->montant, 0, ',', ' ') }} ) MRU</small>
            @else
            <div
                class="font-bold inline-block px-3 py-1.5 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm border border-indigo-200 dark:border-indigo-600 rounded-lg text-indigo-700 dark:text-indigo-300 shadow-md shadow-indigo-200/30 dark:shadow-indigo-600/30 text-xs">
                Assuré
            </div>
            @endif
        </div>
    </td>

    <td class="table-cell py-2 px-2">
        @if(!$etat->validated)
        <button type="button" onclick="openPaymentModal({{ $etat->id }}, {{ $etat->part_medecin }})"
            class="bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-2 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Valider
        </button>
        @else
        <div class="flex flex-col items-center space-y-1">
            <span
                class="bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 text-xs font-semibold px-2 py-1 rounded-full flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Validé
            </span>
            <form method="POST" action="{{ route('etatcaisse.unvalider', $etat->id) }}" class="inline">
                @csrf
                <button type="submit"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs px-2 py-1 rounded shadow">
                    Annuler
                </button>
            </form>
        </div>
        @endif
    </td>
    {{-- Assurance --}}
    @php
    $caisse = $etat->caisse;
    $assurance = $etat->assurance;
    $couverture = intval($caisse?->couverture ?? 100);
    @endphp

    <td class="group p-2 transition-colors duration-300 hover:bg-indigo-50/30 dark:hover:bg-indigo-900/30 rounded-lg">
        @if($assurance && $couverture)
        <div class="flex flex-col items-center space-y-2">
            <!-- Nom de l'assurance -->
            <span
                class="inline-block px-3 py-1.5 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm border border-indigo-200 dark:border-indigo-600 rounded-lg text-indigo-700 dark:text-indigo-300 font-bold shadow-md shadow-indigo-200/30 dark:shadow-indigo-600/30 text-xs">
                {{ $assurance->nom }}
            </span>

            <!-- Barre de progression moderne -->
            <div class="w-full max-w-xs bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                <div class="bg-gradient-to-r from-green-400 to-emerald-600 h-2 rounded-full transition-all duration-700 ease-out"
                    style="width: {{ $couverture }}%"></div>
            </div>

            <!-- Texte de pourcentage -->
            <span
                class="text-xs font-bold bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 to-green-500">
                {{ $couverture }}% de couverture
            </span>
        </div>
        @else
        <div class="flex justify-center">
            <span
                class="inline-block px-3 py-1.5 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-400 dark:text-gray-500 italic font-bold text-xs">
                0 %
            </span>
        </div>
        @endif
    </td>

    {{-- Médecin cliquable --}}
    <td class="table-cell py-2 px-2">
        @if($etat->medecin)
        <a href="{{ route('medecins.stats', $etat->medecin->id) }}"
            class="text-blue-600 dark:text-blue-400 hover:underline text-sm">
            {{ $etat->medecin->nom }}
        </a>
        @else
        —
        @endif
    </td>
    <td class="table-cell py-2 px-2">
        <div class="flex space-x-2">
            <a href="{{ route('etatcaisse.show', $etat->id) }}"
                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 p-1">
                <i class="fas fa-eye"></i>
            </a>

            <form action="{{ route('etatcaisse.destroy', $etat->id) }}" method="POST"
                onsubmit="return confirm('Supprimer ?')" class="inline">
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
