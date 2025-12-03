@extends('layouts.print')

@section('title', 'Ordonnance - ' . $ordonnance->reference)

@push('styles')
<style>
    .ordonnance-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        margin: -15mm -15mm 20px -15mm;
    }
    
    .format-a5 .ordonnance-header {
        margin: -10mm -10mm 15px -10mm;
        padding: 15px;
    }
    
    .ordonnance-title {
        font-size: 24px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    
    .format-a5 .ordonnance-title {
        font-size: 20px;
    }
    
    .patient-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 25px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
    }
    
    .format-a5 .patient-info {
        gap: 15px;
        padding: 12px;
    }
    
    .info-section h3 {
        font-size: 12px;
        font-weight: bold;
        color: #667eea;
        margin-bottom: 8px;
        text-transform: uppercase;
    }
    
    .format-a5 .info-section h3 {
        font-size: 11px;
    }
    
    .info-section p {
        font-size: 14px;
        color: #333;
        margin: 4px 0;
    }
    
    .format-a5 .info-section p {
        font-size: 12px;
    }
    
    .medicaments-section {
        margin-top: 25px;
    }
    
    .medicament-item {
        margin-bottom: 20px;
        padding: 15px;
        border-left: 4px solid #667eea;
        background: #f8f9fa;
        border-radius: 4px;
    }
    
    .format-a5 .medicament-item {
        padding: 12px;
        margin-bottom: 15px;
    }
    
    .medicament-number {
        font-weight: bold;
        color: #667eea;
        font-size: 16px;
        margin-right: 8px;
    }
    
    .format-a5 .medicament-number {
        font-size: 14px;
    }
    
    .medicament-name {
        font-weight: bold;
        font-size: 16px;
        color: #333;
        margin-bottom: 8px;
    }
    
    .format-a5 .medicament-name {
        font-size: 14px;
    }
    
    .medicament-details {
        font-size: 13px;
        color: #555;
        margin-left: 24px;
        line-height: 1.6;
    }
    
    .format-a5 .medicament-details {
        font-size: 12px;
        margin-left: 20px;
    }
    
    .medicament-details strong {
        color: #667eea;
    }
    
    .notes-section {
        margin-top: 25px;
        padding: 15px;
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        border-radius: 4px;
    }
    
    .format-a5 .notes-section {
        padding: 12px;
        margin-top: 20px;
    }
    
    .notes-section h3 {
        font-size: 13px;
        font-weight: bold;
        color: #856404;
        margin-bottom: 8px;
    }
    
    .format-a5 .notes-section h3 {
        font-size: 12px;
    }
    
    .notes-section p {
        font-size: 13px;
        color: #856404;
    }
    
    .format-a5 .notes-section p {
        font-size: 12px;
    }
    
    .signature-section {
        margin-top: 40px;
        text-align: right;
        padding-top: 20px;
        border-top: 2px dashed #ddd;
    }
    
    .format-a5 .signature-section {
        margin-top: 30px;
    }
    
    .signature-line {
        margin-top: 60px;
        font-size: 12px;
        color: #666;
    }
    
    .format-a5 .signature-line {
        margin-top: 40px;
        font-size: 11px;
    }
    
    .reference-footer {
        margin-top: 20px;
        text-align: center;
        font-size: 10px;
        color: #999;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }
    
    .format-a5 .reference-footer {
        font-size: 9px;
        margin-top: 15px;
    }
</style>
@endpush

@section('content')
<div class="ordonnance-header">
    <div class="ordonnance-title">💊 Ordonnance Médicale</div>
    <div style="text-align: center; font-size: 12px; opacity: 0.9;">
        Référence: {{ $ordonnance->reference }}
    </div>
</div>

<!-- Informations Patient et Médecin -->
<div class="patient-info">
    <div class="info-section">
        <h3>👤 Patient</h3>
        <p style="font-weight: bold; font-size: 16px;">
            {{ $ordonnance->patient->first_name }} {{ $ordonnance->patient->last_name }}
        </p>
        <p>Téléphone: {{ $ordonnance->patient->phone ?? 'N/A' }}</p>
        @if($ordonnance->patient->date_naissance)
        <p>Âge: {{ \Carbon\Carbon::parse($ordonnance->patient->date_naissance)->age }} ans</p>
        @endif
    </div>
    
    <div class="info-section">
        <h3>👨‍⚕️ Médecin</h3>
        <p style="font-weight: bold;">{{ $ordonnance->medecin->nom_complet_avec_prenom }}</p>
        <p>Spécialité: {{ $ordonnance->medecin->specialite ?? 'Médecin' }}</p>
        <p>Date: {{ $ordonnance->date_ordonnance->format('d/m/Y') }}</p>
        @if($ordonnance->date_expiration)
        <p>Expire le: {{ $ordonnance->date_expiration->format('d/m/Y') }}</p>
        @endif
    </div>
</div>

<!-- Médicaments prescrits -->
<div class="medicaments-section">
    <h2 style="font-size: 18px; font-weight: bold; color: #667eea; margin-bottom: 20px; text-align: center;">
        Médicaments Prescrits
    </h2>
    
    @foreach($ordonnance->medicaments as $index => $med)
    <div class="medicament-item">
        <div class="medicament-name">
            <span class="medicament-number">{{ $index + 1 }}.</span>
            {{ $med->medicament_nom }}
        </div>
        <div class="medicament-details">
            @if($med->dosage)
            <p><strong>Dosage:</strong> {{ $med->dosage }}</p>
            @endif
            @if($med->duree)
            <p><strong>Durée:</strong> {{ $med->duree }}</p>
            @endif
            @if($med->note)
            <p style="margin-top: 8px; font-style: italic; color: #666;">
                <strong>Note:</strong> {{ $med->note }}
            </p>
            @endif
        </div>
    </div>
    @endforeach
</div>

<!-- Notes générales -->
@if($ordonnance->notes)
<div class="notes-section">
    <h3>📝 Notes</h3>
    <p>{{ $ordonnance->notes }}</p>
</div>
@endif

<!-- Signature -->
<div class="signature-section">
    <div class="signature-line">
        <p>Signature / Cachet du Médecin</p>
        <div style="margin-top: 50px; border-top: 1px solid #333; width: 200px; margin-left: auto;"></div>
    </div>
</div>

<!-- Référence -->
<div class="reference-footer">
    <p>Référence: {{ $ordonnance->reference }} | Date d'émission: {{ $ordonnance->date_ordonnance->format('d/m/Y H:i') }}</p>
</div>
@endsection

@section('footer')
<div class="mt-8 pt-4 border-t border-gray-300 text-center text-xs text-gray-500">
    <p>{{ config('clinique.name') }} - {{ config('clinique.address') }}</p>
    <p>Tél: {{ config('clinique.phone') }} | {{ config('clinique.website') }}</p>
</div>
@endsection

@php
    $backUrl = route('medecin.ordonnances.show', $ordonnance->id);
@endphp

