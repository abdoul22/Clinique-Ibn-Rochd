<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modes de Paiement - Récapitulatif</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            padding: 20px;
            background: white;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 14px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background-color: #333;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 14px;
            font-weight: bold;
        }

        th:nth-child(2),
        th:nth-child(3),
        th:nth-child(4) {
            text-align: right;
        }

        td {
            padding: 10px 12px;
            border-bottom: 1px solid #ddd;
            font-size: 13px;
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

        tr:hover {
            background-color: #f0f0f0;
        }

        .total-row {
            background-color: #e8f5e9 !important;
            font-weight: bold;
            font-size: 14px;
        }

        .total-row td {
            padding: 15px 12px;
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
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
            padding-top: 15px;
            border-top: 2px solid #eee;
        }

        .btn-container {
            margin-bottom: 20px;
            text-align: right;
        }

        .btn {
            padding: 10px 20px;
            margin-left: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background-color: #3b82f6;
            color: white;
        }

        .btn-secondary {
            background-color: #6b7280;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }

        @media print {
            .btn-container {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="btn-container">
        <a href="{{ route('mode-paiements.index') }}" class="btn btn-secondary">← Retour</a>
        <button onclick="window.print()" class="btn btn-primary">🖨 Imprimer</button>
    </div>

    <div class="header">
        <h1>{{ config('clinique.name', 'Clinique') }}</h1>
        <p>Récapitulatif des Modes de Paiement</p>
        <p style="font-size: 12px; margin-top: 5px;">Date d'impression: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Mode de Paiement</th>
                <th>Entrées (MRU)</th>
                <th>Sorties (MRU)</th>
                <th>Solde (MRU)</th>
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






