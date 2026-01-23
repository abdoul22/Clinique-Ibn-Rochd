<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            font-family: Arial, Helvetica, sans-serif;
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
            display: table;
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 10px;
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
            direction: rtl;
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
            font-size: 9px;
            line-height: 1.5;
            color: #000;
        }

        .header-center img {
            width: 45px;
            height: 45px;
            object-fit: contain;
        }

        .header-right .clinique-name-ar {
            font-size: 14px;
            font-weight: bold;
            color: #c00;
            margin-bottom: 3px;
            direction: rtl;
        }

        /* Informations Patient et Médecin */
        .info-box {
            border: 1px solid #000;
            padding: 6px;
            margin-bottom: 10px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 3px;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            display: table-cell;
            width: 30%;
            font-weight: bold;
            font-size: 10px;
        }

        .info-value {
            display: table-cell;
            width: 70%;
            font-size: 10px;
        }

        /* Titre */
        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 10px 0;
        }

        /* Liste des médicaments */
        .medicaments-list {
            margin-top: 10px;
        }

        .medicament-item {
            border-left: 3px solid #c00;
            padding: 6px;
            margin-bottom: 8px;
            background: #f9f9f9;
        }

        .medicament-name {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 3px;
        }

        .medicament-details {
            font-size: 10px;
            line-height: 1.5;
        }

        .medicament-details .detail-line {
            margin-bottom: 2px;
        }

        .medicament-details strong {
            color: #c00;
        }

        /* Notes */
        .notes-box {
            border: 1px dashed #666;
            padding: 6px;
            margin-top: 10px;
            background: #ffe;
        }

        .notes-title {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 3px;
        }

        .notes-content {
            font-size: 10px;
        }

        /* Footer */
        .footer {
            position: absolute;
            bottom: 8mm;
            right: 10mm;
            text-align: right;
            font-size: 10px;
        }

        /* Boutons d'action */
        .action-buttons {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 9999;
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            text-decoration: none;
        }

        .btn-print {
            background: #6366f1;
            color: white;
        }

        .btn-print:hover {
            background: #4f46e5;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(99,102,241,0.4);
        }

        .btn-back {
            background: #6b7280;
            color: white;
        }

        .btn-back:hover {
            background: #4b5563;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(107,114,128,0.4);
        }

        @media print {
            .page {
                margin: 0;
                border: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
                page-break-after: always;
            }

            .footer {
                position: fixed;
                bottom: 8mm;
            }

            .action-buttons {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <!-- Boutons d'action -->
    <div class="action-buttons">
        <a href="{{ route('superadmin.medical.ordonnances.show', $ordonnance->id) }}" class="btn btn-back">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Retour
        </a>
        <button onclick="window.print()" class="btn btn-print">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Imprimer
        </button>
    </div>

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
                <img src="{{ asset(config('clinique.logo_path')) }}" alt="Logo">
            </div>

            <!-- Partie droite (Arabe) -->
            <div class="header-right">
                <div class="clinique-name-ar">{{ config('clinique.name_ar') }}</div>
                <div class="info-line">{{ config('clinique.address_ar') }}</div>
                <div class="info-line">{{ config('clinique.phone_ar') }}</div>
                <div class="info-line">{{ config('clinique.email_ar') }}</div>
            </div>
        </div>

        <!-- Titre -->
        <div class="title">ORDONNANCE MÉDICALE</div>

        <!-- Informations Patient et Médecin -->
        <div class="info-box">
            <div class="info-row">
                <div class="info-label">Patient :</div>
                <div class="info-value">{{ $ordonnance->patient->first_name }} {{ $ordonnance->patient->last_name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Téléphone :</div>
                <div class="info-value">{{ $ordonnance->patient->phone ?? 'N/A' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Médecin :</div>
                <div class="info-value">{{ $ordonnance->medecin->fonction ?? 'Dr' }} {{ $ordonnance->medecin->nom }} {{ $ordonnance->medecin->prenom }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Spécialité :</div>
                <div class="info-value">{{ $ordonnance->medecin->specialite ?? 'Médecin' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Date :</div>
                <div class="info-value">{{ $ordonnance->date_ordonnance->format('d/m/Y') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Référence :</div>
                <div class="info-value">{{ $ordonnance->reference }}</div>
            </div>
        </div>

        <!-- Liste des médicaments -->
        <div class="medicaments-list">
            @foreach($ordonnance->medicaments as $index => $med)
            <div class="medicament-item">
                <div class="medicament-name">{{ $index + 1 }}. {{ $med->medicament_nom }}</div>
                <div class="medicament-details">
                    @if($med->dosage)
                    <div class="detail-line"><strong>Posologie :</strong> {{ $med->dosage }}</div>
                    @endif
                    @if($med->duree)
                    <div class="detail-line"><strong>Durée :</strong> {{ $med->duree }}</div>
                    @endif
                    @if($med->note)
                    <div class="detail-line"><strong>Note :</strong> {{ $med->note }}</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <!-- Notes additionnelles -->
        @if($ordonnance->notes)
        <div class="notes-box">
            <div class="notes-title">Notes :</div>
            <div class="notes-content">{{ $ordonnance->notes }}</div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <strong>Signature / Cachet du Médecin</strong>
        </div>
    </div>
</body>

</html>
