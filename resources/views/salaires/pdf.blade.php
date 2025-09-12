<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h1 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        th {
            background: #f5f5f5;
        }

        .highlight {
            background: #fff7d6;
        }
    </style>
    <title>Salaires {{ str_pad($month,2,'0',STR_PAD_LEFT) }}/{{ $year }}</title>
</head>

<body>
    <h1>Salaires du mois {{ str_pad($month,2,'0',STR_PAD_LEFT) }}/{{ $year }}</h1>
    <table>
        <thead>
            <tr>
                <th>Personnel</th>
                <th>Fonction</th>
                <th>Salaire brut</th>
                <th>Crédit déduit ce mois</th>
                <th>Net à payer</th>
            </tr>
        </thead>
        <tbody>
            @foreach($personnels as $p)
            <tr class="{{ $p['credit_deduit_ce_mois']>0 ? 'highlight' : '' }}">
                <td>{{ $p['nom'] }}</td>
                <td>{{ $p['fonction'] ?? '—' }}</td>
                <td>{{ number_format($p['salaire'], 0, ',', ' ') }} MRU</td>
                <td>{{ number_format($p['credit_deduit_ce_mois'], 0, ',', ' ') }} MRU</td>
                <td>{{ number_format($p['net_a_payer'], 0, ',', ' ') }} MRU</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
