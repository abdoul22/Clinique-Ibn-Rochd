<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ordonnance - {{ $ordonnance->reference }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            background-color: #e3f2fd;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #f5f9ff;
            padding: 30px;
            border: 1px solid #90caf9;
            min-height: 800px;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #1e40af;
            padding-bottom: 15px;
        }
        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        .header-right {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: top;
        }
        .logo {
            color: #1e40af;
            font-weight: bold;
            font-size: 18px;
            margin-bottom: 5px;
        }
        .contact-info {
            font-size: 10px;
            line-height: 1.4;
            color: #666;
        }
        .patient-box {
            display: table;
            width: 100%;
            border: 1px solid #90caf9;
            margin-bottom: 20px;
        }
        .patient-left {
            display: table-cell;
            width: 50%;
            padding: 15px;
            border-right: 1px solid #90caf9;
            background-color: #fff;
        }
        .patient-right {
            display: table-cell;
            width: 50%;
            padding: 15px;
            background-color: #fff;
        }
        .patient-label {
            font-weight: bold;
            font-size: 10px;
            color: #1565c0;
        }
        .patient-value {
            font-size: 11px;
            margin-top: 3px;
        }
        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 30px 0;
            color: #1e40af;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .prescription {
            background-color: #fff;
            border: 1px solid #90caf9;
            padding: 20px;
            min-height: 400px;
        }
        .medication {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px dotted #90caf9;
        }
        .medication:last-child {
            border-bottom: none;
        }
        .medication-name {
            font-weight: bold;
            font-size: 13px;
            color: #1e40af;
            margin-bottom: 5px;
        }
        .medication-details {
            font-size: 11px;
            line-height: 1.6;
            margin-left: 20px;
            color: #555;
        }
        .notes {
            background-color: #fff;
            border: 1px solid #90caf9;
            padding: 15px;
            margin-top: 20px;
            font-size: 10px;
            color: #666;
        }
        .signature {
            margin-top: 60px;
            text-align: right;
        }
        .signature-line {
            margin-top: 10px;
            font-size: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <div class="header">
            <div class="header-left">
                <div class="logo">{{ config('clinique.name') }}</div>
                <div class="contact-info">
                    {{ config('clinique.phone') }}<br>
                    {{ config('clinique.address') }}<br>
                    {{ config('clinique.website') }}
                </div>
            </div>
            <div class="header-right">
                <div class="contact-info">
                    <strong>{{ config('clinique.name_ar') }}</strong><br>
                    {{ config('clinique.phone') }}<br>
                    {{ config('clinique.address') }}
                </div>
            </div>
        </div>

        <!-- Informations Patient et Médecin -->
        <div class="patient-box">
            <div class="patient-left">
                <div class="patient-label">Patient :</div>
                <div class="patient-value">{{ $ordonnance->patient->first_name }} {{ $ordonnance->patient->last_name }}</div>
                <div class="patient-label" style="margin-top: 10px;">N. Tél :</div>
                <div class="patient-value">{{ $ordonnance->patient->phone ?? 'N/A' }}</div>
            </div>
            <div class="patient-right">
                <div class="patient-label">Médecin :</div>
                <div class="patient-value">{{ $ordonnance->medecin->nom_complet_avec_prenom }}</div>
                <div class="patient-label" style="margin-top: 10px;">Spécialité :</div>
                <div class="patient-value">{{ $ordonnance->medecin->specialite ?? 'Médecin' }}</div>
                <div class="patient-label" style="margin-top: 10px;">Date :</div>
                <div class="patient-value">{{ $ordonnance->date_ordonnance->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <!-- Titre -->
        <div class="title">ORDONNANCE MÉDICALE</div>

        <!-- Prescription -->
        <div class="prescription">
            @foreach($ordonnance->medicaments as $index => $med)
                <div class="medication">
                    <div class="medication-name">* {{ strtoupper($med->medicament_nom) }}</div>
                    <div class="medication-details">
                        @if($med->dosage)
                            {{ $med->dosage }}
                        @endif
                        @if($med->duree)
                            | {{ $med->duree }}
                        @endif
                        @if($med->note)
                            <br><em>{{ $med->note }}</em>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Notes -->
        @if($ordonnance->notes)
        <div class="notes">
            <strong>Notes :</strong><br>
            {{ $ordonnance->notes }}
        </div>
        @endif

        <!-- Signature -->
        <div class="signature">
            <div class="signature-line">
                Signature / Cachet du Médecin
            </div>
        </div>

        <!-- Footer avec référence -->
        <div style="margin-top: 30px; text-align: center; font-size: 9px; color: #999;">
            Référence : {{ $ordonnance->reference }}
        </div>
    </div>
</body>
</html>

