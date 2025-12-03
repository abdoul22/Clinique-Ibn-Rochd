@extends('layouts.print', ['formatClass' => 'format-a5'])

@section('title', 'Facture N°' . ($caisse->numero_facture ?? $caisse->id))

@section('custom-header')
    <div class="px-4 pt-4 pb-2 border-b-2 border-gray-800 flex justify-between items-start" style="direction: ltr;">
        <!-- Français -->
        <div class="text-left" style="font-size: 10px; line-height: 1.2;">
            <div class="font-bold text-xs">{{ config('clinique.name') }}</div>
            <div>{{ config('clinique.director_name') }}</div>
            <div>{{ config('clinique.director_specialty') }}</div>
            <div class="text-gray-500 mt-1">{{ config('clinique.center_type') }}</div>
            <div class="text-gray-500 w-48">{{ config('clinique.services_description') }}</div>
        </div>

        <!-- Logo -->
        <div class="flex-shrink-0 mx-2">
            @if(config('clinique.logo_path'))
                <img src="{{ asset(config('clinique.logo_path')) }}" alt="Logo" class="h-20 w-auto object-contain">
            @endif
        </div>

        <!-- Arabe -->
        <div class="text-right" style="font-size: 10px; line-height: 1.2; direction: rtl;">
            <div class="font-bold text-xs">{{ config('clinique.name_ar') }}</div>
            <div>{{ config('clinique.director_name_ar') }}</div>
            <div>{{ config('clinique.director_specialty_ar') }}</div>
            <div class="text-gray-500 mt-1">{{ config('clinique.center_type_ar') }}</div>
            <div class="text-gray-500 w-48">{{ config('clinique.services_description_ar') }}</div>
        </div>
    </div>

    <div class="text-center text-xs text-gray-500 py-2 border-b border-gray-300">
        {{ config('clinique.phone') }}<br>
        {{ config('clinique.address') }}
    </div>
@endsection

@section('content')
    <!-- Titre -->
    <div class="text-center mb-4 mt-2">
        <h2 class="text-lg font-bold uppercase border-b border-dashed border-gray-400 inline-block pb-1">
            RECU N° {{ $caisse->numero_facture ?? $caisse->id }}
        </h2>
    </div>

    <!-- Infos Patient & Examen -->
    <div class="grid grid-cols-1 gap-1 text-xs mb-4">
        <div class="flex">
            <span class="w-24 font-semibold text-gray-600">Numéro d'entrée :</span>
            <span class="font-bold">{{ $caisse->numero_entre ?? '1' }}</span>
        </div>
        <div class="flex">
            <span class="w-24 font-semibold text-gray-600">Patient :</span>
            <span class="font-bold uppercase">{{ ($caisse->patient->first_name ?? '') . ' ' . ($caisse->patient->last_name ?? '') }}</span>
        </div>
        <div class="flex">
            <span class="w-24 font-semibold text-gray-600">Téléphone :</span>
            <span>{{ $caisse->patient->phone ?? 'N/A' }}</span>
        </div>
        <div class="flex">
            <span class="w-24 font-semibold text-gray-600">Prescripteur :</span>
            <span>{{ $caisse->prescripteur->nom ?? 'Externe' }}</span>
        </div>
        <div class="flex">
            <span class="w-24 font-semibold text-gray-600">Examinateur :</span>
            <span>
                @if($caisse->medecin)
                    {{ $caisse->medecin->nom_complet_avec_specialite }}
                @else
                    N/A
                @endif
            </span>
        </div>
        <div class="flex">
            <span class="w-24 font-semibold text-gray-600">Date :</span>
            <span>{{ $caisse->date_examen ? \Carbon\Carbon::parse($caisse->date_examen)->format('d/m/Y') : now()->format('d/m/Y') }} {{ now()->format('H:i') }}</span>
        </div>
    </div>

    <div class="border-t border-dashed border-gray-400 my-3"></div>

    <!-- Tableau des Examens -->
    <div class="mb-4">
        <h3 class="font-bold text-xs mb-2 uppercase">Détails des Examens</h3>
        <table class="w-full text-xs">
            <thead class="border-b border-gray-200 bg-gray-50">
                <tr>
                    <th class="text-left py-1 px-2">Désignation</th>
                    <th class="text-right py-1 px-2">Montant</th>
                </tr>
            </thead>
            <tbody>
                @if($caisse->examens_data)
                    @php $examensData = json_decode($caisse->examens_data, true); @endphp
                    @foreach($examensData as $examenData)
                    <tr class="border-b border-gray-100">
                        <td class="py-1 px-2">{{ $examenData['nom'] ?? 'N/A' }}</td>
                        <td class="py-1 px-2 text-right font-medium">{{ number_format($examenData['total'] ?? 0, 0, ',', ' ') }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr class="border-b border-gray-100">
                        <td class="py-1 px-2">{{ $caisse->examen->nom ?? 'Consultation' }}</td>
                        <td class="py-1 px-2 text-right font-medium">{{ number_format($caisse->total, 0, ',', ' ') }}</td>
                    </tr>
                @endif
            </tbody>
            <tfoot class="bg-gray-50 border-t border-gray-300">
                @php
                    $couverture = $caisse->couverture ?? 0;
                    $montantAssurance = $caisse->assurance_id ? ($caisse->total * ($couverture / 100)) : 0;
                    $montantPatient = $caisse->total - $montantAssurance;
                @endphp
                <tr>
                    <td class="py-2 px-2 font-bold text-right">NET À PAYER</td>
                    <td class="py-2 px-2 text-right font-bold text-sm">{{ number_format($montantPatient, 0, ',', ' ') }} MRU</td>
                </tr>
                @if($caisse->assurance && $couverture > 0)
                <tr>
                    <td colspan="2" class="py-1 px-2 text-center text-gray-500 italic text-[10px]">
                        (Couverture {{ $caisse->assurance->nom }}: {{ $couverture }}% - {{ number_format($montantAssurance, 0, ',', ' ') }} MRU)
                    </td>
                </tr>
                @endif
            </tfoot>
        </table>
    </div>

    <div class="border-t border-dashed border-gray-400 my-4"></div>

    <!-- Pied de page spécifique -->
    <div class="flex justify-between items-end text-xs">
        <div>
            <span class="font-semibold">Caissier(e) :</span> {{ $caisse->nom_caissier ?? 'N/A' }}
        </div>
        <div class="text-right italic text-gray-500">
            Merci de votre confiance
        </div>
    </div>
@endsection