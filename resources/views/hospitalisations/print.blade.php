@extends('layouts.print', ['formatClass' => 'format-a5'])

@section('title', 'Facture Hospitalisation N°' . $hospitalisation->id)

@section('no-footer', true)

@section('custom-header')
<div class="px-4 pt-4 pb-2 flex justify-between items-start border-b border-gray-800 mb-4">
    <!-- Français -->
    <div class="text-left text-[10px] leading-tight w-1/3">
        <div class="font-bold text-[11px]">{{ config('clinique.name') }}</div>
        <div>{{ config('clinique.director_name') }}</div>
        <div>{{ config('clinique.director_specialty') }}</div>
        <div class="text-gray-600 mt-1">{{ config('clinique.center_type') }}</div>
        <div class="text-gray-500 text-[9px]">{{ config('clinique.services_description') }}</div>
    </div>

    <!-- Logo -->
    <div class="text-center w-1/3">
        <img src="{{ asset(config('clinique.logo_path')) }}" alt="Logo" class="h-20 mx-auto object-contain">
    </div>

    <!-- Arabe -->
    <div class="text-right text-[10px] leading-tight w-1/3" dir="rtl">
        <div class="font-bold text-[11px]">{{ config('clinique.name_ar') }}</div>
        <div>{{ config('clinique.director_name_ar') }}</div>
        <div>{{ config('clinique.director_specialty_ar') }}</div>
        <div class="text-gray-600 mt-1">{{ config('clinique.center_type_ar') }}</div>
        <div class="text-gray-500 text-[9px]">{{ config('clinique.services_description_ar') }}</div>
    </div>
</div>

<div class="text-center text-[9px] text-gray-600 mb-2">
    {{ config('clinique.phone') }} <br>
    {{ config('clinique.address') }}
</div>
@endsection

@section('content')
    <!-- Titre -->
    <div class="text-center border-b border-dashed border-gray-400 pb-2 mb-4">
        <h2 class="font-bold text-sm">FACTURE HOSPITALISATION N° {{ $hospitalisation->id }}</h2>
    </div>

    <!-- Infos Patient -->
    <div class="text-xs mb-4 space-y-1">
        <div class="flex">
            <span class="w-24 text-gray-600">Patient :</span>
            <span class="font-semibold">{{ $hospitalisation->patient->first_name ?? '' }} {{ $hospitalisation->patient->last_name ?? '' }}</span>
        </div>
        <div class="flex">
            <span class="w-24 text-gray-600">Téléphone :</span>
            <span>{{ $hospitalisation->patient->phone ?? 'N/A' }}</span>
        </div>
        <div class="flex">
            <span class="w-24 text-gray-600">Médecin(s) :</span>
            <span>
                @php
                    $medecinsImpliques = $hospitalisation->getAllInvolvedDoctors();
                @endphp
                @if($medecinsImpliques->count() > 0)
                    {{ $medecinsImpliques->map(fn($m) => $m['medecin']->nom_complet_avec_prenom)->join(', ') }}
                @else
                    N/A
                @endif
            </span>
        </div>
        <div class="flex">
            <span class="w-24 text-gray-600">Service :</span>
            <span>{{ $hospitalisation->service->nom ?? 'N/A' }}</span>
        </div>
        @if($hospitalisation->lit && $hospitalisation->lit->chambre)
        <div class="flex">
            <span class="w-24 text-gray-600">Chambre :</span>
            <span>{{ $hospitalisation->lit->chambre->nom }} - Lit {{ $hospitalisation->lit->numero }}</span>
        </div>
        @endif
        <div class="flex">
            <span class="w-24 text-gray-600">Date d'entrée :</span>
            <span>
                @php
                    $dateEntree = \Carbon\Carbon::parse($hospitalisation->date_entree);
                    // Utiliser created_at pour l'heure de création de l'hospitalisation
                    $heureEntree = $hospitalisation->created_at ? \Carbon\Carbon::parse($hospitalisation->created_at) : $dateEntree;
                @endphp
                {{ $dateEntree->format('d/m/Y') }} {{ $heureEntree->format('H') }}h {{ $heureEntree->format('i') }}mn
            </span>
        </div>
        @if($hospitalisation->date_sortie)
        <div class="flex">
            <span class="w-24 text-gray-600">Date de sortie :</span>
            <span>
                @php
                    $dateSortie = \Carbon\Carbon::parse($hospitalisation->date_sortie);
                    // Utiliser discharge_at si disponible (enregistré lors du paiement)
                    // Sinon chercher la date de création de la dernière caisse (paiement)
                    $heureSortie = null;
                    if ($hospitalisation->discharge_at) {
                        $heureSortie = \Carbon\Carbon::parse($hospitalisation->discharge_at);
                    } else {
                        // Chercher la date de création de la dernière caisse liée à cette hospitalisation
                        $derniereCaisse = \App\Models\Caisse::where('gestion_patient_id', $hospitalisation->gestion_patient_id)
                            ->whereHas('examen', function($q) {
                                $q->where('nom', 'Hospitalisation');
                            })
                            ->orderBy('created_at', 'desc')
                            ->first();
                        if ($derniereCaisse && $derniereCaisse->created_at) {
                            $heureSortie = \Carbon\Carbon::parse($derniereCaisse->created_at);
                        } else {
                            // Fallback sur updated_at si le statut est "terminé"
                            $heureSortie = ($hospitalisation->statut === 'terminé' && $hospitalisation->updated_at) 
                                ? \Carbon\Carbon::parse($hospitalisation->updated_at) 
                                : $dateSortie;
                        }
                    }
                @endphp
                {{ $dateSortie->format('d/m/Y') }} {{ $heureSortie->format('H') }}h {{ $heureSortie->format('i') }}mn
            </span>
        </div>
        @endif
    </div>

    <div class="border-b border-dashed border-gray-400 mb-4"></div>

    <!-- Charges facturées -->
    <div class="mb-2">
        <h3 class="font-bold text-xs mb-2">Charges facturées</h3>
        <table class="w-full text-xs">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-1 text-left pl-2">Description</th>
                    <th class="py-1 text-center">Qté</th>
                    <th class="py-1 text-right pr-2">Montant</th>
                </tr>
            </thead>
            <tbody>
                @forelse($chargesFacturees as $charge)
                <tr class="border-b border-gray-100">
                    <td class="py-1 pl-2">{{ $charge->description_snapshot }}</td>
                    <td class="py-1 text-center">{{ $charge->quantity }}</td>
                    <td class="py-1 text-right pr-2 font-medium">{{ number_format($charge->total_price, 0, ',', ' ') }} MRU</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="py-2 text-center italic text-gray-500">Aucune charge facturée</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="border-t border-gray-300">
                @php
                    // Récupérer l'assurance et la couverture depuis la caisse si l'hospitalisation ne les a pas
                    $caisseIds = $chargesFacturees->pluck('caisse_id')->filter()->unique();
                    $caisse = null;
                    if ($caisseIds->isNotEmpty()) {
                        $caisse = \App\Models\Caisse::with('assurance')->find($caisseIds->first());
                    }
                    
                    $assuranceId = $caisse ? ($caisse->assurance_id ?? $hospitalisation->assurance_id) : $hospitalisation->assurance_id;
                    $couverture = $caisse ? ($caisse->couverture ?? $hospitalisation->couverture ?? 0) : ($hospitalisation->couverture ?? 0);
                    $assurance = $hospitalisation->assurance ?? ($caisse ? $caisse->assurance : null);
                    
                    $totalBrut = $chargesFacturees->sum('total_price');
                    $montantAssurance = $assuranceId && $couverture > 0 
                        ? ($totalBrut * ($couverture / 100)) 
                        : 0;
                @endphp
                @if($assurance && $couverture > 0)
                {{-- Afficher le total brut --}}
                <tr>
                    <th colspan="2" class="py-2 text-right pr-4">Total brut</th>
                    <th class="py-2 text-right pr-2 text-sm">{{ number_format($totalBrut, 0, ',', ' ') }} MRU</th>
                </tr>
                {{-- Afficher la déduction assurance --}}
                <tr>
                    <td colspan="2" class="py-1 text-right pr-4 text-gray-600">
                        Couverture assurance ({{ $couverture }}% - {{ $assurance->nom }})
                    </td>
                    <td class="py-1 text-right pr-2 text-sm text-red-600">
                        -{{ number_format($montantAssurance, 0, ',', ' ') }} MRU
                    </td>
                </tr>
                {{-- Afficher le total net --}}
                <tr class="border-t border-gray-400">
                    <th colspan="2" class="py-2 text-right pr-4 font-bold">Total</th>
                    <th class="py-2 text-right pr-2 text-sm font-bold">{{ number_format($totalCharges, 0, ',', ' ') }} MRU</th>
                </tr>
                @else
                {{-- Pas d'assurance, afficher simplement le total --}}
                <tr>
                    <th colspan="2" class="py-2 text-right pr-4">Total</th>
                    <th class="py-2 text-right pr-2 text-sm">{{ number_format($totalCharges, 0, ',', ' ') }} MRU</th>
                </tr>
                @endif
            </tfoot>
        </table>
    </div>

    <div class="border-b border-dashed border-gray-400 mb-4"></div>

    <!-- Pied de page spécifique -->
    <div class="flex justify-between text-xs text-gray-500">
        <span>Caissier(e) : {{ Auth::user()->name ?? 'N/A' }}</span>
        <span>Date : {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</span>
    </div>

    @if($hospitalisation->observation)
    <div class="mt-4 text-xs bg-gray-50 p-2 rounded border border-gray-200">
        <div class="font-bold mb-1">Observations :</div>
        <div>{{ $hospitalisation->observation }}</div>
    </div>
    @endif
@endsection