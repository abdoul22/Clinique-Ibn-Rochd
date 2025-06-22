<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Export PDF - État de Caisse</title>
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
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <h2>Rapport - État de Caisse</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Désignation</th>
                <th>Recette</th>
                <th>Part Médecin</th>
                <th>Part Clinique</th>
                <th>Dépense</th>
                <th>Personnel</th>
                <th>Assurance</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($etatcaisses as $etat)
            <tr>
                <td>{{ $etat->id }}</td>
                <td>{{ $etat->designation }}</td>
                <td>{{ number_format($etat->recette, 0, ',', ' ') }} MRU</td>
                <td>{{ number_format($etat->part_medecin, 0, ',', ' ') }} MRU</td>
                <td>{{ number_format($etat->part_clinique, 0, ',', ' ') }} MRU</td>
                <td>{{ number_format($etat->depense, 0, ',', ' ') }} MRU</td>
                <td>{{ $etat->personnel?->nom ?? '—' }}</td>
                <td>{{ $etat->assurance?->nom ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
