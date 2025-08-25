<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Export PDF - État de Caisse</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .period-info {
            font-size: 14px;
            color: #666;
            margin-bottom: 15px;
        }

        .summary {
            background-color: #f8f9fa;
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 15px;
        }

        .summary-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
            text-align: center;
        }

        .summary-grid {
            width: 100%;
        }

        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 3px;
        }

        .summary-cell {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 2px;
        }

        .summary-label {
            font-weight: bold;
            font-size: 10px;
        }

        .summary-value {
            font-size: 12px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
            font-size: 10px;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 15px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="title">Rapport - État de Caisse</div>
        @if(isset($periodDescription))
        <div class="period-info">{{ $periodDescription }}</div>
        @endif
        <div class="period-info">Généré le {{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }}</div>
    </div>

    @if(isset($resumeFiltre))
    <div class="summary">
        <div class="summary-title">Résumé de la période</div>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell">
                    <div class="summary-label">Recette Caisse</div>
                    <div class="summary-value">{{ number_format($resumeFiltre['recette'], 0, ',', ' ') }} MRU</div>
                </div>
                <div class="summary-cell">
                    <div class="summary-label">Part Médecin</div>
                    <div class="summary-value">{{ number_format($resumeFiltre['part_medecin'], 0, ',', ' ') }} MRU</div>
                </div>
                <div class="summary-cell">
                    <div class="summary-label">Part Clinique</div>
                    <div class="summary-value">{{ number_format($resumeFiltre['part_cabinet'], 0, ',', ' ') }} MRU</div>
                </div>
            </div>
            <div class="summary-row">
                <div class="summary-cell">
                    <div class="summary-label">Dépense</div>
                    <div class="summary-value">{{ number_format($resumeFiltre['depense'], 0, ',', ' ') }} MRU</div>
                </div>
                <div class="summary-cell">
                    <div class="summary-label">Crédit Personnel</div>
                    <div class="summary-value">{{ number_format($resumeFiltre['credit_personnel'], 0, ',', ' ') }} MRU
                    </div>
                </div>
                <div class="summary-cell">
                    <div class="summary-label">Crédit Assurance</div>
                    <div class="summary-value">{{ number_format($resumeFiltre['credit_assurance'], 0, ',', ' ') }} MRU
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($etatcaisses->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">ID</th>
                <th style="width: 25%;">Désignation</th>
                <th style="width: 12%;">Recette</th>
                <th style="width: 12%;">Part Médecin</th>
                <th style="width: 12%;">Part Clinique</th>
                <th style="width: 11%;">Validation</th>
                <th style="width: 10%;">Assurance</th>
                <th style="width: 10%;">Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($etatcaisses as $etat)
            <tr>
                <td class="text-center">{{ $etat->id }}</td>
                <td>{{ $etat->designation }}</td>
                <td class="text-right">{{ number_format($etat->recette, 0, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($etat->part_medecin, 0, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($etat->part_clinique, 0, ',', ' ') }}</td>
                <td class="text-center">
                    @if($etat->validated)
                    Validé
                    @else
                    Non validé
                    @endif
                </td>
                <td>{{ $etat->assurance?->nom ?? '—' }}</td>
                <td class="text-center">{{ $etat->created_at ? $etat->created_at->format('d/m/Y') : '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 30px; color: #666;">
        <p>Aucune donnée trouvée pour la période sélectionnée.</p>
    </div>
    @endif

    <div class="footer">
        Total des entrées: {{ $etatcaisses->count() }}
    </div>
</body>

</html>
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
            font-size: 10px;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 15px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="title">Rapport - État de Caisse</div>
        @if(isset($periodDescription))
        <div class="period-info">{{ $periodDescription }}</div>
        @endif
        <div class="period-info">Généré le {{ \Carbon\Carbon::now()->format('d/m/Y à H:i') }}</div>
    </div>

    @if(isset($resumeFiltre))
    <div class="summary">
        <div class="summary-title">Résumé de la période</div>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell">
                    <div class="summary-label">Recette Caisse</div>
                    <div class="summary-value">{{ number_format($resumeFiltre['recette'], 0, ',', ' ') }} MRU</div>
                </div>
                <div class="summary-cell">
                    <div class="summary-label">Part Médecin</div>
                    <div class="summary-value">{{ number_format($resumeFiltre['part_medecin'], 0, ',', ' ') }} MRU</div>
                </div>
                <div class="summary-cell">
                    <div class="summary-label">Part Clinique</div>
                    <div class="summary-value">{{ number_format($resumeFiltre['part_cabinet'], 0, ',', ' ') }} MRU</div>
                </div>
            </div>
            <div class="summary-row">
                <div class="summary-cell">
                    <div class="summary-label">Dépense</div>
                    <div class="summary-value">{{ number_format($resumeFiltre['depense'], 0, ',', ' ') }} MRU</div>
                </div>
                <div class="summary-cell">
                    <div class="summary-label">Crédit Personnel</div>
                    <div class="summary-value">{{ number_format($resumeFiltre['credit_personnel'], 0, ',', ' ') }} MRU
                    </div>
                </div>
                <div class="summary-cell">
                    <div class="summary-label">Crédit Assurance</div>
                    <div class="summary-value">{{ number_format($resumeFiltre['credit_assurance'], 0, ',', ' ') }} MRU
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($etatcaisses->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">ID</th>
                <th style="width: 25%;">Désignation</th>
                <th style="width: 12%;">Recette</th>
                <th style="width: 12%;">Part Médecin</th>
                <th style="width: 12%;">Part Clinique</th>
                <th style="width: 11%;">Validation</th>
                <th style="width: 10%;">Assurance</th>
                <th style="width: 10%;">Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($etatcaisses as $etat)
            <tr>
                <td class="text-center">{{ $etat->id }}</td>
                <td>{{ $etat->designation }}</td>
                <td class="text-right">{{ number_format($etat->recette, 0, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($etat->part_medecin, 0, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($etat->part_clinique, 0, ',', ' ') }}</td>
                <td class="text-center">
                    @if($etat->validated)
                    Validé
                    @else
                    Non validé
                    @endif
                </td>
                <td>{{ $etat->assurance?->nom ?? '—' }}</td>
                <td class="text-center">{{ $etat->created_at ? $etat->created_at->format('d/m/Y') : '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 30px; color: #666;">
        <p>Aucune donnée trouvée pour la période sélectionnée.</p>
    </div>
    @endif

    <div class="footer">
        Total des entrées: {{ $etatcaisses->count() }}
    </div>
</body>

</html>
