<!-- resources/views/etatcaisse/print.blade.php -->
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Impression - État de caisse</title>
    <style>
        body {
            font-family: sans-serif;
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
            background-color: #f2f2f2;
        }

        .title {
            text-align: center;
            font-size: 20px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="title">État de caisse - Impression</div>

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
            @foreach($etatcaisses as $etat)
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
