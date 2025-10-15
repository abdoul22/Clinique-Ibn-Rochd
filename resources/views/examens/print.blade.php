<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Impression - Examens</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f3f3f3;
        }

        .text-right {
            text-align: right;
        }

        @media print {
            button {
                display: none;
            }

            body {
                margin: 0;
            }
        }
    </style>
</head>

<body>

    <button onclick="window.print()" style="margin-bottom: 20px; padding: 8px 16px;">Imprimer</button>

    <h2>Liste des examens</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Service</th>
                <th>Tarif (MRU)</th>
                <th>Part Médecins (MRU)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($examens as $examen)
            <tr>
                <td>{{ $examen->id }}</td>
                <td>{{ $examen->nom_affichage }}</td>
                <td>{{ $examen->service_affichage }}</td>
                <td class="text-right">{{ number_format($examen->tarif, 0, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($examen->part_medecin, 0, ',', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
