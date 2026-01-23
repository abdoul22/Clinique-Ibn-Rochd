<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport d'Observation - {{ $consultation->patient->first_name }} {{ $consultation->patient->last_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4;
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
            width: 210mm;
            min-height: 297mm;
            padding: 15mm 20mm;
            margin: 0 auto;
            background: #fff;
            position: relative;
        }

        /* En-tête à 3 colonnes */
        .header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header-left {
            display: table-cell;
            width: 35%;
            vertical-align: top;
            text-align: left;
            padding-right: 10px;
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
            padding-left: 10px;
        }

        .header-left .clinique-name {
            font-size: 14px;
            font-weight: bold;
            color: #c00;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .header-left .info-line,
        .header-right .info-line {
            font-size: 9px;
            line-height: 1.6;
            color: #000;
        }

        .header-center img {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        .header-right .clinique-name-ar {
            font-size: 15px;
            font-weight: bold;
            color: #c00;
            margin-bottom: 4px;
            direction: rtl;
        }

        /* Section informations patient/médecin */
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .info-row {
            display: table-row;
        }

        .info-cell {
            display: table-cell;
            padding: 5px 0;
            font-size: 10px;
        }

        .info-row:last-child .info-cell {
            border-bottom: none;
        }

        .info-label {
            font-weight: bold;
            width: 25%;
        }

        .info-value {
            width: 25%;
        }

        /* Titre */
        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 20px 0;
            letter-spacing: 1px;
        }

        /* Sections du rapport */
        .section {
            margin-bottom: 15px;
        }

        .section-title {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 5px;
            color: #000;
        }

        .section-content {
            padding: 5px 0;
            min-height: 20px;
            font-size: 10px;
            line-height: 1.6;
            background: #fff;
        }

        /* Signature */
        .signature-section {
            margin-top: 50px;
            text-align: right;
            font-size: 10px;
        }

        /* Footer */
        .footer {
            position: absolute;
            bottom: 15mm;
            right: 20mm;
            text-align: right;
            font-size: 9px;
            color: #666;
        }

        /* Boutons d'action en haut à droite */
        .action-buttons {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            gap: 10px;
        }

        .action-buttons button {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-print {
            background: #3b82f6;
            color: white;
        }

        .btn-print:hover {
            background: #2563eb;
        }

        .btn-close {
            background: #6b7280;
            color: white;
        }

        .btn-close:hover {
            background: #4b5563;
        }

        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .page {
                box-shadow: none;
                margin: 0;
            }

            .no-print {
                display: none;
            }
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

        <!-- Informations Patient et Médecin -->
        <div class="info-section">
            <div class="info-row">
                <div class="info-cell info-label">Patient :</div>
                <div class="info-cell info-value">{{ $consultation->patient->first_name }} {{ $consultation->patient->last_name }}</div>
                <div class="info-cell info-label">Médecin :</div>
                <div class="info-cell info-value">Dr. {{ $consultation->medecin->nom }} {{ $consultation->medecin->prenom }}</div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Téléphone :</div>
                <div class="info-cell info-value">{{ $consultation->patient->phone ?? 'N/A' }}</div>
                <div class="info-cell info-label">Spécialité :</div>
                <div class="info-cell info-value">{{ $consultation->medecin->specialite ?? 'Médecin' }}</div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">Âge :</div>
                <div class="info-cell info-value">{{ $consultation->patient->age ?? 'N/A' }}</div>
                <div class="info-cell info-label">Date :</div>
                <div class="info-cell info-value">{{ $consultation->created_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <!-- Titre -->
        <div class="title">RAPPORT D'OBSERVATION</div>

        <!-- Motif -->
        @if($consultation->motif)
        <div class="section">
            <div class="section-title">Motif :</div>
            <div class="section-content">{{ $consultation->motif }}</div>
        </div>
        @endif

        <!-- Antécédents -->
        @if($consultation->antecedents)
        <div class="section">
            <div class="section-title">Antécédents :</div>
            <div class="section-content">{{ $consultation->antecedents }}</div>
        </div>
        @endif

        <!-- Histoire du malade -->
        @if($consultation->histoire_maladie)
        <div class="section">
            <div class="section-title">Histoire du malade :</div>
            <div class="section-content">{{ $consultation->histoire_maladie }}</div>
        </div>
        @endif

        <!-- Examen clinique -->
        @if($consultation->examen_clinique)
        <div class="section">
            <div class="section-title">Examen clinique :</div>
            <div class="section-content">{{ $consultation->examen_clinique }}</div>
        </div>
        @endif

        <!-- Conduite à tenir -->
        @if($consultation->conduite_tenir)
        <div class="section">
            <div class="section-title">Conduite à tenir :</div>
            <div class="section-content">{{ $consultation->conduite_tenir }}</div>
        </div>
        @endif

        <!-- Résumé -->
        @if($consultation->resume)
        <div class="section">
            <div class="section-title">Résumé :</div>
            <div class="section-content">{{ $consultation->resume }}</div>
        </div>
        @endif

        <!-- Footer avec signature -->
        <div class="footer">
            <strong>Signature / Cachet du Médecin</strong>
        </div>
    </div>

    <div class="action-buttons no-print">
        <button onclick="window.print()" class="btn-print">
            🖨️ Imprimer
        </button>
        <button onclick="window.close()" class="btn-close">
            Fermer
        </button>
    </div>
</body>

</html>

