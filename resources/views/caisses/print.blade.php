@extends('layouts.print', ['formatClass' => 'format-a5'])

@section('title', 'Facture N°' . ($caisse->numero_facture ?? $caisse->id))

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
    <h2 class="font-bold text-sm">RECU N° {{ $caisse->numero_facture ?? $caisse->id }}</h2>
</div>

<!-- Infos Patient -->
<div class="text-xs mb-4 space-y-1">
    <div class="flex">
        <span class="w-24 text-gray-600">Numéro d'entrée :</span>
        <span class="font-semibold">{{ $caisse->numero_entre ?? '1' }}</span>
    </div>
    <div class="flex">
        <span class="w-24 text-gray-600">Nom du patient :</span>
        <span class="font-semibold">{{ ($caisse->patient->first_name ?? '') . ' ' . ($caisse->patient->last_name ?? '')
            }}</span>
    </div>
    <div class="flex">
        <span class="w-24 text-gray-600">Adresse / Tel :</span>
        <span>{{ $caisse->patient->phone ?? 'N/A' }}</span>
    </div>
    <div class="flex">
        <span class="w-24 text-gray-600">Prescripteur :</span>
        <span>{{ $caisse->prescripteur->nom ?? 'Externe' }}</span>
    </div>
    <div class="flex">
        <span class="w-24 text-gray-600">Examinateur :</span>
        <span>
            @if($caisse->medecin)
            {{ $caisse->medecin->nom_complet_avec_specialite }}
            @else
            N/A
            @endif
        </span>
    </div>
    <div class="flex">
        <span class="w-24 text-gray-600">Date de l'examen :</span>
        <span>
            @php
                $dateExamen = $caisse->created_at 
                    ? \Carbon\Carbon::parse($caisse->created_at) 
                    : \Carbon\Carbon::now();
            @endphp
            {{ $dateExamen->format('d/m/Y H:i') }}
        </span>
    </div>
</div>

<div class="border-b border-dashed border-gray-400 mb-4"></div>

<!-- Tableau Examens -->
<div class="mb-2">
    <h3 class="font-bold text-xs mb-2">Examens demandés</h3>
    <table class="w-full text-xs">
        <tbody>
            @php
                // Décoder examens_data si c'est une string JSON
                $examensData = is_string($caisse->examens_data) 
                    ? json_decode($caisse->examens_data, true) 
                    : $caisse->examens_data;
            @endphp
            @if($examensData && is_array($examensData) && count($examensData) > 0)
            @foreach($examensData as $examenData)
            @php
                $examen = \App\Models\Examen::find($examenData['id']);
                $tarifEffectif = $examen ? $examen->getTarifPourAssurance($caisse->assurance_id) : 0;
                $totalEffectif = $tarifEffectif * ($examenData['quantite'] ?? 1);
            @endphp
            <tr>
                <td class="py-1">
                    {{ $examenData['nom'] ?? 'N/A' }}
                    @if(isset($examenData['quantite']) && $examenData['quantite'] > 1)
                        <span class="text-gray-600">(Qté: {{ $examenData['quantite'] }})</span>
                    @endif
                </td>
                <td class="py-1 text-right font-medium">{{ number_format($totalEffectif, 0) }}</td>
            </tr>
            @endforeach
            @else
            <tr>
                <td class="py-1">{{ $caisse->examen->nom ?? 'Consultation' }}</td>
                <td class="py-1 text-right font-medium">{{ number_format($caisse->total, 0) }}</td>
            </tr>
            @endif
        </tbody>
        <tfoot class="border-t border-gray-300">
            @php
            $couverture = $caisse->couverture ?? 0;
            $montantAssurance = $caisse->assurance_id ? ($caisse->total * ($couverture / 100)) : 0;
            $montantPatient = $caisse->total - $montantAssurance;
            @endphp
            <tr>
                <th class="py-2 text-right">Total</th>
                <th class="py-2 text-right text-sm">{{ number_format($montantPatient, 0) }}</th>
            </tr>
            @if($caisse->assurance && $couverture > 0)
            <tr>
                <td colspan="2" class="text-center text-[10px] text-gray-500 py-1">
                    ({{ $couverture }}% pris en charge par {{ $caisse->assurance->nom }})
                </td>
            </tr>
            @endif
        </tfoot>
    </table>
</div>

<div class="border-b border-dashed border-gray-400 mb-4"></div>

<!-- Pied de page spécifique -->
<div class="flex justify-between text-xs text-gray-500">
    <span>Caissier(e) : {{ $caisse->nom_caissier ?? 'N/A' }}</span>
    <span>Date : {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</span>
</div>
@endsection
