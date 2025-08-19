<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Facture IBN ROCHD</title>
    <style>
        :root {
            --fs-xs: 10px;
            --fs-s: 11px;
            --fs-m: 12px;
            --fs-l: 18px;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #000;
            background: #fff;
        }

        .sheet {
            width: 148mm;
            min-height: 210mm;
            margin: 0 auto;
            padding: 8mm;
            background: #fff;
            color: #000;
            font-family: DejaVu Sans, Arial, sans-serif;
            line-height: 1.2;
            box-sizing: border-box;
            font-size: var(--fs-s);
        }

        .header {
            display: block;
            text-align: center;
            margin-bottom: 15px;
        }

        .header-row {
            display: block;
            margin-bottom: 8px;
        }

        .fr {
            text-align: left;
            font-size: var(--fs-s);
            margin-bottom: 8px;
        }

        .logo-container {
            text-align: center;
            margin: 12px 0;
        }

        .logo-text {
            font-size: var(--fs-l);
            font-weight: bold;
            text-align: center;
            margin: 8px 0;
        }

        .muted {
            color: #555;
            font-size: var(--fs-xs);
        }

        .divider {
            border-bottom: 1px dashed #000;
            margin: 8px 0;
        }

        .label {
            display: inline-block;
            width: 45%;
            font-size: var(--fs-s);
        }

        .value {
            font-weight: 600;
            font-size: var(--fs-s);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .table td,
        .table th {
            padding: 4px 2px;
            font-size: var(--fs-s);
            border-bottom: 1px solid #ddd;
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: 700;
        }

        .center {
            text-align: center;
        }

        .receipt-title {
            text-align: center;
            font-size: var(--fs-s);
            margin-bottom: 8px;
        }

        @page {
            size: A5 portrait;
            margin: 6mm;
        }
    </style>
</head>

<body>
    <div class="sheet">
        <!-- En-tête -->
        <div class="header">
            <div class="header-row">
                <div class="bold" style="font-size: var(--fs-m);">CENTRE IBN ROCHD</div>
                <div>Dr Brahim Ould Ntaghry</div>
                <div>Spécialiste en Imagerie Médicale</div>
            </div>
            <div class="muted">
                Centre Imagerie Médicale<br>
                Scanner – Echographie – Radiologie Générale – Mammographie – Panoramique Dentaire
            </div>
        </div>

        <!-- Logo centré -->
        <div class="logo-text">IBN ROCHD</div>

        <div class="muted center">
            Urgences Tél. 26 38 24 84 – 22 30 56 26<br>
            Avenue John Kennedy, en face de la Polyclinique – Nouakchott
        </div>

        <div class="divider"></div>

        <div class="receipt-title">
            RECU N° <span class="bold">{{ $caisse->numero_facture ?? $caisse->id }}</span>
        </div>

        <!-- Informations patient -->
        <div style="font-size: var(--fs-s); margin-bottom: 10px;">
            <div><span class="label">Numéro d'entrée</span> : <span class="value">{{ $caisse->numero_entre ?? '1'
                    }}</span></div>
            <div><span class="label">Nom du patient</span> : <span class="value">{{ ($caisse->patient->first_name ?? '')
                    . ' ' . ($caisse->patient->last_name ?? '') }}</span></div>
            <div><span class="label">Adresse / Tel</span> : <span class="value">{{ $caisse->patient->phone ?? 'N/A'
                    }}</span></div>
            <div><span class="label">Prescripteur</span> : <span class="value">{{ $caisse->prescripteur->nom ??
                    'Externe' }}</span></div>
            <div><span class="label">Examinateur</span> : <span class="value">{{ $caisse->medecin->nom ?? 'N/A'
                    }}</span></div>
            <div><span class="label">Date de l'examen</span> :
                <span class="value">
                    {{ $caisse->date_examen ? \Carbon\Carbon::parse($caisse->date_examen)->format('d/m/Y H:i') :
                    \Carbon\Carbon::now()->format('d/m/Y H:i') }}
                </span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Examens -->
        <div class="bold" style="font-size: var(--fs-s); margin-bottom: 6px;">Examens demandés</div>
        <table class="table">
            <tbody>
                @if($caisse->examens_data)
                @php
                $examensData = json_decode($caisse->examens_data, true);
                @endphp
                @foreach($examensData as $examenData)
                <tr>
                    <td>{{ $examenData['nom'] ?? 'N/A' }}</td>
                    <td class="right">{{ number_format($examenData['total'] ?? 0, 0) }}</td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td>{{ $caisse->examen->nom ?? 'Consultation' }}</td>
                    <td class="right">{{ number_format($caisse->total, 0) }}</td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr style="border-top: 2px solid #000;">
                    <th class="right bold">Total</th>
                    <th class="right bold">{{ number_format($caisse->total, 0) }} MRU</th>
                </tr>
            </tfoot>
        </table>

        <div class="divider"></div>

        <div style="font-size: var(--fs-s);">
            <span class="label">Caissier(e)</span> : <span class="value">{{ $caisse->nom_caissier ?? 'N/A' }}</span>
        </div>

        <div style="margin-top: 15px; text-align: center; font-size: var(--fs-xs); color: #666;">
            Merci de votre visite
        </div>
    </div>
</body>

</html>