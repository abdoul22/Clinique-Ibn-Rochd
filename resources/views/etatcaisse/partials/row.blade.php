<tr class="border-t">
    <td class="py-2 px-4">{{ $etat->id }}</td>

    <td class="py-2 px-4">
        @if($etat->caisse)
        <a href="{{ route('caisses.show', $etat->caisse_id) }}" class="text-blue-600 hover:underline">
            Facture n°{{ $etat->caisse->numero_facture }}
        </a>
        @else
        {{ $etat->designation }}
        @endif
    </td>

<td class="py-3 px-4 group">
        <div
            class="flex items-center justify-between bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-3 transition-all duration-300 hover:shadow-md hover:scale-[1.02]">
            <div class="flex items-center space-x-2">
                <!-- Icône -->
                <div class="bg-emerald-500/10 p-2 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-600" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <div class="text-xs font-medium text-gray-500">Recette</div>
                    <div class="font-mono font-bold text-emerald-700 tracking-tight text-lg">
                        {{ number_format($etat->recette, 0, ',', ' ') }}
                    </div>
                </div>
            </div>
            <span class="bg-emerald-100 text-emerald-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                MRU
            </span>
        </div>
    </td>

    <td class="py-3 px-4 group">
        <div
            class="flex items-center justify-between bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl p-3 transition-all duration-300 hover:shadow-md hover:scale-[1.02]">
            <div class="flex items-center space-x-2">
                <!-- Icône -->
                <div class="bg-blue-500/10 p-2 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <div class="text-xs font-medium text-gray-500">Part Médecin</div>
                    <div class="font-mono font-bold text-blue-700 tracking-tight text-lg">
                        {{ number_format($etat->part_medecin, 0, ',', ' ') }}
                    </div>
                </div>
            </div>
            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                MRU
            </span>
        </div>
    </td>

    <td class="py-3 px-4 group">
        <div
            class="flex items-center justify-between bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-3 transition-all duration-300 hover:shadow-md hover:scale-[1.02]">
            <div class="flex items-center space-x-2">
                <!-- Icône -->
                <div class="bg-indigo-500/10 p-2 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path
                            d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                    </svg>
                </div>
                <div>
                    <div class="text-xs font-medium text-gray-500">Part Clinique</div>
                    <div class="font-mono font-bold text-indigo-700 tracking-tight text-lg">
                        {{ number_format($etat->part_clinique, 0, ',', ' ') }}
                    </div>
                </div>
            </div>
            <span class="bg-indigo-100 text-indigo-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                MRU
            </span>
        </div>
        </td>

    <td class="py-2 px-4">
        <div class="flex flex-col items-center space-y-4">
            @php $paiement = optional($etat->caisse)->paiements; @endphp
            @if($paiement)
            <p class="text-blue-800 font-bold ">{{ $paiement->type }}</p>
            <small
                class="font-bold inline-block px-5 py-2.5 bg-white/90 backdrop-blur-sm border border-indigo-200 rounded-xl text-indigo-700 shadow-md shadow-indigo-200/30 ">(
                {{ number_format($paiement->montant, 0, ',', ' ') }} ) MRU</small>
            @else
            <div class="font-bold inline-block px-5 py-2.5 bg-white/90 backdrop-blur-sm border border-indigo-200 rounded-xl text-indigo-700 shadow-md shadow-indigo-200/30" >
            Assuré
            </div>
            @endif
        </div>
    </td>

<td class="py-2 px-4">
@if(!$etat->validated)
    <form method="POST" action="{{ route('etatcaisse.valider', $etat->id) }}">
        @csrf
        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1 rounded shadow">
            Valider
        </button>
    </form>
    @else
    <form method="POST" action="{{ route('etatcaisse.unvalider', $etat->id) }}">
        @csrf
        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs px-3 py-1 rounded shadow">
            Annuler
        </button>
    </form>
    @endif
    </td>
    {{-- Assurance --}}
    @php
    $caisse = $etat->caisse;
    $assurance = $etat->assurance;
    $couverture = intval($caisse?->couverture ?? 100);
    @endphp

    <td class="group p-4 transition-colors duration-300 hover:bg-indigo-50/30 rounded-2xl">
        @if($assurance && $couverture)
        <div class="flex flex-col items-center space-y-4">
            <!-- Nom de l'assurance -->
            <span
                class="inline-block px-5 py-2.5 bg-white/90 backdrop-blur-sm border border-indigo-200 rounded-xl text-indigo-700 font-bold shadow-md shadow-indigo-200/30">
                {{ $assurance->nom }}
            </span>

            <!-- Barre de progression moderne -->
            <div class="w-full max-w-xs bg-gray-200 rounded-full h-2.5 overflow-hidden">
                <div class="bg-gradient-to-r from-green-400 to-emerald-600 h-2.5 rounded-full transition-all duration-700 ease-out"
                    style="width: {{ $couverture }}%"></div>
            </div>

            <!-- Texte de pourcentage -->
            <span
                class="text-sm font-bold bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 to-green-500">
                {{ $couverture }}% de couverture
            </span>
        </div>
        @else
        <div class="flex justify-center">
            <span class="font-bolder bg-clip-text text-transparen bg-gray-100/80 rounded-lg animate-pulse">
                0 %
            </span>
        </div>
        @endif
    </td>

    {{-- Médecin cliquable --}}
    <td class="py-2 px-4">
        @if($etat->medecin)
        <a href="{{ route('superadmin.medecins.show', $etat->medecin_id) }}" class="text-blue-600 hover:underline">
            {{ $etat->medecin->nom }}
        </a>
        @else
        —
        @endif
    </td>
    <td class="py-2 px-4">
        <div class="flex space-x-2">
            <a href="{{ route('etatcaisse.show', $etat->id) }}" class="text-gray-500 hover:text-gray-700" title="Voir">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12H9m12 0A9 9 0 113 12a9 9 0 0118 0z" />
                </svg>
            </a>

            <a href="{{ route('etatcaisse.edit', $etat->id) }}" class="text-indigo-500 hover:text-indigo-700"
                title="Modifier">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414
                        a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </a>

            <form action="{{ route('etatcaisse.destroy', $etat->id) }}" method="POST"
                onsubmit="return confirm('Supprimer ?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-red-500 hover:text-red-700" title="Supprimer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </form>
        </div>
    </td>
</tr>
