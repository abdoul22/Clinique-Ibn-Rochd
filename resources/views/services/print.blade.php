<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Impression - Services</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        @media print {
            button {
                display: none;
            }
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
            @foreach ($services as $index => $service)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $service->nom }}</td>
                <td>{{ $service->observation }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()">Imprimer</button>
    </div>
</body>

</html>
