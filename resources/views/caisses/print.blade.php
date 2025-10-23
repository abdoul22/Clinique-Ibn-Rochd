<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Facture IBN ROCHD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        :root {
            --fs-xs: 9px;
            --fs-s: 10px;
            --fs-m: 11px;
            --fs-l: 14px;
        }

        /* Format A5 par défaut - Optimisé pour impression verticale */
        .sheet {
            width: 148mm;
            min-height: 210mm;
            margin: 0 auto;
            padding: 5mm;
            background: #fff;
            color: #000;
            font-family: system-ui, Arial, "Helvetica Neue", sans-serif;
            line-height: 1.2;
            box-sizing: border-box;
            font-size: var(--fs-s);
        }

        /* Format A4 - plus large */
        .sheet.a4 {
            width: 210mm;
            min-height: 297mm;
            padding: 15mm;
            line-height: 1.4;
            font-size: var(--fs-m);
        }

        /* Layout optimisé pour A5 vertical */
        .sheet .header {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-start;
            text-align: left;
            margin-bottom: 3mm;
        }

        .sheet .fr {
            text-align: left;
            font-size: var(--fs-xs);
            flex: 1;
            line-height: 1.1;
        }

        .sheet .ar {
            text-align: right;
            direction: rtl;
            font-size: var(--fs-xs);
            flex: 1;
            line-height: 1.1;
        }

        .sheet .logo-container {
            flex: 0 0 auto;
            margin: 0 3mm;
            text-align: center;
        }

        .sheet .logo-container img {
            height: 25mm;
            width: auto;
        }

        .sheet .muted {
            font-size: var(--fs-xs);
            line-height: 1.1;
        }

        .sheet .divider {
            margin: 2mm 0;
        }

        .sheet .table td,
        .sheet .table th {
            padding: 1mm 0;
            font-size: var(--fs-s);
        }

        /* Layout pour A4 (plus grand) */
        .sheet.a4 .header {
            flex-direction: row;
            justify-content: space-between;
            text-align: left;
            margin-bottom: 5mm;
        }

        .sheet.a4 .fr {
            text-align: left;
            font-size: var(--fs-s);
            line-height: 1.3;
        }

        .sheet.a4 .ar {
            text-align: right;
            direction: rtl;
            font-size: var(--fs-s);
            line-height: 1.3;
        }

        .sheet.a4 .logo-container {
            margin: 0 5mm;
        }

        .sheet.a4 .logo-container img {
            height: 35mm;
        }

        .sheet.a4 .muted {
            font-size: var(--fs-xs);
        }

        .sheet.a4 .divider {
            margin: 3mm 0;
        }

        .sheet.a4 .table td,
        .sheet.a4 .table th {
            padding: 2mm 0;
            font-size: var(--fs-m);
        }


        /* Styles de base supprimés - maintenant gérés par les classes spécifiques */

        .big {
            font-size: var(--fs-l);
            font-weight: 700;
            text-align: center;
        }

        .muted {
            color: #444;
            font-size: var(--fs-xs);
            line-height: 1.1;
        }

        .divider {
            border-bottom: 1px dashed #000;
            margin: 2mm 0;
        }

        .label {
            display: inline-block;
            width: 40%;
            font-size: var(--fs-s);
        }

        .value {
            font-weight: 600;
            font-size: var(--fs-s);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2mm;
        }

        .table td,
        .table th {
            padding: 1mm 0;
            font-size: var(--fs-s);
        }

        .right {
            text-align: right;
        }

        .bold {
            font-weight: 700;
        }

        .print-controls {
            position: fixed;
            top: 10px;
            right: 10px;
            display: flex;
            gap: 8px;
            z-index: 1000;
        }

        .print-button {
            padding: 8px 16px;
            background: #10b981;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
        }

        .print-button:hover {
            background: #059669;
        }

        .format-button {
            padding: 8px 12px;
            background: #6b7280;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
        }

        .format-button:hover {
            background: #4b5563;
        }

        .format-button.active {
            background: #3b82f6;
        }

        @media print {
            @page {
                size: A5 portrait;
                margin: 3mm;
            }

            @page.a4 {
                size: A4 portrait;
                margin: 8mm;
            }


            body {
                margin: 0;
                padding: 0;
            }

            .sheet {
                width: auto;
                min-height: auto;
                padding: 2mm;
                margin: 0;
                box-shadow: none;
            }

            .sheet.a4 {
                width: auto;
                min-height: auto;
                padding: 5mm;
                margin: 0;
            }


            .print-controls {
                display: none !important;
            }

            /* Optimisations spécifiques pour A5 en impression */
            .sheet .header {
                margin-bottom: 2mm;
            }

            .sheet .logo-container img {
                height: 20mm;
            }

            .sheet .divider {
                margin: 1.5mm 0;
            }

            .sheet .table td,
            .sheet .table th {
                padding: 0.5mm 0;
            }
        }

        @media screen {
            .print-controls {
                display: flex;
            }
        }
    </style>
</head>

<body>
    <div class="print-controls">
        <button class="format-button" onclick="setFormat('a4')">A4</button>
        <button class="format-button active" onclick="setFormat('a5')">A5</button>
        <button class="print-button" onclick="window.print()">Imprimer</button>
    </div>

    <div class="sheet a5" id="sheet">

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



        <div class="muted" style="text-align:center; margin: 1mm 0;">
            Urgences Tél. 43 45 54 23 – 22 30 56 26 <br>
            Avenue John Kennedy, en face de la Polyclinique – Nouakchott
        </div>

        <div class="divider"></div>
        <div style="text-align:center; font-size: var(--fs-s); margin-bottom:2mm;">
            RECU N° <span class="bold">{{ $caisse->numero_facture ?? $caisse->id }}</span>
        </div>

        <!-- Infos patient -->
        <div style="font-size: var(--fs-s); line-height: 1.2;">
            <div style="margin-bottom: 1mm;"><span class="label">Numéro d'entrée</span> : <span class="value">{{
                    $caisse->numero_entre ?? '1' }}</span></div>
            <div style="margin-bottom: 1mm;"><span class="label">Nom du patient</span> : <span class="value">{{
                    ($caisse->patient->first_name ?? '') . ' ' . ($caisse->patient->last_name ?? '') }}</span></div>
            <div style="margin-bottom: 1mm;"><span class="label">Adresse / Tel</span> : <span class="value">{{
                    $caisse->patient->phone ?? 'N/A' }}</span></div>
            <div style="margin-bottom: 1mm;"><span class="label">Prescripteur</span> : <span class="value">{{
                    $caisse->prescripteur->nom ?? 'Externe' }}</span></div>
            <div style="margin-bottom: 1mm;"><span class="label">Examinateur</span> : <span class="value">
                    @if($caisse->medecin)
                    {{ $caisse->medecin->nom_complet_avec_specialite }}
                    @else
                    N/A
                    @endif
                </span></div>
            <div style="margin-bottom: 1mm;"><span class="label">Date de l'examen</span> : <span class="value">{{
                    $caisse->date_examen ? \Carbon\Carbon::parse($caisse->date_examen)->format('d/m/Y') . ' ' .
                    \Carbon\Carbon::now()->format('H:i') : \Carbon\Carbon::now()->format('d/m/Y H:i') }}</span></div>
        </div>

        <div class="divider"></div>

        <!-- Examens -->
        <div class="bold" style="font-size: var(--fs-s); margin-bottom:2mm;">Examens demandés</div>
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
                @php
                $couverture = $caisse->couverture ?? 0;
                $montantAssurance = $caisse->assurance_id ? ($caisse->total * ($couverture / 100)) : 0;
                $montantPatient = $caisse->total - $montantAssurance;
                @endphp
                <tr>
                    <th class="right">Total</th>
                    <th class="right">{{ number_format($montantPatient, 0) }}</th>
                </tr>
                @if($caisse->assurance && $couverture > 0)
                <tr>
                    <td colspan="2" style="font-size: var(--fs-xs); text-align: center; color: #666;">
                        ({{ $couverture }}% pris en charge par {{ $caisse->assurance->nom }})
                    </td>
                </tr>
                @endif
            </tfoot>
        </table>

        <div class="divider"></div>
        <div style="font-size: var(--fs-s); margin-top: 2mm;">
            <span class="label">Caissier(e)</span> : <span class="value">{{ $caisse->nom_caissier ?? 'N/A' }}</span>
        </div>
    </div>

    <script>
        function setFormat(format) {
            const sheet = document.getElementById('sheet');
            const buttons = document.querySelectorAll('.format-button');

            // Retirer toutes les classes de format
            sheet.classList.remove('a4', 'a5');

            // Ajouter la nouvelle classe
            sheet.classList.add(format);

            // Mettre à jour les boutons actifs
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            // Mettre à jour la page pour l'impression
            const style = document.createElement('style');
            style.id = 'dynamic-print-style';

            // Supprimer l'ancien style s'il existe
            const oldStyle = document.getElementById('dynamic-print-style');
            if (oldStyle) {
                oldStyle.remove();
            }

            if (format === 'a5') {
                style.textContent = '@media print { @page { size: A5 portrait; margin: 3mm; } }';
            } else if (format === 'a4') {
                style.textContent = '@media print { @page { size: A4 portrait; margin: 8mm; } }';
            }

            document.head.appendChild(style);
        }
    </script>
</body>

</html>
