<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Ordonnance - {{ $ordonnance->reference }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            size: A5;
            margin: 0;
        }
        
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            background: #fff;
        }
        
        .page {
            width: 148mm;
            min-height: 210mm;
            padding: 8mm 10mm;
            margin: 0 auto;
            background: #fff;
            position: relative;
        }
        
        /* En-tête à 3 colonnes */
        .header {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 10px;
            display: table;
        }
        
        .header-left {
            display: table-cell;
            width: 35%;
            vertical-align: top;
            text-align: left;
            padding-right: 5px;
        }
        
        .header-center {
            display: table-cell;
            width: 30%;
            vertical-align: top;
            text-align: center;
        }
        
        .header-right {
            display: table-cell;
            width: 35%;
            vertical-align: top;
            text-align: right;
            padding-left: 5px;
        }
        
        .header-left .clinique-name {
            font-size: 13px;
            font-weight: bold;
            color: #c00;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        
        .header-left .info-line,
        .header-right .info-line {
            font-size: 8px;
            line-height: 1.5;
            color: #000;
        }
        
        .header-center img {
            width: 40px;
            height: 40px;
        }
        
        .header-right .clinique-name-ar {
            font-size: 13px;
            font-weight: bold;
            color: #c00;
            margin-bottom: 3px;
        }
        
        /* Informations Patient et Médecin */
        .info-box {
            width: 100%;
            border: 1px solid #000;
            margin-bottom: 10px;
            display: table;
        }
        
        .info-left {
            display: table-cell;
            width: 50%;
            padding: 8px;
            border-right: 1px solid #000;
            vertical-align: top;
        }
        
        .info-right {
            display: table-cell;
            width: 50%;
            padding: 8px;
            vertical-align: top;
        }
        
        .info-label {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 2px;
        }
        
        .info-value {
            font-size: 11px;
            margin-bottom: 6px;
        }
        
        /* Titre */
        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 15px 0;
            letter-spacing: 1px;
        }
        
        /* Zone prescription */
        .prescription-box {
            min-height: 300px;
            padding: 15px;
            background: #fff;
        }
        
        .medication {
            margin-bottom: 15px;
        }
        
        .medication-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 3px;
        }
        
        .medication-dosage {
            font-size: 10px;
            margin-left: 15px;
            line-height: 1.6;
        }
        
        /* Signature */
        .signature-section {
            margin-top: 40px;
            text-align: center;
            font-size: 9px;
        }
        
        /* Footer */
        .footer {
            position: absolute;
            bottom: 8mm;
            left: 10mm;
            right: 10mm;
            text-align: center;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- En-tête -->
        <div class="header">
            <!-- Partie gauche (Français) -->
            <div class="header-left">
                <div class="clinique-name">{{ config('clinique.name') }}</div>
                <div class="info-line">{{ config('clinique.address') }}</div>
                <div class="info-line">{{ config('clinique.phone') }}</div>
                <div class="info-line">{{ config('clinique.whatsapp') }}</div>
                <div class="info-line">{{ config('clinique.email') }}</div>
            </div>
            
            <!-- Logo au centre -->
            <div class="header-center">
                <img src="{{ public_path(config('clinique.logo_path')) }}" alt="Logo">
            </div>
            
            <!-- Partie droite (Arabe) -->
            <div class="header-right">
                <div class="clinique-name-ar">{{ config('clinique.name_ar') }}</div>
                <div class="info-line">{{ config('clinique.address_ar') }}</div>
                <div class="info-line">{{ config('clinique.phone_ar') }}</div>
                <div class="info-line">{{ config('clinique.email_ar') }}</div>
            </div>
        </div>
        
        <!-- Informations Patient et Médecin -->
        <div class="info-box">
            <div class="info-left">
                <div class="info-label">Patient : {{ $ordonnance->patient->first_name }} {{ $ordonnance->patient->last_name }}</div>
                <div class="info-value">N. {{ $ordonnance->patient->id }}</div>
            </div>
            <div class="info-right">
                <div class="info-label">Médecin : Dr {{ strtoupper($ordonnance->medecin->nom ?? '') }} {{ $ordonnance->medecin->prenom ?? '' }}</div>
                <div class="info-label">Spécialité : {{ $ordonnance->medecin->specialite ?? 'Pédiatre' }}</div>
                <div class="info-label">Date : {{ $ordonnance->created_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
        
        <!-- Titre -->
        <div class="title">ORDONNANCE MÉDICALE</div>
        
        <!-- Prescription -->
        <div class="prescription-box">
            @foreach($ordonnance->medicaments as $index => $med)
                <div class="medication">
                    <div class="medication-title">* {{ strtoupper($med->medicament_nom) }} *</div>
                    <div class="medication-dosage">
                        @if($med->dosage)
                            {{ $med->dosage }}
                        @endif
                        @if($med->duree)
                            | {{ $med->duree }}
                        @endif
                        @if($med->note)
                            <br>{{ $med->note }}
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Signature -->
        <div class="signature-section">
            Signature / Cachet du Médecin
        </div>
        
        <!-- Footer -->
        <div class="footer">
            {{ config('clinique.website') }}
        </div>
    </div>
</body>
</html>

