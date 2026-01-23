<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rapport d'Observation - {{ $consultation->patient->first_name }} {{ $consultation->patient->last_name }}</title>
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
        .patient-info {
            background-color: #fff;
            border: 1px solid #90caf9;
            padding: 15px;
            margin-bottom: 20px;
        }
        .patient-info table {
            width: 100%;
        }
        .patient-info td {
            padding: 5px;
            font-size: 11px;
        }
        .patient-info td:first-child {
            font-weight: bold;
            width: 120px;
        }
        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 25px 0;
            color: #1e40af;
            text-transform: uppercase;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
            margin-bottom: 8px;
            color: #1565c0;
            font-size: 12px;
        }
        .section-content {
            background-color: #fff;
            border: 1px solid #90caf9;
            padding: 12px;
            min-height: 60px;
            font-size: 11px;
            line-height: 1.6;
        }
        .footer {
            margin-top: 40px;
            text-align: right;
        }
        .signature {
            margin-top: 50px;
            text-align: right;
        }
        .signature-line {
            margin-top: 10px;
            font-size: 10px;
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

        <!-- Informations Patient -->
        <div class="patient-info">
            <table>
                <tr>
                    <td>Patient :</td>
                    <td>{{ $consultation->patient->first_name }} {{ $consultation->patient->last_name }}</td>
                    <td>Médecin :</td>
                    <td>{{ $consultation->medecin->nom_complet_avec_prenom }}</td>
                </tr>
                <tr>
                    <td>Téléphone :</td>
                    <td>{{ $consultation->patient->phone ?? 'N/A' }}</td>
                    <td>Spécialité :</td>
                    <td>{{ $consultation->medecin->specialite ?? 'Médecin' }}</td>
                </tr>
                <tr>
                    <td>Âge :</td>
                    <td>{{ $consultation->patient->age ?? 'N/A' }}</td>
                    <td>Date :</td>
                    <td>{{ $consultation->date_consultation->format('d/m/Y H:i') }}</td>
                </tr>
            </table>
        </div>

        <!-- Titre -->
        <div class="title">Rapport d'Observation</div>

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

        <!-- Signature -->
        <div class="signature">
            <div class="signature-line">
                <strong>Signature / Cachet du Médecin</strong>
            </div>
        </div>
    </div>
</body>
</html>

