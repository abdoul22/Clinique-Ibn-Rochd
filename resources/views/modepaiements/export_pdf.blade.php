<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modes de Paiement - Récapitulatif</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            margin: 20mm;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            color: #000;
            background: white;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #333;
            padding-bottom: 12px;
        }

        .header h1 {
            font-size: 22px;
            margin-bottom: 4px;
        }

        .header p {
            font-size: 13px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 13px;
            font-weight: bold;
        }

        th:nth-child(2),
        th:nth-child(3),
        th:nth-child(4) {
            text-align: right;
        }

        td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
            font-size: 12px;
        }

        td:nth-child(2),
        td:nth-child(3),
        td:nth-child(4) {
            text-align: right;
            font-weight: 600;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .total-row {
            background-color: #e8f5e9 !important;
            font-weight: bold;
            font-size: 13px;
        }

        .total-row td {
            padding: 12px 10px;
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
        }

        .positive {
            color: #2e7d32;
        }

        .negative {
            color: #c62828;
        }

        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 11px;
            color: #666;
            padding-top: 12px;
            border-top: 2px solid #eee;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('clinique.name', 'Clinique') }}</h1>
        <p>Récapitulatif des Modes de Paiement</p>
        <p style="font-size: 11px; margin-top: 4px;">Date d'impression: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30%;">Mode de Paiement</th>
                <th style="width: 25%;">Entrées (MRU)</th>
                <th style="width: 25%;">Sorties (MRU)</th>
                <th style="width: 20%;">Solde (MRU)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td><strong>{{ $item['mode'] }}</strong></td>
                <td class="positive">{{ number_format($item['entree'], 0, ',', ' ') }}</td>
                <td class="negative">{{ number_format($item['sortie'], 0, ',', ' ') }}</td>
                <td class="{{ $item['solde'] >= 0 ? 'positive' : 'negative' }}">
                    {{ number_format($item['solde'], 0, ',', ' ') }}
                </td>
            </tr>
            @endforeach
            
            <tr class="total-row">
                <td><strong>TOTAL GÉNÉRAL</strong></td>
                <td class="positive">{{ number_format(array_sum(array_column($data, 'entree')), 0, ',', ' ') }}</td>
                <td class="negative">{{ number_format(array_sum(array_column($data, 'sortie')), 0, ',', ' ') }}</td>
                <td class="{{ $totalGlobal >= 0 ? 'positive' : 'negative' }}">
                    {{ number_format($totalGlobal, 0, ',', ' ') }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>{{ config('clinique.name', 'Clinique') }} - {{ config('clinique.address', 'Nouakchott, Mauritanie') }}</p>
        <p>Tél: {{ config('clinique.phone', 'N/A') }} - Email: {{ config('clinique.email', 'N/A') }}</p>
    </div>
</body>
</html>






