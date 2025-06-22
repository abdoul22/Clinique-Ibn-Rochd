<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des examens</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>

    <h2>Liste des examens</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Service</th>
                <th>Tarif (MRU)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($examens as $examen)
            <tr>
                <td>{{ $examen->id }}</td>
                <td>{{ $examen->nom }}</td>
                <td>{{ $examen->service->nom ?? 'N/A' }}</td>
                <td class="text-right">{{ number_format($examen->tarif, 0, ',', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
