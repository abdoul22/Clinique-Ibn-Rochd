<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Export PDF - Assurances</title>
    <style>
        body {
            font-family: sans-serif;
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
            background-color: #eee;
        }
    </style>
</head>

<body>
    <h2>Liste des assurances</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Crédit Assurance (MRU)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assurances as $assurance)
            @php
                $creditAssurance = \App\Models\Caisse::where('assurance_id', $assurance->id)
                    ->where('couverture', '>', 0)
                    ->get()
                    ->sum(function($caisse) {
                        return $caisse->total * ($caisse->couverture / 100);
                    });
            @endphp
            <tr>
                <td>{{ $assurance->id }}</td>
                <td>{{ $assurance->nom }}</td>
                <td>{{ number_format($creditAssurance, 0, ',', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
