<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des Prescripteurs</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            margin: 20mm;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            color: #000;
            background: white;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #333;
            padding-bottom: 12px;
        }

        .header h1 {
            font-size: 22px;
            margin-bottom: 4px;
        }

        .header p {
            font-size: 13px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 13px;
            font-weight: bold;
        }

        td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
            font-size: 12px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 11px;
            color: #666;
            padding-top: 12px;
            border-top: 2px solid #eee;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('clinique.name', 'Clinique') }}</h1>
        <p>Liste des Prescripteurs</p>
        <p style="font-size: 11px; margin-top: 4px;">Date d'impression: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%;">ID</th>
                <th style="width: 50%;">Nom</th>
                <th style="width: 40%;">Spécialité</th>
            </tr>
        </thead>
        <tbody>
            @forelse($prescripteurs as $prescripteur)
            <tr>
                <td>{{ $prescripteur->id }}</td>
                <td><strong>{{ $prescripteur->nom }}</strong></td>
                <td>{{ $prescripteur->specialite ?? 'Non spécifiée' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="text-align: center; padding: 25px; color: #999;">
                    Aucun prescripteur trouvé
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>{{ config('clinique.name', 'Clinique') }} - {{ config('clinique.address', 'Nouakchott, Mauritanie') }}</p>
        <p>Tél: {{ config('clinique.phone', 'N/A') }} - Email: {{ config('clinique.email', 'N/A') }}</p>
    </div>
</body>
</html>
