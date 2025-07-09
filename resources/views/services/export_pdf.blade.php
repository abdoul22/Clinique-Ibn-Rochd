<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des Services</title>
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
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        h2 {
            text-align: center;
        }
    </style>
</head>

<body>
    <h2>Liste des Services</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Observation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($services as $index => $service)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $service->nom_affichage }}</td>
                <td>{{ $service->observation_affichage }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
