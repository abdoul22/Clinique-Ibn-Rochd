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
            background-color: #fff;
            color: #000;
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
            color: #000;
        }

        th {
            background-color: #f3f3f3;
            color: #000;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .no-print {
            display: block;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                margin: 0;
                background-color: #fff;
                color: #000;
            }

        th {
            background-color: #f3f3f3 !important;
            color: #000 !important;
        }

        td {
            color: #000 !important;
        }

        th:nth-child(2),
        td:nth-child(2) {
            font-weight: bold;
            font-size: 14px;
            background-color: #e8f4f8 !important;
        }
        }

    </style>
</head>

<body>

    <div class="no-print" style="margin-bottom: 20px;">
        <a href="{{ route(auth()->user()->role->name . '.examens.index') }}" 
           style="display: inline-block; background: #6b7280; color: white; padding: 8px 16px; border-radius: 6px; text-decoration: none; margin-right: 10px;">
            ← Retour
        </a>
        <button onclick="window.print()" style="background: #3b82f6; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer;">
            🖨️ Imprimer
        </button>
    </div>

    <h2>Liste des examens</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Tarif (MRU)</th>
                <th>Part Médecins (MRU)</th>
                <th>Part Clinique (MRU)</th>
                <th>Service</th>
            </tr>
        </thead>
        <tbody>
            @foreach($examens as $examen)
            <tr>
                <td>{{ $examen->id }}</td>
                <td>{{ $examen->nom_affichage }}</td>
                <td class="text-right">{{ number_format($examen->tarif, 0, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($examen->part_medecin, 0, ',', ' ') }}</td>
                <td class="text-right">{{ number_format($examen->tarif - $examen->part_medecin, 0, ',', ' ') }}</td>
                <td>{{ $examen->service_affichage }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
