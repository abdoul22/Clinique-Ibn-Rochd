<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Impression - Récapitulatif des opérateurs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f9f9f9;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <h2>Récapitulatif des opérateurs</h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Médecin</th>
                <th>Service</th>
                <th>Nombre</th>
                <th>Tarif</th>
                <th>Recettes</th>
                <th>Part médecin</th>
                <th>Part clinique</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recaps as $recap)
            <tr>
                <td>{{ $recap->id }}</td>
                <td>{{ $recap->medecin->nom ?? '—' }}</td>
                <td>{{ $recap->service->nom ?? '—' }}</td>
                <td>{{ $recap->nombre }}</td>
                <td>{{ number_format($recap->tarif, 0, ',', ' ') }}</td>
                <td>{{ number_format($recap->recettes, 0, ',', ' ') }}</td>
                <td>{{ number_format($recap->part_medecin, 0, ',', ' ') }}</td>
                <td>{{ number_format($recap->part_clinique, 0, ',', ' ') }}</td>
                <td>{{ $recap->date }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="no-print" style="margin-top: 20px;">
        <button onclick="window.print()">Imprimer</button>
    </div>
</body>

</html>
