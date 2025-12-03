<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>Facture Hospitalisation {{ config('clinique.name') }}</title>
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
                <div>{{ config('clinique.name') }}</div>
                <div>{{ config('clinique.director_name') }}</div>
                <div>{{ config('clinique.director_specialty') }}</div>
                <div class="muted">{{ config('clinique.center_type') }}</div>
                <div class="muted">{{ config('clinique.services_description') }}</div>
            </div>
            <div class="logo-container">
                <img src="{{ asset(config('clinique.logo_path')) }}" alt="Logo {{ config('clinique.name') }}">
            </div>
            <div class="ar">
                <div>{{ config('clinique.name_ar') }}</div>
                <div>{{ config('clinique.director_name_ar') }}</div>
                <div>{{ config('clinique.director_specialty_ar') }}</div>
                <div class="muted">{{ config('clinique.center_type_ar') }}</div>
                <div class="muted">{{ config('clinique.services_description_ar') }}</div>
            </div>
        </div>



        <div class="muted" style="text-align:center; margin: 1mm 0;">
            Urgences Tél. 26 38 24 84 – 22 30 56 26 <br>
            Avenue John Kennedy, en face de la Polyclinique – Nouakchott
        </div>

        <div class="divider"></div>
        <div style="text-align:center; font-size: var(--fs-s); margin-bottom:2mm;">
            FACTURE HOSPITALISATION N° <span class="bold">{{ $hospitalisation->id }}</span>
        </div>

        <!-- Infos patient -->
        <div style="font-size: var(--fs-s); line-height: 1.2;">
            <div style="margin-bottom: 1mm;"><span class="label">Nom du patient</span> : <span class="value">{{
                    $hospitalisation->patient->nom ?? '' }} {{ $hospitalisation->patient->prenom ?? '' }}</span></div>
            <div style="margin-bottom: 1mm;"><span class="label">Téléphone</span> : <span class="value">{{
                    $hospitalisation->patient->telephone ?? 'N/A' }}</span></div>
            <div style="margin-bottom: 1mm;"><span class="label">Médecin traitant</span> : <span class="value">
                    @if($hospitalisation->medecin)
                    {{ $hospitalisation->medecin->nom_complet_avec_prenom }}
                    @else
                    N/A
                    @endif
                </span></div>
            <div style="margin-bottom: 1mm;"><span class="label">Service</span> : <span class="value">{{
                    $hospitalisation->service->nom ?? 'N/A' }}</span></div>
            @if($hospitalisation->lit && $hospitalisation->lit->chambre)
            <div style="margin-bottom: 1mm;"><span class="label">Chambre</span> : <span class="value">{{
                    $hospitalisation->lit->chambre->nom }} - Lit {{ $hospitalisation->lit->numero }}</span></div>
            @endif
            <div style="margin-bottom: 1mm;"><span class="label">Date d'entrée</span> : <span class="value">{{
                    \Carbon\Carbon::parse($hospitalisation->date_entree)->format('d/m/Y') }}</span></div>
            @if($hospitalisation->date_sortie)
            <div style="margin-bottom: 1mm;"><span class="label">Date de sortie</span> : <span class="value">{{
                    \Carbon\Carbon::parse($hospitalisation->date_sortie)->format('d/m/Y') }}</span></div>
            @endif
        </div>

        <div class="divider"></div>

        <!-- Charges facturées -->
        <div class="bold" style="font-size: var(--fs-s); margin-bottom:2mm;">Charges facturées</div>
        <table class="table">
            <thead>
                <tr>
                    <th style="text-align: left;">Description</th>
                    <th style="text-align: center;">Qté</th>
                    <th style="text-align: right;">Montant</th>
                </tr>
            </thead>
            <tbody>
                @forelse($chargesFacturees as $charge)
                <tr>
                    <td>{{ $charge->description_snapshot }}</td>
                    <td style="text-align: center;">{{ $charge->quantity }}</td>
                    <td class="right">{{ number_format($charge->total_price, 0, ',', ' ') }} MRU</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align: center; font-style: italic;">Aucune charge facturée</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" class="right">Total</th>
                    <th class="right bold">{{ number_format($totalCharges, 0, ',', ' ') }} MRU</th>
                </tr>
            </tfoot>
        </table>

        <div class="divider"></div>
        <div style="font-size: var(--fs-s); margin-top: 2mm;">
            <span class="label">Date d'impression</span> : <span class="value">{{ \Carbon\Carbon::now()->format('d/m/Y
                H:i') }}</span>
        </div>

        @if($hospitalisation->observation)
        <div class="divider"></div>
        <div style="font-size: var(--fs-xs); margin-top: 2mm;">
            <div class="bold">Observations :</div>
            <div style="margin-top: 1mm;">{{ $hospitalisation->observation }}</div>
        </div>
        @endif
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
