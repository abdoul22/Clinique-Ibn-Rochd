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

    <td class="py-2 px-4">
        {{ number_format($etat->recette, 0, ',', ' ') }} MRU
    </td>

    <td class="py-2 px-4">
        {{ number_format($etat->part_medecin, 0, ',', ' ') }} MRU
    </td>

    <td class="py-2 px-4">
        {{ number_format($etat->part_clinique, 0, ',', ' ') }} MRU
    </td>

    <td class="py-2 px-4">
        @php $paiement = optional($etat->caisse)->paiements; @endphp
        @if($paiement)
        <p class="text-blue-800 font-bold">{{ $paiement->type }}</p>
        <small class="font-bold ">( {{ number_format($paiement->montant, 0, ',', ' ') }} ) MRU</small>
        @else
        —
        @endif
    </td>

    <td class="py-2 px-4">
        {{ $etat->personnel?->nom ?? '—' }}<br>
        <small>{{ number_format($etat->personnel_credit, 0, ',', ' ') }} MRU</small>
    </td>
{{-- Assurance --}}
@php
    $caisse = $etat->caisse;
    $assurance = $etat->assurance;
    $couverture = intval($caisse?->couverture ?? 100);
@endphp

<td class="py-2 px-4">
    @if($assurance && $couverture < 100)
        {{ $assurance->nom }}
    @else
        —
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
