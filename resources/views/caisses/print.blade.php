<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Facture IBN ROCHD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        :root {
            --fs-xs: 11px;
            --fs-s: 12px;
            --fs-m: 13px;
            --fs-l: 22px;
        }

        .sheet {
            width: 148mm;
            min-height: 210mm;
            margin: 0 auto;
            padding: 10mm;
            background: #fff;
            color: #000;
            font-family: system-ui, Arial, "Helvetica Neue", sans-serif;
            line-height: 1.3;
            box-sizing: border-box;
        }

        .header {
            display: flex;
            justify-content: space-between;
        }

        .fr {
            text-align: left;
            font-size: var(--fs-s);
        }

        .ar {
            text-align: right;
            direction: rtl;
            font-size: var(--fs-s);
        }

        .big {
            font-size: var(--fs-l);
            font-weight: 700;
            text-align: center;
        }

        .logo-container {
            text-align: center;
            margin: 8px 0;
        }

        .logo-container img {
            height: 50px;
            width: auto;
        }

        .muted {
            color: #444;
            font-size: var(--fs-xs);
        }

        .divider {
            border-bottom: 1px dashed #000;
            margin: 6px 0;
        }

        .label {
            display: inline-block;
            width: 45%;
        }

        .value {
            font-weight: 600;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        .table td,
        .table th {
            padding: 4px 0;
            font-size: var(--fs-m);
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: 700;
        }

        .print-button {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 8px 16px;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            z-index: 1000;
        }

        .print-button:hover {
            background: #059669;
        }

        @media print {
            @page {
                size: A5 portrait;
                margin: 8mm;
            }

            body {
                margin: 0;
            }

            .sheet {
                width: auto;
                min-height: auto;
                padding: 0;
            }

            .print-button {
                display: none !important;
            }
        }

        @media screen {
            .print-button {
                display: block;
            }
        }
    </style>
</head>

<body>
    <button class="print-button" onclick="window.print()">Imprimer</button>

    <div class="sheet">

        <!-- En-tête bilingue -->
        <div class="header">
            <div class="fr">
                <div>CENTRE IBN ROCHD</div>
                <div>Dr Brahim Ould Ntaghry</div>
                <div>Spécialiste en Imagerie Médicale</div>
                <div class="muted">Centre Imagerie Médicale</div>
                <div class="muted">Scanner – Echographie – Radiologie Générale – Mammographie – Panoramique Dentaire
                </div>
            </div>
            <div class="logo-container">
                <img src="{{ asset('images/logo.png') }}" alt="Logo IBN ROCHD">
            </div>
            <div class="ar">
                <div>مركز ابن رشد</div>
                <div>الدكتور إبراهيم ولد نْتَغري</div>
                <div>اختصاصي في التشخيص الطبي والأشعة</div>
                <div class="muted">مركز التشخيص الطبي</div>
                <div class="muted">فحص بالأشعة – تصوير بالموجات فوق الصوتية – أشعة عامة – تصوير الثدي – أشعة الأسنان
                    البانورامية</div>
            </div>
        </div>



        <div class="muted" style="text-align:center;">
            Urgences Tél. 26 38 24 84 – 22 30 56 26 <br>
            Avenue John Kennedy, en face de la Polyclinique – Nouakchott
        </div>

        <div class="divider"></div>
        <div style="text-align:center; font-size: var(--fs-s); margin-bottom:6px;">
            RECU N° <span class="bold">{{ $caisse->numero_facture ?? $caisse->id }}</span>
        </div>

        <!-- Infos patient -->
        <div style="font-size: var(--fs-s);">
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
                    {{ $caisse->date_examen ? \Carbon\Carbon::parse($caisse->date_examen)->format('d/m/Y') . ' ' .
                    \Carbon\Carbon::now()->format('H:i') :
                    \Carbon\Carbon::now()->format('d/m/Y H:i') }}
                </span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Examens -->
        <div class="bold" style="font-size: var(--fs-s); margin-bottom:4px;">Examens demandés</div>
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
                <tr>
                    <th class="right">Total</th>
                    <th class="right">{{ number_format($caisse->total, 0) }}</th>
                </tr>
            </tfoot>
        </table>

        <div class="divider"></div>
        <div style="font-size: var(--fs-s);">
            <span class="label">Caissier(e)</span> : <span class="value">{{ $caisse->nom_caissier ?? 'N/A' }}</span>
        </div>
    </div>
</body>

</html>
</html>
