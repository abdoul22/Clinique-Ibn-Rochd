<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Prescripteurs PDF</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px;
        }

        th {
            background: #eee;
        }
    </style>
</head>

<body>
    <h2>Liste des prescripteurs</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Spécialité</th>
            </tr>
        </thead>
        <tbody>
            @foreach($prescripteurs as $prescripteur)
            <tr>
                <td>{{ $prescripteur->id }}</td>
                <td>{{ $prescripteur->nom }}</td>
                <td>{{ $prescripteur->specialite }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
