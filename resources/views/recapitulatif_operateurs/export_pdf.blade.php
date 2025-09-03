<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Export PDF - Récapitulatif des opérateurs</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
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
                <th>Examen</th>
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
                <td>{{ $loop->iteration }}</td>
                <td>{{ $recap->medecin->nom ?? '—' }}</td>
                <td>{{ $recap->examen->nom ?? '—' }}</td>
                <td>{{ $recap->nombre }}</td>
                <td>{{ number_format($recap->tarif, 0, ',', ' ') }}</td>
                <td>{{ number_format($recap->recettes, 0, ',', ' ') }}</td>
                <td>{{ number_format($recap->part_medecin, 0, ',', ' ') }}</td>
                <td>{{ number_format($recap->part_clinique, 0, ',', ' ') }}</td>
                <td>{{ \Carbon\Carbon::parse($recap->jour)->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
